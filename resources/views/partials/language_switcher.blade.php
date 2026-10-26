
<style>
    .lang-switcher {
        position: relative;
        display: inline-block;
        font-family: sans-serif;
        margin-right: -28px;
        z-index: 999;
        @media (max-width: 913px) {
            margin-right: 0 !important;
        }
    }

    .lang-btn {
        background-color: #ffffff;
        border: 1px solid #12a5bc;
        color: #12a5bc;
        padding: 13px 12px;
        font-size: 14px;
        cursor: pointer;
        border-radius: 12px;
        min-width: 60px;
        @media (max-width: 568px) {
            padding: 6px 12px;
            border-radius: 6px;
        }
    }

    .lang-menu {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        background-color: #ffffff;
        border: 1px solid #12a5bc;
        color: #12a5bc;
        border-radius: 12px;
        min-width: 62.8px;
        z-index: 999;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        @media (max-width: 768px) {
            border-radius: 6px;
        }
    }

    .lang-menu a {
        display: block;
        padding: 12px 12px;
        text-decoration: none;
        color: #12a5bc;
        @media (max-width: 768px) {
            padding: 6px 12px;
            border-radius: 6px;
        }
    }

    .lang-menu a:hover {
        background-color: #0d8fa1db;
        color: #ffffff;
        border-radius: 12px;
        padding: 12px 12px;
        @media (max-width: 768px) {
            padding: 6px 12px;
            border-radius: 6px;
        }
    }
    .lang-switcher.open .lang-menu {
        display: block;
    }
</style>
<div class="lang-switcher">
    <button class="lang-btn">
{{--        {{ $available_locales[app()->getLocale()]['flag'] }}--}}
        {{ $available_locales[app()->getLocale()]['label'] }} ▼
    </button>
    <div class="lang-menu">
        @foreach ($available_locales as $localeCode => $locale)
            @if (app()->getLocale() !== $localeCode)
                @php
                    $segments = request()->segments();
                    if(isset($segments[0])) {
                        $segments[0] = $localeCode;
                    } else {
                        $segments[] = $localeCode;
                    }
                    $url = url(implode('/', $segments));
                    if (request()->getQueryString()) {
                        $url .= '?' . request()->getQueryString();
                    }
                @endphp
                <a href="{{ $url }}">
                    {{ $locale['label'] }}
                </a>
            @endif
        @endforeach
    </div>
</div>
