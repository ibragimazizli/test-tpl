<div class="mobile-menu">
    <div class="container">
        <nav class="mobile-menu__nav">
            @include('partials/language_switcher')
            <a href="{{ route('about-us') }}" class="nav-link">
                <img src="{{ asset('images/icons/icon-arrow-right.svg') }}" alt="" />
                {{ __('messages.about-us') }}
            </a>

            <a href="{{ route('insurance-companies') }}" class="nav-link">
                <img src="{{ asset('images/icons/icon-arrow-right.svg') }}" alt="" />
                {{ __('messages.insurance_companies') }}
            </a>

            <a href="{{ route('manual-guide') }}" class="nav-link">
                <img src="{{ asset('images/icons/icon-arrow-right.svg') }}" alt="" />
                {{ __('messages.manual-guide') }}
            </a>
            <a href="{{ route('faq') }}" class="nav-link">
                <img src="{{ asset('images/icons/icon-arrow-right.svg') }}" alt="" />
                {{ __('messages.faq') }}
            </a>
        </nav>
    </div>
</div>
