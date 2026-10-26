
@php
    $l = app()->getLocale();
    $isTerminal = request()->query('source') === 'terminal';
    $homeUrl= $isTerminal ? '#' : route('home');
@endphp

@if($isTerminal)
    <header class="header header--terminal">
        <div class="container">
            <div class="header-left">
                <a href="{{$homeUrl}}">
                    <img class="tpl-logo" src="{{ asset('images/logo-tpl.svg') }}" alt="{{ __('messages.logo_tpl') }}" />
                </a>
            </div>

            <div class="header-right" style="display: flex !important;">
                @include('partials/language_switcher')
            </div>
        </div>
    </header>
    <style>
        .header--terminal {
            padding-top: 15px !important;
            padding-bottom: 15px !important;
            box-shadow: 0 2px 15px rgba(0,0,0,0.08) !important;
        }
        .header--terminal .lang-switcher {
            display: inline-block !important;
            margin-right: 0 !important;
        }
        .header--terminal .lang-btn {
            padding: 12px 24px !important;
            font-size: 16px !important;
            font-weight: 600;
            border-width: 2px !important;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .header--terminal .lang-menu {
            min-width: 100% !important;
            border-width: 2px !important;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important;
        }
        .header--terminal .lang-menu a {
            padding: 15px 20px !important;
            font-size: 16px !important;
            font-weight: 500;
        }
        /* Adjust body padding for terminal mode */
        body {
            padding-top: 90px !important;
        }
        @media (max-width: 768px) {
            body {
                padding-top: 70px !important;
            }
            .header--terminal {
                padding-top: 10px !important;
                padding-bottom: 10px !important;
            }
        }
    </style>
@else
<header class="header">
    <div class="container">
        <div class="header-left">
                <a href="{{ route('home') }}">
                <img  class="tpl-logo" src="{{ asset('images/logo-tpl.svg') }}" alt="{{ __('messages.logo_tpl') }}" />
            </a>

            <a href="https://isb.az/" target="_blank" class="logo-isb">
                <img src="{{ asset('images/logo-isb-'.$l.'.svg') }}" alt="{{ __('messages.logo_isb') }}" />
            </a>
        </div>

        <div class="header-right">
            <nav class="nav">
                    <a href="{{ route('about-us') }}" class="nav-link">{{ __('messages.about-us') }}</a>
            </nav>
            <nav class="nav">
                    <a href="{{ route('insurance-companies') }}" class="nav-link">{{ __('messages.insurance_companies') }}</a>
            </nav>
            <nav class="nav">
                    <a href="{{ route('manual-guide') }}" class="nav-link">{{ __('messages.manual-guide') }}</a>
            </nav>
            <nav class="nav" style="margin-right: 18px">
                    <a href="{{ route('faq') }}" class="nav-link">{{ __('messages.faq') }}</a>
            </nav>
            @include('partials/language_switcher')
            <button class="button button-primary button-pay"
{{--                    style="margin-right: -45px"--}}
            >
                <img src="{{ asset('images/icons/icon-credit-card.svg') }}" alt="" />
                {{ __('messages.pay_now') }}
            </button>
                <a href="{{ route('registration') }}" class="button button-tpl button-registration">
                {{ __('messages.contract_registration') }}
                <img src="{{ asset('images/icons/icon-add.svg') }}" alt="" />
            </a>
            <button class="button-icon button-icon--outline button-menu">
                <img src="{{ asset('images/icons/icon-menu.svg') }}" alt="" class="icon-menu" />
                <img src="{{ asset('images/icons/icon-cross.svg') }}" alt="" class="icon-close" />
            </button>

        </div>
    </div>
</header>
@endif
