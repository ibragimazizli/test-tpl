@php
    $l = app()->getLocale();
    $isTerminal = request()->query('source') === 'terminal';
@endphp
@if(!$isTerminal)
<footer class="footer">
    <div class="container">

        <div class="footer-contact">
            <p style="font-weight: normal">{{ __('messages.footer_text_tpl') }}</p>
                <a href="{{ route('contact-us') }}" class="contact-url">
                <p class="contact-text">{{ __('messages.contact') }}</p>
            </a>
        </div>
        <div class="footer-links">
            <div class="social-links">
                <a href="https://www.facebook.com/www.isb.az/" target="_blank" class="social-link">
                    <i class="fa-brands fa-facebook fa-lg"></i>
                </a>

                <a href="https://www.instagram.com/isb_az/" target="_blank" class="social-link">
                    <i class="fa-brands fa-instagram fa-lg"></i>
                </a>

                <a href="https://www.youtube.com/channel/UCR6jhq5x_lfmBmUkPeH2nPQ" target="_blank" class="social-link">
                    <i class="fa-brands fa-youtube fa-lg"></i>
                </a>


                <a href="https://x.com/isb_az?s=11" target="_blank" class="social-link">
                    <i class="fa-brands fa-x-twitter fa-lg"></i>
                </a>

                <a href="https://www.linkedin.com/company/i%CC%87cbari-s%C4%B1%C4%9Forta-b%C3%BCrosu/posts/?feedView=all" target="_blank" class="social-link">
                    <i class="fa-brands fa-linkedin fa-lg"></i>
                </a>
            </div>
        </div>
        <a href="https://isb.az/" target="_blank" class="logo-isb">
            <img src="{{ asset('images/logo-isb-'.$l.'.svg') }}" alt="{{ __('messages.logo_isb') }}" />
        </a>
    </div>
</footer>
@endif

<style>
    .social-links {
        display: flex;
        gap: 18px;
    }
    .social-links .social-link {
        height: 46px;
        width: 46px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #7a8292;
        border: 1px solid #cbd5dc;
        transition: color .4s ease, border-color .4s ease;
    }
    .social-links .social-link:hover {
        border-color: #12a5bc;
        color: #12a5bc;
    }
    .contact-url .contact-text{
        color: #000000;
        transition: color 0.4s ease;
    }
    .contact-url .contact-text:hover {
        color: #12a5bc;
    }
</style>
