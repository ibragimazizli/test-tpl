<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Advanced API Security Middleware
 *
 * Provides multi-layered protection against:
 * - Automated bots (Selenium, Puppeteer, etc.)
 * - Third-party unauthorized access
 * - Data scraping and harvesting
 * - Brute force attacks
 * - Request flooding
 */
class ApiSecurityMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {

        // Skip if API security is disabled
        if (env('API_SECURITY_ENABLED', true) === false) {
            return $next($request);
        }

        // Layer 1: Request Signature Verification
        if (!$this->verifyRequestSignature($request)) {
            $this->logSecurityEvent('signature_failed', $request);
            return $this->securityResponse('SECURITY_INVALID_REQUEST_SIGNATURE', 403);
        }

        // Layer 2: Browser Fingerprint Validation
        if (!$this->validateBrowserFingerprint($request)) {
            $this->logSecurityEvent('fingerprint_failed', $request);
            return $this->securityResponse('SECURITY_INVALID_BROWSER_FINGERPRINT', 403);
        }

        // Layer 3: Advanced Rate Limiting
        if (!$this->checkRateLimit($request)) {
            $this->logSecurityEvent('rate_limit_exceeded', $request);

            $headers = [];
            if ($seconds = $request->attributes->get('security_retry_after')) {
                $headers['Retry-After'] = $seconds;
                $headers['X-RateLimit-Reset'] = now()->addSeconds($seconds)->timestamp;
            }

            return $this->securityResponse('SECURITY_TOO_MANY_REQUESTS', 429, $headers);
        }

        // Layer 4: IP Reputation Check
        if (!$this->checkIpReputation($request)) {
            $this->logSecurityEvent('ip_blocked', $request);
            return $this->securityResponse('SECURITY_ACCESS_DENIED', 403);
        }

        // Layer 5: Bot Detection (User-Agent & Behavior)
        if (!$this->detectBot($request)) {
            $this->logSecurityEvent('bot_detected', $request);
            return $this->securityResponse('SECURITY_AUTOMATED_ACCESS_DETECTED', 403);
        }

        // Layer 6: Request Pattern Analysis
        if (!$this->analyzeRequestPattern($request)) {
            $this->logSecurityEvent('suspicious_pattern', $request);
            return $this->securityResponse('SECURITY_SUSPICIOUS_ACTIVITY_DETECTED', 403);
        }

        // All checks passed - allow request
        $this->trackLegitimateRequest($request);

        return $next($request);
    }

    /**
     * Layer 1: Verify HMAC-based request signature
     */
    private function verifyRequestSignature(Request $request): bool
    {
        // Get signature from request
        $providedSignature = $request->header('X-Request-Signature');
        $timestamp = $request->header('X-Request-Timestamp');
        $nonce = $request->header('X-Request-Nonce');

        if (!$providedSignature || !$timestamp || !$nonce) {
            return false;
        }

        // Check timestamp (prevent replay attacks - 5 minute window)
        if (abs(time() - $timestamp) > 300) {
            return false;
        }

        // Check nonce uniqueness (prevent replay attacks)
        $nonceKey = "nonce:$nonce";
        if (Cache::has($nonceKey)) {
            return false;
        }
        Cache::put($nonceKey, true, now()->addMinutes(10));

        // Generate expected signature
        $payload = $this->getSignaturePayload($request, $timestamp, $nonce);
        $secret = env('API_SECURITY_SECRET', config('app.key'));

        // If secret has base64: prefix, decode it to get binary key
        if (str_starts_with($secret, 'base64:')) {
            $secret = base64_decode(substr($secret, 7));
        }

        // Use binary key directly for HMAC
        $expectedSignature = hash_hmac('sha256', $payload, $secret);

        // Compare signatures (timing-safe)
        return hash_equals($expectedSignature, $providedSignature);
    }

    /**
     * Generate signature payload
     */
    private function getSignaturePayload(Request $request, string $timestamp, string $nonce): string
    {
        $method = $request->method();
        $path = '/' . $request->path(); // Add leading slash to match JavaScript URL.pathname
        $body = $request->getContent();

        return implode('|', [$method, $path, $timestamp, $nonce, $body]);
    }

    /**
     * Layer 2: Validate browser fingerprint
     */
    private function validateBrowserFingerprint(Request $request): bool
    {
        $fingerprint = $request->header('X-Browser-Fingerprint');

        if (!$fingerprint) {
            return false;
        }

        // Validate fingerprint format (should be 64-char hex)
        if (!preg_match('/^[a-f0-9]{64}$/i', $fingerprint)) {
            return false;
        }

        // Check if fingerprint is not in blocklist
        $blockKey = "fingerprint:blocked:$fingerprint";
        if (Cache::has($blockKey)) {
            return false;
        }

        // Track fingerprint usage
        $this->trackFingerprint($request, $fingerprint);

        return true;
    }

    /**
     * Layer 3: Advanced multi-tier rate limiting
     *
     * Note: IP-based limiting is set high to accommodate corporate environments
     * where many users share the same public IP address. The primary rate limiting
     * is done via browser fingerprint which is unique per browser.
     */
    private function checkRateLimit(Request $request): bool
    {
        $ip = $this->getClientIp($request);
        $fingerprint = $request->header('X-Browser-Fingerprint');

        // Tier 1: Per-IP rate limiting (high limit for corporate networks)
        // Corporate offices may have 100+ users behind same IP
        $ipLimit = env('API_RATE_LIMIT_IP', 300); // requests per minute (was 60)
        if (!$this->checkLimit("rate:ip:$ip", $ipLimit, 60, $request)) {
            return false;
        }

        // Tier 2: Per-Fingerprint rate limiting (PRIMARY rate limiting)
        // Each browser has unique fingerprint, even behind same IP
        // This is the main defense against individual abuse
        $fingerprintLimit = env('API_RATE_LIMIT_FINGERPRINT', 30);
        if ($fingerprint && !$this->checkLimit("rate:fingerprint:$fingerprint", $fingerprintLimit, 60, $request)) {
            return false;
        }

        // Tier 3: Per-Endpoint rate limiting (per user/fingerprint, not per IP)
        // Changed from IP-based to fingerprint-based for corporate-friendliness
        $endpoint = $request->path();
        $endpointLimit = $this->getEndpointLimit($endpoint);
        $endpointKey = $fingerprint ?: $ip; // Fall back to IP if no fingerprint
        if (!$this->checkLimit("rate:endpoint:$endpointKey:$endpoint", $endpointLimit, 60, $request)) {
            return false;
        }

        // Tier 4: Global rate limiting (protection against DDoS)
        $globalLimit = env('API_RATE_LIMIT_GLOBAL', 1000);
        if (!$this->checkLimit("rate:global", $globalLimit, 60, $request)) {
            return false;
        }

        return true;
    }

    /**
     * Check rate limit for a specific key
     */
    private function checkLimit(string $key, int $limit, int $seconds, Request $request): bool
    {
        $data = Cache::get($key, ['count' => 0, 'expires_at' => 0]);

        // Handle potential integer legacy values (if cache not flushed)
        if (is_int($data)) {
            $data = ['count' => $data, 'expires_at' => now()->addSeconds($seconds)->timestamp];
        }

        $now = now()->timestamp;

        // Reset if expired (safety check in case Cache driver doesn't evict properly)
        if ($data['expires_at'] > 0 && $data['expires_at'] <= $now) {
            $data = ['count' => 0, 'expires_at' => 0];
        }

        if ($data['count'] >= $limit) {
            $retryAfter = max(0, $data['expires_at'] - $now);
            $request->attributes->set('security_retry_after', $retryAfter);
            $request->attributes->set('security_limit_reason', $key);
            $request->attributes->set('security_limit_count', $data['count']);
            return false;
        }

        if ($data['expires_at'] === 0) {
            $data['expires_at'] = $now + $seconds;
        }

        $data['count']++;

        $ttl = max(1, $data['expires_at'] - $now);

        Cache::put($key, $data, $ttl);
        return true;
    }

    /**
     * Get endpoint-specific rate limit
     *
     * Each endpoint can be individually configured via .env
     * Sensitive endpoints (register, pay) have lower defaults
     */
    private function getEndpointLimit(string $endpoint): int
    {
        $limits = [
            // Registration flow endpoints
            'callService/his/borderVehicleInfo' => (int) env('API_LIMIT_BORDER_VEHICLE_INFO', 10),
            'callService/migration/countries' => (int) env('API_LIMIT_MIGRATION_COUNTRIES', 20),
            'callService/his/getVehicleDetails' => (int) env('API_LIMIT_VEHICLE_DETAILS', 10),
            'callService/his/validateBorderVehiclePolicyStatus' => (int) env('API_LIMIT_VALIDATE_POLICY_STATUS', 10),
            'callService/his/calculateBorderInsuranceFee' => (int) env('API_LIMIT_CALCULATE_FEE', 10),
            'callService/his/getPersonDataForTpl' => (int) env('API_LIMIT_PERSON_DATA', 10),
            'callService/his/tplApiRegister' => (int) env('API_LIMIT_REGISTER', 5),

            // Payment endpoints
            'callService/payment/invoiceInfo' => (int) env('API_LIMIT_INVOICE_INFO', 10),
            'callService/payment/invoiceStatus' => (int) env('API_LIMIT_INVOICE_STATUS', 10),
            'callService/his/getPaymentCodes' => (int) env('API_LIMIT_PAYMENT_CODES', 10),
            'callService/payment/payInvoices' => (int) env('API_LIMIT_PAY_INVOICES', 5),

            // Index page endpoint
            'callService/his/getBorderVehiclePoliciesByVehicle' => (int) env('API_LIMIT_VEHICLE_POLICIES', 10),
        ];

        foreach ($limits as $pattern => $limit) {
            if (str_contains($endpoint, $pattern)) {
                return $limit;
            }
        }

        return (int) env('API_LIMIT_DEFAULT', 30);
    }

    /**
     * Layer 4: IP reputation and blocking
     */
    private function checkIpReputation(Request $request): bool
    {
        $ip = $this->getClientIp($request);

        // Check if IP is permanently blocked
        if (Cache::has("ip:blocked:permanent:$ip")) {
            return false;
        }

        // Check if IP is temporarily blocked
        if (Cache::has("ip:blocked:temp:$ip")) {
            return false;
        }

        // Check failure count
        $failureKey = "ip:failures:$ip";
        $failures = Cache::get($failureKey, 0);

        // Temporary block after 10 failures
        if ($failures >= 10) {
            Cache::put("ip:blocked:temp:$ip", true, now()->addHours(1));
            return false;
        }

        // Check if IP is from suspicious range
        if ($this->isSuspiciousIp($ip)) {
            $this->incrementFailures($ip);
            return false;
        }

        return true;
    }

    /**
     * Layer 5: Bot detection
     */
    private function detectBot(Request $request): bool
    {
        $userAgent = $request->header('User-Agent');

        // Missing user agent
        if (empty($userAgent)) {
            return false;
        }

        // Known bot patterns
        $botPatterns = [
            'selenium',
            'puppeteer',
            'playwright',
            'headless',
            'phantom',
            'bot',
            'crawler',
            'spider',
            'scraper',
            'curl',
            'wget',
            'python-requests',
            'axios',
            'postman',
            'insomnia',
            'httpclient',
            'okhttp',
            'go-http-client',
            'java',
            'scrapy',
            'beautifulsoup',
            'mechanize'
        ];

        foreach ($botPatterns as $pattern) {
            if (stripos($userAgent, $pattern) !== false) {
                return false;
            }
        }

        // Check for headless browser indicators
        if ($this->detectHeadlessBrowser($request)) {
            return false;
        }

        return true;
    }

    /**
     * Detect headless browser indicators
     */
    private function detectHeadlessBrowser(Request $request): bool
    {
        // Check for common headless browser headers
        $headlessIndicators = [
            'HeadlessChrome',
            'PhantomJS',
            'webdriver',
        ];

        $userAgent = $request->header('User-Agent', '');

        foreach ($headlessIndicators as $indicator) {
            if (stripos($userAgent, $indicator) !== false) {
                return true;
            }
        }

        // Check for missing expected headers
        $requiredHeaders = ['Accept', 'Accept-Language', 'Accept-Encoding'];
        foreach ($requiredHeaders as $header) {
            if (!$request->header($header)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Layer 6: Request pattern analysis
     */
    private function analyzeRequestPattern(Request $request): bool
    {
        $ip = $this->getClientIp($request);
        $fingerprint = $request->header('X-Browser-Fingerprint');

        // Check for rapid sequential requests (bot behavior)
        $patternKey = "pattern:$ip:$fingerprint";
        $requests = Cache::get($patternKey, []);
        $now = microtime(true);

        // Add current request
        $requests[] = $now;

        // Keep only last 10 requests
        $requests = array_slice($requests, -10);

        // Check if requests are too uniform (bot pattern)
        if (count($requests) >= 5) {
            $intervals = [];
            for ($i = 1; $i < count($requests); $i++) {
                $intervals[] = $requests[$i] - $requests[$i - 1];
            }

            // Calculate standard deviation
            $mean = array_sum($intervals) / count($intervals);
            $variance = 0;
            foreach ($intervals as $interval) {
                $variance += pow($interval - $mean, 2);
            }
            $stdDev = sqrt($variance / count($intervals));

            // If intervals are too uniform (low std dev), likely a bot
            if ($stdDev < 0.1 && $mean < 2) {
                return false;
            }
        }

        Cache::put($patternKey, $requests, now()->addMinutes(5));

        return true;
    }

    /**
     * Track fingerprint usage for analysis
     */
    private function trackFingerprint(Request $request, string $fingerprint): void
    {
        $ip = $this->getClientIp($request);
        $trackKey = "fingerprint:track:$fingerprint";

        $data = Cache::get($trackKey, [
            'ips' => [],
            'count' => 0,
            'first_seen' => now()->timestamp,
        ]);

        $data['ips'][$ip] = ($data['ips'][$ip] ?? 0) + 1;
        $data['count']++;

        $maxIps = (int) env('API_SECURITY_MAX_IPS_PER_FINGERPRINT', 50);
        $blockHours = (int) env('API_SECURITY_FINGERPRINT_BLOCK_HOURS', 24);

        // If fingerprint is used from too many IPs, it's suspicious.
        // Limit raised to 50 due to iCloud Private Relay / mobile CGNAT rotations.
        if (count($data['ips']) > $maxIps) {
            Cache::put("fingerprint:blocked:$fingerprint", true, now()->addHours($blockHours));
        }

        Cache::put($trackKey, $data, now()->addDays(1));
    }

    /**
     * Track legitimate requests for reputation building
     */
    private function trackLegitimateRequest(Request $request): void
    {
        $ip = $this->getClientIp($request);
        $reputationKey = "ip:reputation:$ip";

        $reputation = Cache::get($reputationKey, 0);
        Cache::put($reputationKey, min($reputation + 1, 100), now()->addDays(7));
    }

    /**
     * Increment failure count for IP
     */
    private function incrementFailures(string $ip): void
    {
        $failureKey = "ip:failures:$ip";
        $failures = Cache::get($failureKey, 0);
        Cache::put($failureKey, $failures + 1, now()->addHours(24));
    }

    /**
     * Check if IP is from suspicious range
     */
    private function isSuspiciousIp(string $ip): bool
    {
        // Check against known VPN/proxy ranges (simplified)
        // In production, use a service like IPQualityScore or similar

        // Block localhost/private IPs from public endpoints (only if explicitly enabled)
        if (env('API_SECURITY_BLOCK_PRIVATE_IPS', false)) {
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get real client IP (considering proxies like HAProxy)
     *
     * Security: Only trusts forwarded headers when request comes from trusted proxy.
     * Configure TRUSTED_PROXY_IPS in .env for your HAProxy server(s).
     */
    private function getClientIp(Request $request): string
    {
        $remoteAddr = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $request->ip();

        // Get trusted proxy IPs from environment (comma-separated)
        // Example: TRUSTED_PROXY_IPS=10.0.0.1,10.0.0.2,172.16.0.0/12
        $trustedProxies = array_filter(array_map('trim', explode(',', env('TRUSTED_PROXY_IPS', ''))));

        // Check if request is coming from a trusted proxy
        $isTrustedProxy = empty($trustedProxies) || $this->isIpInList($remoteAddr, $trustedProxies);

        // Only trust forwarded headers if from trusted proxy
        if ($isTrustedProxy) {
            $headers = [
                'HTTP_CF_CONNECTING_IP', // Cloudflare
                'HTTP_X_FORWARDED_FOR',  // HAProxy, Nginx, AWS ELB, etc.
                'HTTP_X_REAL_IP',        // Nginx
                'HTTP_CLIENT_IP',
            ];

            foreach ($headers as $header) {
                if (!empty($_SERVER[$header])) {
                    $ip = $_SERVER[$header];
                    // X-Forwarded-For can contain multiple IPs: client, proxy1, proxy2
                    // First IP is the original client
                    if (strpos($ip, ',') !== false) {
                        $ip = trim(explode(',', $ip)[0]);
                    }
                    if (filter_var($ip, FILTER_VALIDATE_IP)) {
                        return $ip;
                    }
                }
            }
        }

        return $remoteAddr;
    }

    /**
     * Check if IP is in a list of IPs or CIDR ranges
     */
    private function isIpInList(string $ip, array $list): bool
    {
        foreach ($list as $item) {
            if (strpos($item, '/') !== false) {
                // CIDR notation
                if ($this->ipInCidr($ip, $item)) {
                    return true;
                }
            } elseif ($ip === $item) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if IP is within CIDR range
     */
    private function ipInCidr(string $ip, string $cidr): bool
    {
        list($subnet, $bits) = explode('/', $cidr);
        $ip = ip2long($ip);
        $subnet = ip2long($subnet);
        $mask = -1 << (32 - (int) $bits);
        $subnet &= $mask;
        return ($ip & $mask) === $subnet;
    }

    /**
     * Log security event
     */
    private function logSecurityEvent(string $event, Request $request): void
    {
        $logData = [
            'resolved_ip' => $this->getClientIp($request),
            'remote_addr' => $_SERVER['REMOTE_ADDR'] ?? '',
            'x_forwarded_for' => $_SERVER['HTTP_X_FORWARDED_FOR'] ?? '',
            'x_real_ip' => $_SERVER['HTTP_X_REAL_IP'] ?? '',
            'cf_connecting_ip' => $_SERVER['HTTP_CF_CONNECTING_IP'] ?? '',
            'http_client_ip' => $_SERVER['HTTP_CLIENT_IP'] ?? '',
            'user_agent' => $request->header('User-Agent'),
            'endpoint' => $request->path(),
            'method' => $request->method(),
            'fingerprint' => $request->header('X-Browser-Fingerprint'),
            'timestamp' => now()->toIso8601String(),
        ];

        if ($event === 'rate_limit_exceeded') {
            $logData['reason'] = $request->attributes->get('security_limit_reason', 'unknown');
            $logData['count'] = $request->attributes->get('security_limit_count', 0);
        }

        Log::channel('security')->warning("API Security: $event", $logData);
    }

    /**
     * Generate security response
     */
    private function securityResponse(string $message, int $code, array $headers = []): Response
    {
        return response()->json([
            'response' => [
                'message' => $message,
                'code' => $code,
            ]
        ], $code)->withHeaders($headers);
    }
}
