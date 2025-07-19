// --- Language Switcher Initialization ---
function initLanguageSwitcher() {
    const languageSwitcher = document.querySelector('.language-switcher');
    const currentLangBtn = document.querySelector('.current-lang');
    const langOptions = document.querySelectorAll('.lang-option');

    if (languageSwitcher && currentLangBtn) {
        // Remove any previous event listeners (avoid duplicate toggling)
        const newCurrentLangBtn = currentLangBtn.cloneNode(true);
        currentLangBtn.parentNode.replaceChild(newCurrentLangBtn, currentLangBtn);

        newCurrentLangBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            languageSwitcher.classList.toggle('active');
        });

        // Close dropdown when clicking outside
        if (!document.body._langSwitcherClickHandler) {
            document.body._langSwitcherClickHandler = function(e) {
                if (!languageSwitcher.contains(e.target)) {
                    languageSwitcher.classList.remove('active');
                }
            };
            document.addEventListener('click', document.body._langSwitcherClickHandler);
        }
    }

    // Remove possible duplicate handlers on lang-options
    langOptions.forEach(option => {
        const newOption = option.cloneNode(true);
        option.parentNode.replaceChild(newOption, option);

        newOption.addEventListener('click', function(e) {
            e.preventDefault();
            const lang = this.getAttribute('href').split('=')[1];
            switchLanguage(lang);
            languageSwitcher.classList.remove('active');
        });
    });
}

// GET CURRENT LANGUAGE
function getCurrentLanguage() {
    const urlParams = new URLSearchParams(window.location.search);
    const lang = urlParams.get('lang');
    return lang && translations[lang] ? lang : 'nl';
}

// UPDATE PAGE TEXTS (as you already have)
function updatePageContent(lang) {
    // ... (your update logic here, unchanged)
}

// SWITCH LANGUAGE (now calls re-initializer)
function switchLanguage(lang) {
    // Update URL
    const url = new URL(window.location);
    url.searchParams.set('lang', lang);
    window.history.replaceState({}, '', url);

    // Update page content
    updatePageContent(lang);

    // Update the current language display
    const currentLang = document.querySelector('.current-lang');
    if (currentLang) {
        switch(lang) {
            case 'nl':
                currentLang.innerHTML = '<span class="flag-circle"><span class="fi fi-nl"></span></span> NL';
                break;
            case 'en':
                currentLang.innerHTML = '<span class="flag-circle"><span class="fi fi-gb"></span></span> EN';
                break;
            case 'de':
                currentLang.innerHTML = '<span class="flag-circle"><span class="fi fi-de"></span></span> DE';
                break;
        }
    }

    // --- CRITICAL: Re-initialize event listeners on the replaced node
    initLanguageSwitcher();
}

// --- DOM READY LOGIC ---
document.addEventListener('DOMContentLoaded', function() {
    const currentLang = getCurrentLanguage();
    updatePageContent(currentLang);

    // Always (re-)initialize language switcher on load
    initLanguageSwitcher();

    // ...rest of your other initialization code (chat, modal, etc.)...
});
