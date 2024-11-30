/**
 * cslice-gtranslate.js script
 * use: <p class="cslice-gtranslate-wrapper globe globe-white"></p>
 */

const gtranslateSwitchers = new Set();

function getCookieLang() {
    return document.cookie
        .split('; ')
        .find(row => row.startsWith('googtrans'))
        ?.split('/')[2] || csliceGTranslate.defaultLang;
}

document.addEventListener('DOMContentLoaded', () => {
    const wrappers = document.querySelectorAll('.cslice-gtranslate-wrapper');
    if (!wrappers.length) return;

    const currentLang = getCookieLang();

    wrappers.forEach((wrapper, index) => {
        if (wrapper.tagName.toLowerCase() === 'p') {
            wrapper.textContent = '';
        }

        const switcher = document.createElement('select');
        switcher.className = 'cslice-language-switcher';
        switcher.setAttribute('aria-label', 'Select Language');
        switcher.innerHTML = Object.entries(csliceGTranslate.languages)
            .map(([code, name]) => `<option value="${code}">${name}</option>`)
            .join('');

        if (wrapper.classList.contains('wp-block-outermost-icon-block')) {
            wrapper.querySelector('.icon-container').dataset.lang = currentLang;
        }

        gtranslateSwitchers.add(switcher);
        switcher.value = currentLang;

        if (index === 0 && currentLang !== csliceGTranslate.defaultLang) {
            loadGoogleTranslate();
        }

        switcher.addEventListener('change', (event) => {
            const lang = event.target.value;
            switcher.disabled = true;

            if (wrapper.classList.contains('wp-block-outermost-icon-block')) {
                wrapper.querySelector('.icon-container').dataset.lang = lang;
            }

            gtranslateSwitchers.forEach(s => {
                if (s !== switcher) s.value = lang;
            });

            if (!window.google?.translate) {
                loadGoogleTranslate(() => changeLanguage(lang));
            } else {
                changeLanguage(lang);
            }
            gtranslateSwitchers.forEach(s => s.disabled = false);
        });

        wrapper.appendChild(switcher);
        if (index === 0) {
            const translate = document.createElement('div');
            translate.id = 'google_translate_element';
            translate.style.display = 'none';
            wrapper.appendChild(translate);
        }
    });
});

function loadGoogleTranslate(callback) {
    if (window.googleTranslateElementInit) {
        callback?.();
        return;
    }

    window.googleTranslateElementInit = () => {
        new google.translate.TranslateElement({
            pageLanguage: csliceGTranslate.defaultLang,
            includedLanguages: csliceGTranslate.includedLanguages,
            autoDisplay: false,
        }, 'google_translate_element');
        setTimeout(callback, 1000);
    };

    const script = document.createElement('script');
    script.src = `${csliceGTranslate.apiUrl}?cb=googleTranslateElementInit`;
    script.defer = true;
    document.head.appendChild(script);
}

function changeLanguage(lang) {
    const translate = document.querySelector('.goog-te-combo');
    if (translate) {
        translate.value = lang;
        translate.dispatchEvent(new Event('change', { bubbles: true }));
    }
}
