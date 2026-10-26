import Keyboard from "simple-keyboard";
import "simple-keyboard/build/css/index.css";

export class VirtualKeyboard {
    constructor() {
        this.keyboard = null;
        this.activeInput = null;
        this.wrapper = null;
        this.lastMousedownTarget = null;
        this.init();
    }

    getKeyboardLayouts() {
        const bottomRow = "{lang} {space}";

        const layouts = {
            en: {
                default: [
                    "` 1 2 3 4 5 6 7 8 9 0 - = {bksp}",
                    "Q W E R T Y U I O P [ ] \\",
                    "A S D F G H J K L ; ' {enter}",
                    "{shift} Z X C V B N M , . / {shift}",
                    bottomRow
                ],
                shifted: [
                    "~ ! @ # $ % ^ & * ( ) _ + {bksp}",
                    "q w e r t y u i o p { } |",
                    "a s d f g h j k l : \" {enter}",
                    "{shift} z x c v b n m < > ? {shift}",
                    bottomRow
                ]
            },
            az: {
                default: [
                    "` 1 2 3 4 5 6 7 8 9 0 - = {bksp}",
                    "Q Ü E R T Y U İ O P Ö Ğ",
                    "A S D F G H J K L I Ə {enter}",
                    "{shift} Z X C V B N M Ç Ş , . {shift}",
                    bottomRow
                ],
                shifted: [
                    "~ ! @ # $ % ^ & * ( ) _ + {bksp}",
                    "q ü e r t y u i o p ö ğ",
                    "a s d f g h j k l ı ə {enter}",
                    "{shift} z x c v b n m ç ş < > {shift}",
                    bottomRow
                ]
            },
            ru: {
                default: [
                    "Ё 1 2 3 4 5 6 7 8 9 0 - = {bksp}",
                    "Й Ц У К Е Н Г Ш Щ З Х Ъ \\",
                    "Ф Ы В А П Р О Л Д Ж Э {enter}",
                    "{shift} Я Ч С М И Т Ь Б Ю . , {shift}",
                    bottomRow
                ],
                shifted: [
                    "ё ! \" № ; % : ? * ( ) _ + {bksp}",
                    "й ц у к е н г ш щ з х ъ /",
                    "ф ы в а п р о л д ж э {enter}",
                    "{shift} я ч с м и т ь б ю , . {shift}",
                    bottomRow
                ]
            },
            tr: {
                default: [
                    "\" 1 2 3 4 5 6 7 8 9 0 * - {bksp}",
                    "Q W E R T Y U I O P Ğ Ü",
                    "A S D F G H J K L Ş İ {enter}",
                    "{shift} Z X C V B N M Ö Ç . , {shift}",
                    bottomRow
                ],
                shifted: [
                    "é ! ' ^ + % & / ( ) = ? _ {bksp}",
                    "q w e r t y u ı o p ğ ü",
                    "a s d f g h j k l ş i {enter}",
                    "{shift} z x c v b n m ö ç : ; {shift}",
                    bottomRow
                ]
            },
            ka: {
                default: [
                    "„ 1 2 3 4 5 6 7 8 9 0 - = {bksp}",
                    "ქ წ ე რ ტ ყ უ ი ო პ ა [ ] \\",
                    "შ ს დ ფ გ ჰ ჯ კ ლ ; ' {enter}",
                    "{shift} ზ ხ ც ვ ბ ნ მ , . / {shift}",
                    bottomRow
                ],
                shifted: [
                    "“ ! @ # $ % ^ & * ( ) _ + {bksp}",
                    "Ქ Წ Ე Რ Ტ Ყ Უ Ი Ო Პ Ა { } |",
                    "Შ Ს Დ Ფ Გ Ჰ Ჯ Კ Ლ : \" {enter}",
                    "{shift} Ზ Ხ Ც Ვ Ბ Ნ Მ < > ? {shift}",
                    bottomRow
                ]
            },
            fa: {
                default: [
                    "‍ ۱ ۲ ۳ ۴ ۵ ۶ ۷ ۸ ۹ ۰ - = {bksp}",
                    "ض ص ث ق ف غ ع ه خ ح ج چ \\",
                    "ش س ی ب ل ا ت ن م ک گ {enter}",
                    "{shift} ظ ط ز ر ذ د پ و . / {shift}",
                    bottomRow
                ],
                shifted: [
                    "~ ! @ # $ % ^ & * ) ( _ + {bksp}",
                    "ٌ ٍ ً ُ ِ َ ّ ۀ ة ي ؤ إ أ",
                    "ؤ إ أ آ ة ي ّ َ ِ ُ ً ٍ {enter}",
                    "{shift} ظ ط ز ر ذ د پ و . / {shift}",
                    bottomRow
                ]
            }
        };

        // Add numeric layout which is common across all
        Object.keys(layouts).forEach(key => {
            layouts[key].numeric = [
                "1 2 3",
                "4 5 6",
                "7 8 9",
                "0 {bksp}",
                "{space}"
            ];
        });

        return layouts;
    }

    init() {
        this.createContainer();

        this.layouts = this.getKeyboardLayouts();
        // Default language is en because inputs only allow english chars
        this.currentLang = 'en';

        this.keyboard = new Keyboard({
            onChange: input => this.onChange(input),
            onKeyPress: button => this.onKeyPress(button),
            preventMouseDownDefault: true,
            layout: this.layouts[this.currentLang],
            display: {
                "{bksp}": "⌫",
                "{enter}": "return",
                "{shift}": "⇧",
                "{space}": "<span class='space-icon'>␣</span>",
                "{close}": "<span class='close-icon'>✕</span>",
                "{lang}": `🌐 ${this.currentLang.toUpperCase()}`
            },
            theme: "hg-theme-default hg-layout-default myTheme",
            buttonTheme: [
                {
                    class: "hg-action",
                    buttons: "{enter} {bksp} {lang}"
                }
            ]
        });

        this.bindEvents();
    }

    createContainer() {
        this.wrapper = document.createElement('div');
        this.wrapper.className = 'virtual-keyboard-wrapper hidden';

        // Stop events from bubbling to document so outside-click detectors (like intl-tel-input dropdown)
        // do not trigger and close the search input
        // ALSO we must preventDefault so that touching the keyboard on kiosks/mobile doesn't steal focus from the input!
        ['mousedown', 'touchstart', 'pointerdown'].forEach(evt => {
            this.wrapper.addEventListener(evt, e => {
                e.stopPropagation();
                e.preventDefault();
            });
        });

        ['mouseup', 'touchend', 'pointerup', 'click'].forEach(evt => {
            this.wrapper.addEventListener(evt, e => e.stopPropagation());
        });

        // We put simple-keyboard directly inside
        const keyboardDiv = document.createElement('div');
        keyboardDiv.className = 'simple-keyboard';
        this.wrapper.appendChild(keyboardDiv);

        document.body.appendChild(this.wrapper);
    }

    bindEvents() {
        ['mousedown', 'touchstart'].forEach(evt => {
            document.addEventListener(evt, (e) => {
            this.lastMousedownTarget = e.target;
        }, true);
        });

        document.addEventListener('focusin', (e) => {
            const target = e.target;

            if (this.isKeyboardInput(target)) {
                // SPECIAL CASE: For dropdown search inputs, ONLY show keyboard if it was a user click.
                // This prevents the keyboard from showing up automatically when a dropdown opens.
                const isDropdownSearch = target.classList.contains('dropdown-search-input');
                if (isDropdownSearch) {
                    const clickedWithinWrapper = target.closest('.dropdown-search-input-wrapper')?.contains(this.lastMousedownTarget);
                    if (!clickedWithinWrapper && this.lastMousedownTarget !== target) {
                        return;
                    }
                }

                this.activeInput = target;

                let layoutName = this.getLayoutForInput(target);

                this.keyboard.setInput(target.value);
                if (typeof target.selectionStart === 'number' && typeof this.keyboard.setCaretPosition === 'function') {
                    this.keyboard.setCaretPosition(target.selectionStart);
                }
                this.keyboard.setOptions({
                    layoutName: layoutName
                });

                this.show();
            } else {
                // If focusing something else (like a select, or button, or excluded input)
                // and it's NOT part of the keyboard wrapper, hide the keyboard.

                // Don't hide if focus moves to the intl-tel-input dropdown/container (e.g. while searching)
                if (target.closest('.iti, .iti__country-list, .iti--container, .iti__dropdown-content')) {
                    return;
                }

                if (this.wrapper && !this.wrapper.contains(target)) {
                    this.hide();
                }
            }
        });

        // Global click listener to handle clicks on non-focusable elements (like background)
        document.addEventListener('mousedown', (e) => {
            const target = e.target;

            // 1. If clicking inside keyboard, let it handle normally
            if (this.wrapper.contains(target) || target.closest('.virtual-keyboard-wrapper')) {
                return;
            }

            // 2. If clicking on the active input, don't hide
            if (this.activeInput && (this.activeInput === target || this.activeInput.contains(target))) {
                return;
            }

            // 3. If clicking on another input that should trigger the keyboard, don't hide
            // (Let focusin handle it to avoid flicker)
            if (this.isKeyboardInput(target)) {
                return;
            }

            // 4. Don't hide if clicking something that is part of the search dropdown UI
            if (target.closest('.dropdown__list') || target.closest('.dropdown-search-input-wrapper')) {
                return;
            }

            // 5. Don't hide if clicking something that is part of the intl-tel-input
            if (target.closest('.iti, .iti__country-list, .iti--container, .iti__dropdown-content')) {
                return;
            }

            // For any other click outside keyboard and input, hide keyboard immediately
            // This allows the click to propagate and work on first attempt
            if (!this.wrapper.classList.contains('hidden')) {
                this.hide();
            }
        }, true);

        document.addEventListener('input', (e) => {
            if (e.target === this.activeInput) {
                this.keyboard.setInput(e.target.value);
                if (typeof e.target.selectionStart === 'number' && typeof this.keyboard.setCaretPosition === 'function') {
                    this.keyboard.setCaretPosition(e.target.selectionStart);
                }
            }
        });

        // Sync simple-keyboard caret when user clicks or moves cursor inside the input
        document.addEventListener('selectionchange', () => {
            if (this.activeInput && document.activeElement === this.activeInput) {
                if (typeof this.activeInput.selectionStart === 'number' && typeof this.keyboard.setCaretPosition === 'function') {
                    this.keyboard.setCaretPosition(this.activeInput.selectionStart);
                }
            }
        });

        // Sync keyboard state if external buttons alter the input value directly without firing 'input'
        ['mouseup', 'touchend', 'click'].forEach(eventType => {
            document.addEventListener(eventType, () => {
                // Set a small timeout so native clear (x) button operations finish first
                setTimeout(() => {
                    if (this.activeInput) {
                        const val = this.activeInput.value || '';
                        if (val !== this.keyboard.getInput()) {
                            this.keyboard.setInput(val);
                        }
                    }
                }, 50);
            }, { passive: true });
        });
    }

    isKeyboardInput(target) {
        if (!target || !target.tagName) return false;

        const isInput = target.tagName === 'INPUT' || target.tagName === 'TEXTAREA';
        if (!isInput) return false;

        const ignoreTypes = ['hidden', 'radio', 'checkbox', 'file', 'button', 'submit', 'reset', 'time', 'date', 'datetime-local'];
        const ignoreIds = ['birthday'];

        // Support intl-tel-input search field
        if (target.classList.contains('iti__search-input')) {
            return true;
        }

        return !target.disabled &&
            !target.readOnly &&
            !ignoreTypes.includes(target.type) &&
            (!target.id || !ignoreIds.includes(target.id));
    }

    getLayoutForInput(input) {
        if (input.type === 'number' || input.type === 'tel' || input.inputMode === 'numeric') {
            return 'numeric';
        }
        return 'default';
    }

    onChange(input) {
        if (this.activeInput) {
            let nextCaret = this.keyboard.caretPosition;

            // For tel inputs, strictly enforce country code without caretaker drift
            if (this.activeInput.type === 'tel') {
                const itiInstance = intlTelInput?.getInstance(this.activeInput);
                if (itiInstance) {
                    const countryData = itiInstance.getSelectedCountryData();
                    if (countryData && countryData.dialCode) {
                        const dialCode = countryData.dialCode;
                        const prefix = '+' + dialCode;

                        // 1. If input is completely empty (e.g., cleared by X button), force prefix
                        if (input.trim() === '') {
                            input = prefix;
                            nextCaret = input.length;
                        }
                        // 2. If the user backspaced into the prefix itself (e.g., "+99", "+9", "+"), restore prefix
                        else if (prefix.startsWith(input.trim())) {
                            input = prefix;
                            nextCaret = input.length;
                        }
                        // 3. If input doesn't start with prefix (e.g., corrupted, pasted number without +)
                        else if (!input.startsWith(prefix)) {
                            let raw = input.replace(/\+/g, '').replace(/^\s+/, '');
                            if (raw.startsWith(dialCode)) {
                                raw = raw.substring(dialCode.length).replace(/^\s+/, '');
                            }
                            input = raw.length > 0 ? `${prefix} ${raw}` : prefix;
                            nextCaret = input.length;
                        }

                        this.keyboard.setInput(input);
                    }
                }
            }

            this.activeInput.value = input;

            if (nextCaret !== null && typeof this.activeInput.setSelectionRange === 'function') {
                try {
                    this.activeInput.setSelectionRange(nextCaret, nextCaret);
                    if (typeof this.keyboard.setCaretPosition === 'function') {
                        this.keyboard.setCaretPosition(nextCaret);
                    }
                } catch(e) {}
            }

            this.activeInput.dispatchEvent(new Event('input', { bubbles: true }));

            // Avoid change event on search inputs to prevent immediate dropdown closing
            if (!this.activeInput.classList.contains('iti__search-input') && !this.activeInput.classList.contains('dropdown-search-input')) {
            this.activeInput.dispatchEvent(new Event('change', { bubbles: true }));
        }
    }
    }

    onKeyPress(button) {
        if (button === "{shift}") {
            this.handleShift('{shift}');
        } else if (button === "{close}") {
            this.hide();
        } else if (button === "{lang}") {
            this.handleLangToggle();
        } else if (this.activeInput && this.activeInput.getAttribute('type') === 'email') {
            // Option to force lower case on emails could go here, but omitted to prevent auto-swapping
        }
    }

    handleLangToggle() {
        const langs = ['en', 'az', 'ru', 'tr', 'ka', 'fa'];
        let currentIndex = langs.indexOf(this.currentLang);
        let nextIndex = (currentIndex + 1) % langs.length;
        this.currentLang = langs[nextIndex];

        // Ensure we preserve shift state (layoutName) if currently open in numeric/shifted
        const currentLayoutName = this.keyboard.options.layoutName;

        this.keyboard.setOptions({
            layout: this.layouts[this.currentLang],
            layoutName: currentLayoutName,
            display: {
                "{bksp}": "⌫",
                "{enter}": "return",
                "{shift}": "⇧",
                "{space}": "<span class='space-icon'>␣</span>",
                "{close}": "<span class='close-icon'>✕</span>",
                "{lang}": `🌐 ${this.currentLang.toUpperCase()}`
            }
        });
    }

    handleShift(layout = null) {
        const currentLayout = this.keyboard.options.layoutName;
        if (currentLayout === 'numeric') return;

        let newLayout = currentLayout === 'default' ? 'shifted' : 'default';

        if (layout === '{shift}') {
            newLayout = currentLayout === 'default' ? 'shifted' : 'default';
        } else if (layout) {
            newLayout = layout;
        }

        this.keyboard.setOptions({ layoutName: newLayout });
    }

    show() {
        this.wrapper.classList.remove('hidden');
        document.body.classList.add('keyboard-open');

        if (this.activeInput) {
            // Wait for keyboard to show and layout to settle
            setTimeout(() => {
                const inputRect = this.activeInput.getBoundingClientRect();
                const keyboardHeight = this.wrapper.offsetHeight;
                const viewportHeight = window.innerHeight;

                // If it's a dropdown search input, we need much larger margin to fit the dropdown list (which is up to ~250px height)
                const isDropdown = this.activeInput.classList.contains('dropdown-search-input');
                const margin = isDropdown ? 260 : 100;

                // Keyboard is fixed at bottom: [viewportHeight - keyboardHeight, viewportHeight]
                const keyboardTop = viewportHeight - keyboardHeight;

                // If input + needed space is covered by keyboard or too close
                if (inputRect.bottom + margin > keyboardTop) {
                    const scrollAmount = (inputRect.bottom + margin) - keyboardTop;
                    window.scrollBy({
                        top: scrollAmount,
                        behavior: 'smooth'
                    });
                }

                // Also ensure top is visible
                if (inputRect.top < margin && !isDropdown) {
                    this.activeInput.scrollIntoView({ behavior: 'smooth', block: 'start' });
                } else if (inputRect.top < 20 && isDropdown) {
                    this.activeInput.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }, 150);
        }
    }

    hide() {
        this.wrapper.classList.add('hidden');

        // Delay removing the class that causes layout shift (padding-bottom)
        // This ensures that if the user clicked a button, the button doesn't move
        // before the 'click' event is fired.
        setTimeout(() => {
            if (this.wrapper.classList.contains('hidden')) {
                document.body.classList.remove('keyboard-open');
            }
        }, 400);
    }
}
