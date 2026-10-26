<?php

namespace App\Helpers;

use Illuminate\Http\Request;

class ClientIpHelper
{
    /**
     * Determine the real client IP address.
     */
    public static function getRealIp(?Request $request = null): string
    {
        $request = $request ?? request();
        
        $clientIp = $request->header('X-Forwarded-For')
            ? trim(explode(',', $request->header('X-Forwarded-For'))[0])
            : $request->ip();

        return strtok((string) $clientIp, ':');
    }

    /**
     * Check if the current client IP is a private/reserved IP.
     */
    public static function isPrivateIp(?Request $request = null): bool
    {
        $ip = self::getRealIp($request);
        return !filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
    }

    /**
     * Check if the current client should bypass checks based on specific IP.
     * Supports a comma-separated list of IPs loaded from the .env file.
     */
    public static function isExemptIp(?Request $request = null): bool
    {
        $ip = self::getRealIp($request);
        
        // Read from ENV, supporting multiple comma-separated IPs (fallback to existing one).
        $exemptIpsStr = env('EXEMPT_CLIENT_IPS', '94.20.38.68');
        
        // Convert to array and clear whitespace
        $exemptIps = array_filter(array_map('trim', explode(',', $exemptIpsStr)));
        
        return in_array($ip, $exemptIps, true);
    }
}
