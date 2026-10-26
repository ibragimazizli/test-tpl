@php
    $v = '?v=' . env('APP_VERSION', '1.0');
    $l = app()->getLocale();

    $clientIp = \App\Helpers\ClientIpHelper::getRealIp(request());
    $isPrivateIp = \App\Helpers\ClientIpHelper::isPrivateIp(request());
    $isExemptIp = \App\Helpers\ClientIpHelper::isExemptIp(request());

    $isTerminal = request()->query('source') === 'terminal';
    $isBypassed = $isTerminal || $isExemptIp || $isPrivateIp;

    $useRecaptcha = config('api.recaptcha.enabled') && !$isBypassed;
@endphp

<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="no-js">

<head>
    <meta charset="UTF-8" />
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo-tpl.svg') . $v }}" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    @if($useRecaptcha)
        <meta name="recaptcha-site-key" content="{{ env('RECAPTCHA_SITE_KEY') }}">
    @endif
    <meta name="description"
        content="TPL.AZ - Border insurance for foreign vehicles entering Azerbaijan. Fast, secure, and legally compliant insurance service.">
    <meta name="author" content="TPL.AZ Team">
    <meta name="robots" content="index, follow">
    <meta property="og:title" content="TPL.AZ - Border Insurance for Azerbaijan" />
    <meta property="og:description"
        content="Quick and secure border insurance required for entry into Azerbaijan. Purchase online and stay compliant with local laws." />
    <meta property="og:image" content="{{ asset('images/logo-tpl.svg') . $v }}" />
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:type" content="website" />
    <title>@yield('title', 'TPL.AZ - Border Insurance for Azerbaijan')</title>
    <link rel="stylesheet" href="{{ asset('fonts/fontawesome.min.css') . $v }}" />
    <link rel="stylesheet" href="{{ asset('vendor/swiper/swiper-bundle.min.css') . $v }}" />
    <link rel="stylesheet" href="{{ asset('vendor/intl-tel-input-25.3.1/build/css/intlTelInput.min.css') . $v }}" />

    @yield('styles')

    @vite([
        'resources/css/app.css',
        'resources/css/style.min.css',
        'resources/scss/style.scss'
    ])

    @if(app()->environment('production') && !$isExemptIp)
        <script async src="https://www.googletagmanager.com/gtag/js?id=G-TMEY38ZTXR"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag() { dataLayer.push(arguments); }
            gtag('js', new Date());

            gtag('config', 'G-TMEY38ZTXR');
        </script>
    @endif
</head>

<body>
    <div class="core-loader" id="core-loader" style="display: none">
        <div class="spinner"></div>
    </div>
    <div class="overlay"></div>

    @include('partials.language_modal')

    {{--<div
        style="background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba; padding: 15px; margin: 0; text-align: center; font-size: 16px; z-index: 9999;">
        --}}
        {{-- {!! __('messages.maintenance_alert') !!}--}}
        {{--</div>--}}
    @yield('content')

    <script src="{{ asset('vendor/swiper/swiper-bundle.min.js') . $v }}" defer></script>
    <script src="{{ asset('vendor/intl-tel-input-25.3.1/build/js/intlTelInput.min.js') . $v }}" defer></script>
    @if($useRecaptcha)
        <script src="{{ env('RECAPTCHA_SITE_RENDER_URL') . env('RECAPTCHA_SITE_KEY') }}"></script>
    @endif

    <script>
        window.translations = {
            rate_limit_title: "{{ __('messages.rate_limit_title') }}",
            rate_limit_message: "{{ __('messages.rate_limit_message') }}",
            SECURITY_TOO_MANY_REQUESTS: "{{ __('messages.security_too_many_requests') }}",
            SECURITY_INVALID_REQUEST_SIGNATURE: "{{ __('messages.security_invalid_signature') }}",
            SECURITY_INVALID_BROWSER_FINGERPRINT: "{{ __('messages.security_invalid_fingerprint') }}",
            SECURITY_IP_NOT_ALLOWED: "{{ __('messages.security_ip_not_allowed') }}",
            SECURITY_ACCESS_DENIED: "{{ __('messages.security_access_denied') }}",
            SECURITY_AUTOMATED_ACCESS_DETECTED: "{{ __('messages.security_automated_access') }}",
            SECURITY_SUSPICIOUS_ACTIVITY_DETECTED: "{{ __('messages.security_suspicious_activity') }}",
            security_error_title: "{{ __('messages.security_error_title') }}",
            security_error_message: "{{ __('messages.security_error_message') }}"
        };
    </script>

    @vite([
        'resources/js/app.js',
        'resources/js/main.js',
        'resources/js/api-security.js'
    ])

    <script>
        @php
            $appKey = config('app.key');
            if (str_starts_with($appKey, 'base64:')) {
                $appKey = substr($appKey, 7);
            }
        @endphp
        window.API_SECURITY_SECRET = "{{ $appKey }}";
    </script>

    {{--
    <script src="{{env('USERWAY_URL')}}" data-account="{{env('USERWAY_ACCOUNT')}}" async></script>--}}
</body>

</html>
