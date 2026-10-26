import "../scss/style.scss";
import Swiper from "swiper";
import "intl-tel-input/build/js/intlTelInput";
import Pikaday from 'pikaday';
import 'pikaday/css/pikaday.css';
import { VirtualKeyboard } from './virtual-keyboard';

// Initialize Virtual Keyboard if source=terminal
const urlParams = new URLSearchParams(window.location.search);
if (urlParams.get('source') === 'terminal') {
    try {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                document.body.classList.add('is-terminal');
                new VirtualKeyboard();
            });
        } else {
            document.body.classList.add('is-terminal');
            new VirtualKeyboard();
        }
    } catch (error) {
        console.error('Failed to initialize Virtual Keyboard:', error);
    }
}

import axios from 'axios';

axios.interceptors.response.use(null, function (error) {
    if (error.response && error.response.status === 419) {
        console.warn("CSRF token expired, reloading the page...");
        location.reload();
    }
    return Promise.reject(error);
});

const buttonPay = document.querySelector(".button-pay");
const modalPayment = document.querySelector(".modal-payment");
const overlay = document.querySelector(".overlay");
const modalCloseEls = document.querySelectorAll(".modal-close");
const faqItems = document.querySelectorAll(".faq-item");
const buttonMenu = document.querySelector(".button-menu");
const mobileMenu = document.querySelector(".mobile-menu");
const footer = document.querySelector(".footer");
const dropdowns = document.querySelectorAll(".dropdown");
const loader = document.getElementById('core-loader');

window.paymentState = {
    invoiceList: [],
    transactionId: '',
};

const closeModal = (shouldRedirect = true) => {
    if (overlay) overlay.classList.remove("active");
    document
        .querySelectorAll(".modal")
        .forEach((item) => {
            item.querySelectorAll("input").forEach((input) => {
                if (input.type !== "radio" && input.type !== "checkbox") {
                    input.value = "";
                }
            });
            document.querySelectorAll(".alert").forEach((alert) => {
                alert.style.display = "none";
            });
            item.classList.remove("active")
            if (item.classList.contains("modal-payment")) {
                const plateSearchBtn = document.getElementById("plate-search-button");
                const finalSearchBtn = document.getElementById("final-search-button");
                const modalBottom = document.getElementById("modalBottom");
                const plateInvoiceList = document.getElementById("plate-invoice-list");
                const alertContainer = document.querySelector(".alert_payment_search_modal");
                if (alertContainer) alertContainer.style.display = 'none';
                if (plateInvoiceList) plateInvoiceList.innerHTML = '';
                if (finalSearchBtn) finalSearchBtn.style.display = 'none';
                if (modalBottom) modalBottom.style.display = 'none';
                const plateRadio = document.querySelector("input[name='searchType'][value='plate']");
                if (plateRadio) {
                    plateRadio.checked = true;
                    plateRadio.dispatchEvent(new Event("change"));
                }
                if (shouldRedirect) {
                    const locale = document.documentElement.lang || 'az';
                    window.location.href = "/" + locale;
            }
            }
        });
        window.paymentState = {
            invoiceList: [],
            transactionId: '',
        };
};

if (overlay) {
    overlay.addEventListener("click", closeModal);
}

if (modalCloseEls) {
    modalCloseEls.forEach((item) => {
    item.addEventListener("click", closeModal);
});
}

if (faqItems) {
faqItems.forEach((item) => {
    item.addEventListener("click", () => {
        const isActive = item.classList.contains("active");
        faqItems.forEach((i) => i.classList.remove("active"));
        if (!isActive) {
            item.classList.add("active");
        }
    });
});
}
if (typeof Swiper !== "undefined") {
    const swiper = new Swiper(".swiper", {
        direction: "horizontal",
        loop: true,
        spaceBetween: 20,
        slidesPerView: 2.2,
        breakpoints: {
            640: {
                slidesPerView: 3,
            },
            1200: {
                slidesPerView: 5,
            },
        },
        autoplay: {
            delay: 3000,
        },
        speed: 1000,
    });
}






buttonPay?.addEventListener("click", () => {
    closeModal(false);
    overlay.classList.add("active");
    modalPayment.classList.add("active");
});
buttonMenu?.addEventListener("click", () => {
    buttonMenu.classList.toggle("active");
    footer.classList.toggle("fixed");
    mobileMenu?.classList.toggle("active");
});

// Consolidated dropdown logic to handle terminal keyboard and search interactions properly
let isInputFocused = false;  // Inputa fokuslanıbsa, bunu izləyək

// Fokuslananda inputa fokus olduğunu qeyd edirik
document.addEventListener("focusin", function (e) {
    if (e.target.closest('.dropdown__input_hidden') || e.target.closest('.dropdown-search-input-wrapper')) {
        isInputFocused = true;
    }
            });

// Fokusdan çıxanda inputa fokus olmadığını qeyd edirik
document.addEventListener("focusout", function (e) {
    if (e.target.closest('.dropdown__input_hidden') || e.target.closest('.dropdown-search-input-wrapper')) {
        isInputFocused = false;
    }
    });

    document.addEventListener("click", function (e) {
    const isDropdownBtn = e.target.closest(".dropdown__button");
    const isInsideDropdown = e.target.closest(".dropdown");
    const isKeyboard = e.target.closest('.virtual-keyboard-wrapper');
    const isSearchInput = e.target.closest('.dropdown-search-input-wrapper') || e.target.closest('.dropdown__input_hidden');  // Fokuslanmış inputu tap

    // 1. Handle toggle click
    if (isDropdownBtn) {
        const wrapper = isDropdownBtn.closest(".dropdown");
        const list = wrapper.querySelector(".dropdown__list");

        // Close others first
        dropdowns.forEach(d => {

            if (d !== wrapper) {

                const otherList = d.querySelector(".dropdown__list");
                const otherBtn = d.querySelector(".dropdown__button");
                if (otherList) otherList.classList.remove("dropdown__list_visible", "active");
                if (otherBtn) otherBtn.classList.remove("dropdown__button_active");
            }
        });

        if (list) {
            list.classList.toggle("dropdown__list_visible");
            list.classList.toggle("active");
        }
        isDropdownBtn.classList.toggle("dropdown__button_active");
        return;
    }

    // 2. Handle item selection
    const listItem = e.target.closest(".dropdown__list-item");
    if (listItem) {
        const wrapper = listItem.closest(".dropdown");
        const btn = wrapper.querySelector(".dropdown__button");
        const list = wrapper.querySelector(".dropdown__list");
        const input = wrapper.querySelector(".dropdown__input_hidden");

        wrapper.querySelectorAll(".dropdown__list-item").forEach(el =>
            el.classList.remove("dropdown__list-item_active")
        );
        listItem.classList.add("dropdown__list-item_active");

        listItem.scrollIntoView({ behavior: "smooth", block: "nearest" });

        if (btn) {
            const span = btn.querySelector("span");
            if (span) span.textContent = listItem.innerText;
            btn.classList.remove("dropdown__button_active");
        }

        if (input) {
            input.setAttribute("value", listItem.dataset.value);
            input.dispatchEvent(new Event("change"));
        }

        if (list) list.classList.remove("dropdown__list_visible", "active");
        return;
            }

    // 3. Handle click outside (but ignore input if focused)
    if (!isInsideDropdown && !isKeyboard && !isSearchInput && !isInputFocused) {
        dropdowns.forEach(d => {
            const list = d.querySelector(".dropdown__list");
            const btn = d.querySelector(".dropdown__button");
            if (list) list.classList.remove("dropdown__list_visible", "active");
            if (btn) btn.classList.remove("dropdown__button_active");
        });
        }
    });

    document.addEventListener("keydown", function (e) {
        if (e.key === "Tab" || e.key === "Escape") {
        dropdowns.forEach(d => {
            const list = d.querySelector(".dropdown__list");
            const btn = d.querySelector(".dropdown__button");

            if (list) list.classList.remove("dropdown__list_visible", "active");
            if (btn) btn.classList.remove("dropdown__button_active");
        });
        }
    });

window.initBirthdayPicker = function (fieldId = 'birthday') {
    const field = document.getElementById(fieldId);
    if (!field) return;

    const today = new Date();
    const sixteenYearsAgo = new Date(
        today.getFullYear() - 16,
        today.getMonth(),
        today.getDate()
    );

    new Pikaday({
        field,
        yearRange: [1920, sixteenYearsAgo.getFullYear()],
        maxDate: sixteenYearsAgo,
        defaultDate: sixteenYearsAgo,
        setDefaultDate: true,
        format: 'DD-MM-YYYY',
        toString(date) {
            const day = date.getDate().toString().padStart(2, '0');
            const month = (date.getMonth() + 1).toString().padStart(2, '0');
            const year = date.getFullYear();
            return `${day}-${month}-${year}`;
        },
        parse(dateString) {
            const [day, month, year] = dateString.split('-');
            return new Date(parseInt(year), parseInt(month) - 1, parseInt(day));
        }
    });
};

window.showLoader = function () {
    loader.style.display = 'flex';
}

window.hideLoader = function () {
    loader.style.display = 'none';
}
window.isValidEmail = function(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}
const siteKeyMeta = document.querySelector('meta[name="recaptcha-site-key"]');
const siteKey = siteKeyMeta ? siteKeyMeta.getAttribute('content') : null;

// Lock object to track pending requests for specific URLs to prevent double-submissions
const pendingRequests = {};

/**
 * Shows a rate limit error message as a visible toast notification.
 * Creates a new toast element if needed, ensuring visibility regardless of page structure.
 * @param {string} title - The error title
 * @param {string} message - The error message
 */
function showRateLimitError(title, message) {
    // Create or get toast container
    let toast = document.getElementById('rate-limit-toast');

    if (!toast) {
        // Create toast element
        toast = document.createElement('div');
        toast.id = 'rate-limit-toast';
        toast.style.cssText = `
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #fff3cd;
            color: #856404;
            padding: 16px 20px;
            border-radius: 8px;
            border-left: 4px solid #ffc107;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
            z-index: 999999;
            font-family: inherit;
            max-width: 400px;
            min-width: 280px;
            text-align: left;
            display: none;
        `;
        document.body.appendChild(toast);
    }

    // Set toast content
    toast.innerHTML = `
        <div style="display: flex; align-items: flex-start; gap: 12px;">
            <div style="flex-shrink: 0; margin-top: 2px;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#856404" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
            </div>
            <div style="flex: 1;">
                <div style="font-weight: 600; font-size: 14px; margin-bottom: 4px; color: #664d03;">${title}</div>
                <div style="font-size: 13px; color: #856404;">${message}</div>
            </div>
            <button id="rate-limit-toast-close" style="
                background: transparent;
                border: none;
                color: #856404;
                font-size: 18px;
                cursor: pointer;
                padding: 0 4px;
                line-height: 1;
                opacity: 0.7;
            ">&times;</button>
        </div>
    `;

    // Show toast with animation
    toast.style.display = 'block';
    toast.style.opacity = '0';
    toast.style.transform = 'translateX(20px)';

    // Trigger animation
    requestAnimationFrame(() => {
        toast.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
        toast.style.opacity = '1';
        toast.style.transform = 'translateX(0)';
    });

    // Close button handler
    const closeBtn = document.getElementById('rate-limit-toast-close');
    if (closeBtn) {
        closeBtn.onclick = () => {
            hideRateLimitToast(toast);
        };
    }

    // Clear existing timeout if present
    if (toast.dataset.timeoutId) {
        clearTimeout(parseInt(toast.dataset.timeoutId));
    }

    // Auto-hide after 8 seconds
    const timeoutId = setTimeout(() => {
        hideRateLimitToast(toast);
    }, 8000);

    toast.dataset.timeoutId = timeoutId;
}

function hideRateLimitToast(toast) {
    if (toast && toast.style.display !== 'none') {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(20px)';

        if (toast.dataset.timeoutId) {
            clearTimeout(parseInt(toast.dataset.timeoutId));
            delete toast.dataset.timeoutId;
        }

        setTimeout(() => {
            toast.style.display = 'none';
        }, 300);
    }
}

window.callService = async function(url, payload = {}, useRecaptcha = true) {

    // Hide active rate-limit toasts on new service calls
    const existingToast = document.getElementById('rate-limit-toast');
    if (existingToast && existingToast.style.display !== 'none') {
        hideRateLimitToast(existingToast);
    }

    // 1. Frontend Rate Limiting / Debounce check
    if (pendingRequests[url]) {
        console.warn(`Request to ${url} is already in progress. Ignoring duplicate call.`);
        return Promise.reject(new Error("REQUEST_IN_PROGRESS"));
    }

    pendingRequests[url] = true;

    let recaptchaToken = null;
    const makeRequest = async (recaptchaToken) => {
        const requestData = {
            input: {
                ...(payload.input || {}),
                recaptchaToken: recaptchaToken,
            }
        };
        try {
            // Use API Security wrapper if available
            if (window.apiSecurity) {
                return await window.apiSecurity.post(url, requestData);
            }
            return await axios.post(url, requestData);
        } catch (e) {
            // 2. Handle 429 Too Many Requests
            if (e.response && e.response.status === 429) {
                const retryAfter = e.response.headers['retry-after'] || 60;
                console.error(`Rate limit exceeded. Retry after ${retryAfter} seconds.`);

                // Get translated messages (with fallback to English)
                const translations = window.translations || {};
                const title = translations.rate_limit_title || 'Too many requests';
                // Try to find specific translation for the backend error message
                const backendMessage = e.response.data?.response?.message;
                let message = translations[backendMessage] || translations.rate_limit_message || 'Please wait :seconds seconds and try again.';

                if (message.includes(':seconds')) {
                    message = message.replace(':seconds', retryAfter);
                }
                showRateLimitError(title, message);

                // Suppress generic front-end fallback alerts that try to display immediately after this error
                window.suppressRateLimitFallback = true;
                setTimeout(() => { window.suppressRateLimitFallback = false; }, 100);

                // Return a special error object that callers can check to avoid duplicate error handling
                const rateLimitError = new Error("RATE_LIMIT_EXCEEDED");
                rateLimitError.handled = true; // Mark as already handled
                throw rateLimitError;
            }

            // 3. Handle 403 Forbidden (Security Block)
            if (e.response && e.response.status === 403) {
                console.warn("Security check failed:", e.response.data);

                // Get translated messages
                const translations = window.translations || {};
                // Default to "Access Denied" if no translation found
                const title = translations.security_error_title || 'Access Denied';

                // Try to find specific translation for the backend error message code
                const backendMessage = e.response.data?.response?.message;
                // Default to generic message if specific code translation not found
                const message = translations[backendMessage] || translations.security_error_message || 'Your request was blocked for security reasons.';

                // Show user-friendly alert using the same toast system as rate limits
                showRateLimitError(title, message);

                // Suppress generic front-end fallback alerts that try to display immediately after this error
                window.suppressRateLimitFallback = true;
                setTimeout(() => { window.suppressRateLimitFallback = false; }, 100);

                // Return a special error object that callers can check to avoid duplicate error handling
                const securityError = new Error("SECURITY_CHECK_FAILED");
                securityError.handled = true;
                throw securityError;
            }

            console.error("Error during API call:", e);
            if (e.response && e.response.status === 419) {
                console.warn("CSRF token expired, reloading the page...");
                location.reload();
            } else {
                throw e;
            }
        }
    };

    try {
    if (useRecaptcha && siteKey) {
            return await new Promise((resolve, reject) => {
                if (typeof grecaptcha === 'undefined' || !grecaptcha.enterprise) {
                    console.warn("Recaptcha is enabled but script is not loaded or not ready.");
                    reject(new Error("RECAPTCHA_SCRIPT_MISSING"));
                    return;
                }
                grecaptcha.enterprise.ready(async () => {
                    try {
                        recaptchaToken = await grecaptcha.enterprise.execute(siteKey, { action: 'validate_captcha' });
                        const result = await makeRequest(recaptchaToken);
                        resolve(result);
                    } catch (e) {
                        if (e.message === "RATE_LIMIT_EXCEEDED" || e.message === "REQUEST_IN_PROGRESS") {
                            reject(e);
                        } else {
                        console.error("Failed to execute reCAPTCHA", e);
                        reject(new Error("RECAPTCHA_FAILED"));
                    }
                    }
                });
            });
    } else {
        return await makeRequest(null);
    }
    } catch (e) {
        throw e;
    } finally {
        delete pendingRequests[url];
    }
}


document.addEventListener("DOMContentLoaded", () => {
    // SQL injection və digər təhlükəsizlik pattern-ləri
    const sqlInjectionPattern = /['"`;\\]|--|\/\*|[*%#=?><~`^()[\]{}]/g;
    // Bütün text və email input-ları böyük hərflərə çevir (istisna: .notUppercase)
    const inputsToUppercase = document.querySelectorAll("input[type='text']:not(.notUppercase), input[type='email']:not(.notUppercase)");
    inputsToUppercase.forEach((input) => {
        input.addEventListener("input", () => {
            input.value = input.value.toUpperCase();
        });
    });
    // Ümumi input-lar üçün SQL injection təmizləmə və "ı"/"I" silinməsi
    const inputsForInj = document.querySelectorAll("input:not(.noSqlSanitize):not(.onlyLegalName):not(.onlyForAddress)");
    inputsForInj.forEach((input) => {
        input.addEventListener("input", () => {
            let sanitized = input.value;
            sanitized = sanitized.replace(sqlInjectionPattern, "");
            sanitized = sanitized.replace(/ı/g, ""); // Task2
            input.value = sanitized;
        });
    });
    // Yalnız hərf və rəqəm qəbul edən input-lar
    const letterNumberInputs = document.querySelectorAll("input.onlyLettersNumbers");
    letterNumberInputs.forEach((input) => {
        input.addEventListener("input", () => {
            input.value = input.value.replace(/[^a-zA-Z0-9]/g, '');
        });
    });
    // maxLength class-ı varsa tətbiq et
    inputsForInj.forEach((input) => {
        const classList = Array.from(input.classList);
        const maxLengthClass = classList.find(cls => cls.startsWith('maxLength-'));
        if (maxLengthClass) {
            const max = parseInt(maxLengthClass.split('-')[1], 10);
            input.addEventListener("input", () => {
                if (input.value.length > max) {
                    input.value = input.value.slice(0, max);
                }
            });
        }
    });
    // Yalnız ad üçün (hərf və boşluq)
    const onlyNameInputs = document.querySelectorAll("input.onlyName");
    onlyNameInputs.forEach((input) => {
        input.addEventListener("input", () => {
            let value = input.value;
            value = value.replace(/[^a-zA-Z\s]/g, '');
            value = value.replace(/\s{2,}/g, ' ');
            value = value.replace(/^\s+/, '');
            input.value = value;
        });
    });
    // Ünvan üçün — bütün simvollara icazə verilir, sadəcə boşluqları tənzimləyək
    const addressInputs = document.querySelectorAll('input.onlyForAddress');
    addressInputs.forEach((input) => {
        input.addEventListener('input', () => {
            let value = input.value;
            value = value.replace(/\s{2,}/g, ' ');
            value = value.replace(/^\s+/, '');
            value = value.replace(/[^A-Za-z0-9\s\p{P}\p{S}]/gu, '');
            input.value = value;
        });
    });
    // Email input-lar üçün yalnız İngilis simvollarına icazə ver
    const emailInputs = document.querySelectorAll("input[type='email']");
    emailInputs.forEach((input) => {
        input.addEventListener("input", () => {
            let value = input.value;
            // Yalnız ingilis hərfləri, rəqəmlər və email üçün vacib simvollar
            value = value.replace(/[^a-zA-Z0-9@._\-]/g, '');
            //  "ı" və "I" sil
            value = value.replace(/ı/g, "");
            input.value = value;
        });
    });
    // Hüquqi ad üçün (Legal name): hərflər, rəqəmlər, boşluq və " , & ( ) . - +
    const legalNameInputs = document.querySelectorAll("input.onlyLegalName");
    legalNameInputs.forEach((input) => {
        input.addEventListener("input", () => {
            let value = input.value;
            // yalnız icazə verilən simvolları saxla
            value = value.replace(/[^a-zA-Z0-9\s"&().\-+]/g, '');
            // iki boşluqdan çox olmasın
            value = value.replace(/\s{2,}/g, ' ');
            // başlanğıc boşluq silinsin
            value = value.replace(/^\s+/, '');
            // Task2: “ı” və “I” silinsin
            value = value.replace(/ı/g, "");
            input.value = value;
        });
    });
    const radioButtons = document.querySelectorAll("input[type='radio']");
    radioButtons.forEach((radio) => {
        radio.addEventListener("change", () => {
            if (radio.name === "insurance-period") return;
            const parentForm = radio.closest(".inputs");
            if (parentForm) {
                const inputs = parentForm.querySelectorAll("input");
                inputs.forEach((input) => {
                    const isInsideRadioGroupInsurance = input.closest(".radio-group-insurance");
                    const isRadio = input.type === "radio";
                    if (isInsideRadioGroupInsurance && isRadio) return;
                    input.value = "";
                });

                const dropdowns = parentForm.querySelectorAll(".dropdown__input_hidden");
                dropdowns.forEach((dropdown) => {
                    dropdown.value = "";
                });

                const dropdownButtons = parentForm.querySelectorAll(".dropdown__button span");
                dropdownButtons.forEach((button) => {
                    button.textContent = "Select";
                });
            }
        });
    });



    const urlParams = new URLSearchParams(window.location.search);
    const paymentCode = urlParams.get("payment-code");

    const modal = document.querySelector(".modal-payment");
    const overlay = document.querySelector(".overlay");

    if (!modal) return;

    if (paymentCode) {
        modal.classList.add("active");
        overlay?.classList.add("active");

        const invoiceRadio = modal.querySelector("input[name='searchType'][value='invoice']");
        const plateRadio = modal.querySelector("input[name='searchType'][value='plate']");
        invoiceRadio.checked = true;
        plateRadio.checked = false;

        const invoiceGroup = modal.querySelector("#invoice-search-group");
        const plateGroup = modal.querySelector("#plate-search-group");
        const modalBottom = modal.querySelector("#modalBottom");
        const finalSearchBtn = modal.querySelector("#final-search-button");

        invoiceGroup.classList.add("active");
        plateGroup.classList.remove("active");
        modalBottom.style.display = "flex";
        finalSearchBtn.style.display = "block";

        const codeInput = modal.querySelector("#code");
        if (codeInput) codeInput.value = paymentCode;

        const cleanUrl = window.location.origin + window.location.pathname;
        window.history.replaceState({}, document.title, cleanUrl);
    }
});

window.showAlert = (alertElement, titleText, messages = [], type = "error", iconPath = null) => {
    // Don't show generic fallback alerts if they are fired synchronously from a rate-limit or security block
    if (window.suppressRateLimitFallback) {
        return;
    }

    alertElement.style.display = "none";
    const alertContainer = alertElement.querySelector(".alert-right");
    alertContainer.innerHTML = "";

    alertElement.classList.remove("alert-error", "alert-success", "alert-info");
    alertElement.classList.add(`alert-${type}`);
    alertElement.style.position = "relative";

    if (iconPath) {
        const iconImg = alertElement.querySelector(".alert-left .icon img");
        if (iconImg) {
            iconImg.setAttribute("src", iconPath);
        }
    }

    let closeBtn = alertElement.querySelector(".alert-close-btn");
    if (!closeBtn) {
        closeBtn = document.createElement("button");
        closeBtn.innerHTML = "&times;";
        closeBtn.classList.add("alert-close-btn");
        closeBtn.style.cssText = `
        background: transparent;
        border: none;
        font-size: 24px;
        color: red;
        position: absolute;
        top: 0px;
        right: 12px;
        cursor: pointer;
        z-index: 10;
    `;
        alertElement.appendChild(closeBtn);
    }
    closeBtn.onclick = () => {
        alertElement.style.display = "none";
    };
    const title = document.createElement("h4");
    title.classList.add("alert-title");
    title.textContent = titleText;
    alertContainer.appendChild(title);
    messages.forEach(text => {
        const p = document.createElement("p");
        p.classList.add("alert-text");
        p.textContent = text;
        alertContainer.appendChild(p);
    });
    alertElement.style.display = "flex";
};


const langSwitcher = document.querySelectorAll('.lang-switcher');
const langBtn = document.querySelectorAll('.lang-btn');

langBtn.forEach(btn => {
    btn.addEventListener('click', function (e) {
        e.preventDefault();
        langSwitcher.forEach(switcher => {
            switcher.classList.toggle('open');
        });
    });
})

window.addEventListener('click', function (e) {
    if (!e.target.closest('.lang-switcher') && !e.target.closest('.lang-btn')) {
        langSwitcher.forEach(switcher => {
            switcher.classList.remove('open');
        });
    }
});

async function generatePDFFromModal(modalSelector) {
    const modal = document.querySelector(modalSelector);
    if (!modal) return;

    if (typeof window.showLoader === 'function') {
        window.showLoader();
    }

    try {
        let jsPDF, QRCode, plusJakartaSansBase64;
        try {
            const [pdfModule, qrModule, fontModule] = await Promise.all([
                import('jspdf'),
                import('qrcode'),
                import('./plusJakartaSans.js')
            ]);
            jsPDF = pdfModule.jsPDF || pdfModule.default?.jsPDF || pdfModule.default || pdfModule;
            QRCode = qrModule.default?.toDataURL ? qrModule.default : qrModule;
            plusJakartaSansBase64 = fontModule.plusJakartaSansBase64 || fontModule.default?.plusJakartaSansBase64;
        } catch (error) {
            console.error("Failed to load PDF dependencies:", error);
            return;
        }

    const modalItems = modal.querySelectorAll(".modal-list .modal-item");
    const dataMap = {};
    modalItems.forEach((item) => {
        const titleEl = item.querySelector(".modal-item-title");
        const contentEl = item.querySelector(".modal-item-content");
        if (!titleEl || !contentEl) return;
        const label = titleEl.textContent.replace(":", "").trim();
        dataMap[label] = contentEl.textContent.trim();
    });

    let pdfTitle = ''
    const pdfInfoLabel = document.getElementById("pdf-info-text")?.textContent?.trim() || "Məlumat";
    let pdfFooter = document.getElementById("pdf-footer-text")?.textContent?.trim() || "";

    let redirectUrl = "";
    let identifier = "payment";
    let identifierName = "payment-info";
    let fH=9

    if (modalSelector.includes("pay-payment-modal")) {
        pdfTitle = document.getElementById("pdf-title-text")?.textContent?.trim();
        const paymentEl = modal.querySelector(".invoice-number");
        const paymentCode = paymentEl ? paymentEl.textContent.trim() : "";
        redirectUrl = `${window.location.origin}?payment-code=${encodeURIComponent(paymentCode)}`;
        identifier = paymentCode || "payment";
        fH=6
    }
    else if (modalSelector.includes("vehicleModal")) {
        pdfFooter = document.getElementById("pdf-footer-text2")?.textContent?.trim() || "";
        pdfTitle = document.querySelector(".modal-title")?.textContent?.trim();
        const plateEl = modal.querySelector(".plate-number");
        const plate = plateEl ? plateEl.textContent.trim() : "";
        redirectUrl = `${window.location.origin}?plate-number=${encodeURIComponent(plate)}`;
        identifier = plate || "vehicle";
        identifierName = "policy-info";
    }

    const qrDataUrl = await QRCode.toDataURL(redirectUrl, { width: 150 });

    const doc = new jsPDF({ unit: "mm", format: "a4" });
    const pageWidth = doc.internal.pageSize.getWidth();
    const pageHeight = doc.internal.pageSize.getHeight();
    const leftMargin = 12;

    doc.addFileToVFS("PlusJakartaSans-Medium.ttf", plusJakartaSansBase64);
    doc.addFont("PlusJakartaSans-Medium.ttf", "PlusJakartaSans", "normal");
    doc.setFont("PlusJakartaSans");

    const logoWidth = 45;
    const logoHeight = 25;
    const headerTop = 20;
    const rightMargin = 12;

// Loqo (sağda)
    try {
        doc.addImage("/images/tpllogopdf.png", "PNG", pageWidth - logoWidth - rightMargin, headerTop, logoWidth, logoHeight);
        } catch (e) { console.warn("Logo fail", e); }

// Başlıq
    doc.setFontSize(24);
    doc.setTextColor(30, 30, 30);

// Loqoya qədər olan maksimum yazı eni
    const titleMaxWidth = pageWidth - logoWidth - leftMargin - rightMargin - 10;

// Başlıq uzun olsa, sətirlərə böl
    const titleLines = doc.splitTextToSize(pdfTitle, titleMaxWidth);

// Başlığın dikey ortalanması (logo ilə eyni səviyyədə)
    const titleY = headerTop + logoHeight / 2 + (titleLines.length > 1 ? -3 : 3);

// Yazını çək
    doc.text(titleLines, leftMargin, titleY);

    const infoBarTop = headerTop + logoHeight + 12;
    doc.setFillColor(232, 249, 252);
    doc.rect(0, infoBarTop, pageWidth, 10, "F");
    doc.setTextColor(0, 167, 180);
    doc.setFontSize(14);
    doc.text(pdfInfoLabel, leftMargin, infoBarTop + 7);

    let y = infoBarTop + 18;
    const lineGap = 12;
    doc.setTextColor(40, 40, 40);
    doc.setFontSize(12);
    Object.entries(dataMap).forEach(([label, value]) => {
        doc.text(`${label}:`, leftMargin, y);
        doc.text(value, pageWidth - leftMargin, y, { align: "right" });
        doc.setDrawColor(200);
        doc.line(leftMargin, y + 2, pageWidth - leftMargin, y + 2);
        y += lineGap;
    });

    const qrSize = 40;
    doc.addImage(qrDataUrl, "PNG", pageWidth - qrSize - leftMargin, pageHeight - 80, qrSize, qrSize);

    doc.setTextColor(140, 210, 215);
    doc.setFontSize(10);
    doc.text("@ tpl.az", leftMargin, pageHeight - 45);

    doc.setTextColor(255, 255, 255);
    doc.setFontSize(9);
    const footerLines = doc.splitTextToSize(pdfFooter, pageWidth - leftMargin * 2);
    const footerLineHeight = 4;
    const minFooterHeight = 22;
    const footerHeight = Math.max(minFooterHeight, footerLines.length * footerLineHeight + 6);
    doc.setFillColor(0, 167, 180);
    doc.rect(0, pageHeight - footerHeight, pageWidth, footerHeight, "F");
    let fy = pageHeight - footerHeight + fH;
    footerLines.forEach((t) => {
        doc.text(t, pageWidth / 2, fy, { align: "center" });
        fy += footerLineHeight;
    });


    doc.save(`${identifierName}-${identifier}.pdf`);
    } catch (err) {
        console.error("Error generating PDF:", err);
    } finally {
        if (typeof window.hideLoader === 'function') {
            window.hideLoader();
        }
    }
}
window.generatePDFFromModal = generatePDFFromModal;


