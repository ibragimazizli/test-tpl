<div id="welcome-language-modal" class="lang-welcome-overlay" style="display: none;">
    <div class="lang-welcome-modal">
        <div class="lang-welcome-header">
            <h3>🌐 Please, select your language</h3>
            <p class="lang-welcome-subheader">Zəhmət olmasa, dil seçimi edin.</p>
        </div>
        <div class="lang-welcome-grid">
            @foreach ($available_locales as $localeCode => $locale)
                @php
                    $segments = request()->segments();
                    if(isset($segments[0])) {
                        if (array_key_exists($segments[0], $available_locales)) {
                            $segments[0] = $localeCode;
                        } else {
                            array_unshift($segments, $localeCode);
                        }
                    } else {
                        $segments[] = $localeCode;
                    }
                    $url = url(implode('/', $segments));
                    if (request()->getQueryString()) {
                        $url .= '?' . request()->getQueryString();
                    }
                    if (app()->getLocale() === $localeCode) {
                        $url = 'javascript:void(0)';
                    }
                @endphp
                <a href="{{ $url }}" class="lang-welcome-btn @if(app()->getLocale() === $localeCode) current-locale @endif" data-locale="{{ $localeCode }}">
                    <span class="lang-label">{{ $locale['label'] }}</span>
                </a>
            @endforeach
        </div>
    </div>
</div>

<style>
/* Modern, minimalist language selection modal */
.lang-welcome-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background-color: rgba(18, 165, 188, 0.15); /* Sleek backdrop with brand color hint */
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    z-index: 10000;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    animation: fadeInModal 0.5s ease forwards;
}

.lang-welcome-modal {
    background: #ffffff;
    border-radius: 24px;
    padding: 40px;
    width: 90%;
    max-width: 440px;
    box-shadow: 0 24px 60px rgba(0, 0, 0, 0.1);
    transform: translateY(30px) scale(0.95);
    opacity: 0;
    animation: scaleUpModal 0.6s cubic-bezier(0.16, 1, 0.3, 1) 0.15s forwards;
    text-align: center;
    position: relative;
    overflow: hidden;
}

/* Optional soft gradient accent at the top */
.lang-welcome-modal::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 6px;
    background: linear-gradient(90deg, #12a5bc, #46cddb);
}

.lang-welcome-header {
    margin-bottom: 32px;
}

.lang-welcome-header h3 {
    margin: 0;
    font-size: 24px;
    color: #1a1a1a;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-weight: 700;
    letter-spacing: -0.5px;
}

.lang-welcome-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
    max-height: 60vh;
    overflow-y: auto;
    padding: 4px; /* Gives room for button hover states */
}

/* Custom scrollbar for Webkit */
.lang-welcome-grid::-webkit-scrollbar {
    width: 6px;
}
.lang-welcome-grid::-webkit-scrollbar-track {
    background: transparent;
}
.lang-welcome-grid::-webkit-scrollbar-thumb {
    background-color: rgba(18, 165, 188, 0.3);
    border-radius: 10px;
}
.lang-welcome-grid::-webkit-scrollbar-thumb:hover {
    background-color: rgba(18, 165, 188, 0.6);
}

.lang-welcome-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 16px;
    background-color: #fcfcfc;
    border: 1px solid #eaeaea;
    border-radius: 16px;
    color: #4a4a4a;
    text-decoration: none;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 15px;
    font-weight: 600;
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    position: relative;
    overflow: hidden;
}

.lang-welcome-btn::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 100%;
    height: 100%;
    background: #12a5bc;
    transform: translate(-50%, -50%) scale(0);
    border-radius: 50%;
    z-index: 0;
    transition: transform 0.4s ease-out, opacity 0.3s;
    opacity: 0;
}

.lang-welcome-btn .lang-label {
    position: relative;
    z-index: 1;
    transition: color 0.3s ease;
}

.lang-welcome-btn:hover {
    border-color: #12a5bc;
    transform: translateY(-4px);
    box-shadow: 0 10px 20px rgba(18, 165, 188, 0.15);
}

.lang-welcome-btn:hover::after {
    transform: translate(-50%, -50%) scale(2.5);
    opacity: 1;
}

.lang-welcome-btn:hover .lang-label {
    color: #ffffff;
}

.lang-welcome-btn:active {
    transform: translateY(-2px);
}

@media (max-width: 480px) {
    .lang-welcome-grid {
        grid-template-columns: 1fr;
        gap: 12px;
        max-height: 55vh; /* slightly less tall for better mobile fit */
    }
    .lang-welcome-modal {
        padding: 32px 24px;
        border-radius: 20px;
    }
}

/* Terminal/Kiosk mode optimizations */
@media (max-width: 320px), (max-height: 500px) {
    .lang-welcome-modal {
        padding: 20px 16px;
        width: 95%;
    }
    .lang-welcome-header {
        margin-bottom: 12px;
    }
    .lang-welcome-header h3 {
        font-size: 20px;
    }
    .lang-welcome-grid {
        max-height: 65vh; /* More height given since padding is less */
    }
    .lang-welcome-btn {
        padding: 12px;
        font-size: 14px;
    }
}

@keyframes fadeInModal {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes scaleUpModal {
    from {
        opacity: 0;
        transform: translateY(30px) scale(0.95);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}
</style>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const LANG_STORAGE_KEY = 'user_has_selected_language';
    const hasSelectedLang = localStorage.getItem(LANG_STORAGE_KEY);
    
    // Attach click events to the header language switcher
    const headerLangLinks = document.querySelectorAll('.lang-menu a');
    headerLangLinks.forEach(function(link) {
        link.addEventListener('click', function() {
            localStorage.setItem(LANG_STORAGE_KEY, 'true');
        });
    });

    if (!hasSelectedLang) {
        // We'll slightly delay showing the modal to ensure smooth entry
        setTimeout(() => {
            const modal = document.getElementById('welcome-language-modal');
            if (modal) {
                modal.style.display = 'flex';
                // Lock body scroll
                document.body.style.overflow = 'hidden';
            }
        }, 100);

        // Handle modal language selection
        const modalLangLinks = document.querySelectorAll('.lang-welcome-btn');
        modalLangLinks.forEach(function(link) {
            link.addEventListener('click', function(e) {
                localStorage.setItem(LANG_STORAGE_KEY, 'true');
                
                // Allow body scroll again
                document.body.style.overflow = '';

                // If clicking current locale, close modal without redirect
                if (link.getAttribute('href') === 'javascript:void(0)') {
                    e.preventDefault();
                    const modal = document.getElementById('welcome-language-modal');
                    modal.style.transition = 'opacity 0.4s ease';
                    modal.style.opacity = '0';
                    setTimeout(() => {
                        modal.style.display = 'none';
                    }, 400);
                }
            });
        });
    }
});
</script>