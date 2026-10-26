<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Helpers\ClientIpHelper;

class ApiController extends Controller
{
    public function getServiceToken(): ?string
    {
        if (session()->has('service_token') && session()->has('service_token_created_at')) {
            $createdAt = session('service_token_created_at');
            if (now()->diffInMinutes($createdAt) < 60) {
                return session('service_token');
            }
            session()->forget(['service_token', 'service_token_created_at']);
        }

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->post(config('api.gateway') . '/aas/loginApi', [
                    'username' => config('api.username'),
                    'userpassword' => config('api.password'),
            "requestNumber" => uniqid(),
                    "compId" => config('api.comp_id'),
        ]);

        if ($response->successful() && isset($response['token'])) {
            $token = $response['token'];
            session([
                'service_token' => $token,
                'service_token_created_at' => now(),
            ]);
            return $token;
        }
        return null;
    }
    public function callApi(Request $request, $module, $service): \Illuminate\Http\JsonResponse
    {
        $clientIp = ClientIpHelper::getRealIp($request);
        $isPrivateIp = ClientIpHelper::isPrivateIp($request);
        $isExemptIp = ClientIpHelper::isExemptIp($request);
        $isTerminal = str_contains($request->header('Referer', ''), 'source=terminal');
        $isBypassed = $isTerminal || $isExemptIp || $isPrivateIp;

        $useRecaptcha = config('api.recaptcha.enabled') && !$isBypassed;

        if ($useRecaptcha) {
            $request->validate([
                'input' => 'required|array',
                'input.recaptchaToken' => 'required|string',
            ]);
            $verifyUrl = sprintf(
                config('api.recaptcha.site_verify_url'),
                config('api.recaptcha.project_id'),
                config('api.recaptcha.api_key')
            );
            $payload = [
                'event' => [
                    'token' => $request->input('input.recaptchaToken'),
                    'siteKey' => config('api.recaptcha.site_key'),
                    'expectedAction' => 'validate_captcha',
                ],
            ];
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Referer' => config('app.url'),
            ])->withOptions([
                'verify' => false,
            ])->post($verifyUrl, $payload);
            if (!$response->successful()) {
                return $this->errorResponse('RECAPTCHA_ERROR', 'Verification API request failed', 500);
            }
            $body = $response->json();
            if (empty($body['tokenProperties']['valid']) || !$body['tokenProperties']['valid']) {
                return $this->errorResponse('RECAPTCHA_INVALID', 'Token is invalid or expired', 403);
            }
//            $score = $body['riskAnalysis']['score'] ?? 0;
//            if ($score < config('api.recaptcha.allowed_score', 0.5)) {
//                return $this->errorResponse('RECAPTCHA_LOW_SCORE', 'Suspicious behavior detected', 403);
//            }
        }

        $token = $this->getServiceToken();
        if (!$token) {
            return $this->errorResponse('SESSION_EXPIRED', 'SESSION_EXPIRED', 403);
        }
        $input = $request->get('input') ?? [];
        unset($input['recaptchaToken']);
        $response = $this->makeServiceCall($module, $service, $token, uniqid(), $input);

        if ($response === null || !isset($response['response'])) {
            return $this->errorResponse('RESPONSE_IS_EMPTY', 'RESPONSE_IS_EMPTY', 500);
        }
//        if ($response && $response['response']['code'] !== '0') {
//            return $this->errorResponse($response['response']['code'], 'DATA_NOT_FOUND_ERROR', 404);
//        }
        if (in_array($response['response']['code'] ?? '', ['51153', '11821'])) {
            session()->forget(['service_token', 'service_token_created_at']);
            $token = $this->getServiceToken();
            if (!$token) {
                return $this->errorResponse('SESSION_EXPIRED', 'SESSION_EXPIRED', 403);
            }
            $response = $this->makeServiceCall($module, $service, $token, uniqid(), $input);
            if ($response === null || !isset($response['response'])) {
                return $this->errorResponse('RESPONSE_IS_EMPTY', 'RESPONSE_IS_EMPTY', 500);
            }
        }
        $errorResponse = $this->generateErrorResponse($response);
        if ($errorResponse) return $errorResponse;
        $locale = app()->getLocale();
        if ($module === 'migration' && $service === 'countries') {
            $countryList = $response['countryList'] ?? [];
            $excludedOids = ['169', '162'];
            $translatedCountries = collect($countryList)
                ->filter(function ($item) use ($excludedOids) {
                    return !in_array($item['oid'], $excludedOids);
                })
                ->map(function ($item) use ($locale) {
                if ($locale !== 'az') {
                    $translatedName = __('messages.' . $item['oid']);
                    $item['name'] = $translatedName !== 'messages.' . $item['oid'] ? $translatedName : $item['name'];
                }
                return $item;
            });
            $response['countryList'] = $translatedCountries->sortBy('name')->values()->toArray();
        }
        if ($module === 'his' && $service === 'getVehicleDetails') {
            $carBrands = collect($response['carBrands'] ?? []);
            $carModels = collect($response['carModels'] ?? []);
            $response['carBrands'] = $carBrands
                ->sortBy(function ($item) {
                    $startsWithLetter = preg_match('/^\p{L}/u', $item['brandName']) ? 0 : 1;
                    return [$startsWithLetter, mb_strtolower($item['brandName'])];
                })
                ->values()
                ->toArray();

            if ($carModels->isNotEmpty()) {
                $response['carModels'] = $carModels
                    ->sortBy(function ($item) {
                        $startsWithLetter = preg_match('/^\p{L}/u', $item['modelName']) ? 0 : 1;
                        return [$startsWithLetter, mb_strtolower($item['modelName'])];
                    })
                    ->values()
                    ->toArray();
            } else {
                $response['carModels'] = null;
            }
        }
        if ($module === 'his' && $service === 'getBorderVehiclePoliciesByVehicle') {
            $resultHash = $response['resultHash'] ?? [];
            $translatedResultHash = collect($resultHash)->map(function ($item) use ($locale) {
                if ($locale !== 'az') {
                    $translatedName = __('messages.' . $item['policyStatus']);
                    $item['policyStatus'] = $translatedName !== 'messages.' . $item['policyStatus'] ? $translatedName : $item['policyStatus'];
                    $translatedVehicleType = __('messages.' . $item['vehicleType']);
                    $item['vehicleType'] = $translatedVehicleType !== 'messages.' . $item['vehicleType'] ? $translatedVehicleType : $item['vehicleType'];
                }
                if (!empty($item['insurerName'])) {
                    $companyName = trim($item['insurerName']);
                    $companyName = str_replace(['"', "'"], '', $companyName);
                    $companyName = stripslashes($companyName);
                    $key = 'messages.INSURANCE_COMPANY_' . $companyName;
                    $translatedCompany = __($key);
                    if ($translatedCompany !== $key) {
                        $item['insurerName'] = $translatedCompany;
                    }
                }
                return $item;
            });
            $response['resultHash'] = $translatedResultHash->values()->toArray();
        }

        if ($module === 'payment' && $service === 'invoiceStatus') {
            if (!empty($response['invoiceStatus']) && $locale !== 'az') {
                $translated = __('messages.INVOICE_STATUS_' . $response['invoiceStatus']);
                $response['status'] = $translated !== 'messages.INVOICE_STATUS_' . $response['invoiceStatus']
                    ? $translated
                    : $response['invoiceStatus'];
            }
        }
        if ($module === 'payment' && $service === 'invoiceInfo') {
            if (!empty($response['invoiceList']) && isset($response['invoiceList'][0])) {
                $invoice = $response['invoiceList'][0];
                if (!empty($invoice['receiver']['fullName'])) {
                    $companyName = trim($invoice['receiver']['fullName']);
                    $key = 'messages.INSURANCE_COMPANY_' . $companyName;

                    $translated = __($key);
                    if ($translated === $key) {
                        $translated = $companyName;
                    }
                    $response['invoiceList'][0]['receiver']['fullName'] = $translated;
                }
            }
        }

        return response()->json($response);
    }


    private function makeServiceCall($module, $service, $token, $requestNumber, $input)
    {
        $realIp = ClientIpHelper::getRealIp(request());

        Log::channel('serviceCall')->info('tpl.az before request', [
            "requestNumber" => $requestNumber,
            "serviceName" => $module . '/' . $service,
            "input" => $input,
            "token" => $token ? substr($token, 0, 8) . '***' : null,
            "resolved_ip" => $realIp,
            "remote_addr" => $_SERVER['REMOTE_ADDR'] ?? '',
            "x_forwarded_for" => $_SERVER['HTTP_X_FORWARDED_FOR'] ?? '',
            "x_real_ip" => $_SERVER['HTTP_X_REAL_IP'] ?? '',
        ]);

        $startTime = microtime(true);

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->post(config('api.gateway') . '/' . $module . '/' . $service, [
            "requestNumber" => $requestNumber,
            'token' => $token,
                    "compId" => config('api.comp_id'),
            ...$input,
        ]);

        $durationMs = round((microtime(true) - $startTime) * 1000);
        $responseData = $response->json();

        $isError = !$response->successful() || (isset($responseData['response']['code']) && $responseData['response']['code'] !== '0');

        $logData = [
            "requestNumber" => $requestNumber,
            "serviceName" => $module . '/' . $service,
            "status" => $response->status(),
            "duration_ms" => $durationMs,
        ];

        if ($isError) {
            $logData["error_body"] = $response->body();
        }

        Log::channel('serviceCall')->info('tpl.az after request', $logData);

        return $responseData;
    }

    public function generateErrorResponse($response): false|\Illuminate\Http\JsonResponse
    {
        if (isset($response["response"]) && $response["response"]["code"] !== '0') {
            if ($response["response"]["code"] === '99999') {
                $decoded = json_decode($response["response"]["message"]);
                if (isset($decoded[0]->errorLangId)) {
                    return $this->errorResponse("1", $decoded[0]->errorLangId);
                }
            }

            return $this->errorResponse(
                $response["response"]["code"],
                $response["response"]["message"]
            );
        }

        return false;
    }

    public function errorResponse($code, $messageKey = null, $httpCode = 200): \Illuminate\Http\JsonResponse
    {
        $translatedMessage = $messageKey ? __('messages.' . $messageKey) : __('messages.GENERAL_ERROR');
        return response()->json([
            'response' => [
                'code' => $code,
                'message' => $translatedMessage,
                'messageKey' => $messageKey
            ]
        ], $httpCode);
    }



}
