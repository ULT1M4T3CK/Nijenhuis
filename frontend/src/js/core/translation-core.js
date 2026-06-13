(() => {
  /* ---------- 1. DICTIONARY (loaded on demand) --------------- */
  const t = {};
  const LANGS = ['nl', 'de', 'en'];
  const I18N_BASE = '/frontend/src/js/i18n/';
  const CACHE_BUST = 'v=1';

  /* ---------- 1b. SEO META (per-page, per-language) ---------- */
  const meta = {
    home: {
      title: { nl: "Botenverhuur Wanneperveen & Weerribben | Nijenhuis", en: "Boat Rental Wanneperveen & Weerribben | Nijenhuis", de: "Bootsverleih Wanneperveen & Weerribben | Nijenhuis" },
      desc: { nl: "Botenverhuur bij Giethoorn. Huur een bootje, luxe sloep, fluisterboot of kano in de Weerribben. Sloepverhuur en bootverhuur vanaf €20/dag. Geen vaarbewijs nodig.", en: "Boat rental at Giethoorn. Rent a boat, luxury sloop, electric boat or canoe in the Weerribben. Sloop rental from €20/day. No boating license required.", de: "Bootsverleih bei Giethoorn. Miete ein Boot, Luxussloep oder Kanu in den Weerribben. Bootsverleih ab €20/Tag. Kein Führerschein erforderlich." }
    },
    boats: {
      title: { nl: "Botenverhuur Giethoorn | Sloep, Fluisterboot & Kano Huren", en: "Boat Rental Giethoorn | Sloop, Electric Boat & Canoe Hire", de: "Bootsverleih Giethoorn | Sloep, Elektroboot & Kanu mieten" },
      desc: { nl: "Boot huren bij Giethoorn? Nijenhuis Botenverhuur verhuurt luxe sloepen, fluisterboten, zeilboten, kano's en SUP boards in de Weerribben. Vanaf €20/dag. Geen vaarbewijs nodig. Reserveer nu!", en: "Rent a boat at Giethoorn? Nijenhuis Boat Rental offers luxury sloops, electric boats, sailboats, canoes and SUP boards in the Weerribben. From €20/day. No licence required. Book now!", de: "Boot mieten bei Giethoorn? Nijenhuis Bootsverleih vermietet Luxus-Sloopen, Elektroboote, Segelboote, Kanus und SUP-Boards in den Weerribben. Ab €20/Tag. Kein Führerschein nötig. Jetzt reservieren!" }
    },
    camping: {
      title: { nl: "Camping Weerribben bij Giethoorn", en: "Camping Weerribben near Giethoorn", de: "Camping Weerribben bei Giethoorn" },
      desc: { nl: "Seizoenscamping in Nationaal Park Weerribben-Wieden bij Giethoorn. ✓ Eigen aanlegplaats ✓ Water, elektriciteit & sanitair ✓ Caravan mag jaarrond staan. Al 50+ jaar familiebedrijf.", en: "Seasonal camping in National Park Weerribben-Wieden near Giethoorn. ✓ Own berth ✓ Water, electricity & sanitary ✓ Caravan can stay year-round. 50+ years family business.", de: "Saisoncamping im Nationalpark Weerribben-Wieden bei Giethoorn. ✓ Eigener Liegeplatz ✓ Wasser, Strom & Sanitär ✓ Wohnwagen kann ganzjährig stehen. 50+ Jahre Familienbetrieb." }
    },
    vakantiehuis: {
      title: { nl: "Vakantiehuis Belterwiede bij Giethoorn", en: "Holiday House Belterwiede near Giethoorn", de: "Ferienhaus Belterwiede bei Giethoorn" },
      desc: { nl: "Ruim vakantiehuis aan het Belterwiede meer bij Giethoorn. 5 slaapkamers, 2 badkamers, volledig uitgerust. ✓ Het hele jaar geopend ✓ Direct aan het water ✓ Ideaal voor families.", en: "Spacious holiday house at Lake Belterwiede near Giethoorn. 5 bedrooms, 2 bathrooms, fully equipped. ✓ Open year-round ✓ Right by the water ✓ Ideal for families.", de: "Geräumiges Ferienhaus am Belterwiede-See bei Giethoorn. 5 Schlafzimmer, 2 Badezimmer, voll ausgestattet. ✓ Ganzjährig geöffnet ✓ Direkt am Wasser ✓ Ideal für Familien." }
    },
    "te-koop": {
      title: { nl: "Chalets & stacaravans te koop", en: "Chalets & caravans for sale", de: "Chalets & Wohnwagen zu verkaufen" },
      desc: { nl: "Bekijk chalets en stacaravans te koop bij Camping Nijenhuis in Wanneperveen. Direct aan het water in de Weerribben. Actueel aanbod met prijzen.", en: "View chalets and caravans for sale at Camping Nijenhuis in Wanneperveen. Right by the water in the Weerribben. Current listings with prices.", de: "Chalets und Wohnwagen zum Verkauf bei Camping Nijenhuis in Wanneperveen. Direkt am Wasser in den Weerribben. Aktuelle Angebote mit Preisen." }
    },
    vaarkaart: {
      title: { nl: "Vaarkaart Weerribben-Wieden | Routes", en: "Sailing Chart Weerribben-Wieden | Routes", de: "Fahrkarte Weerribben-Wieden | Routen" },
      desc: { nl: "Interactieve vaarkaart voor de Weerribben. Ontdek vaarroutes naar Giethoorn, Wanneperveen en door Nationaal Park Weerribben-Wieden. Inclusief vaarregels en veiligheidstips.", en: "Interactive sailing chart for the Weerribben. Discover routes to Giethoorn, Wanneperveen and through National Park Weerribben-Wieden. Including sailing rules and safety tips.", de: "Interaktive Fahrkarte für die Weerribben. Entdecke Routen nach Giethoorn, Wanneperveen und durch den Nationalpark Weerribben-Wieden. Mit Fahrregeln und Sicherheitstipps." }
    },
    contact: {
      title: { nl: "Contact & Route | Wanneperveen", en: "Contact & Directions | Wanneperveen", de: "Kontakt & Anfahrtsweg | Wanneperveen" },
      desc: { nl: "Contact opnemen met Nijenhuis Botenverhuur. Adres: Veneweg 199, Wanneperveen. Tel: 0522 281 528. Open april-oktober, dagelijks 9:00-18:00. Gratis parkeren.", en: "Contact Nijenhuis Boat Rental. Address: Veneweg 199, Wanneperveen. Tel: 0522 281 528. Open April-October, daily 9:00-18:00. Free parking.", de: "Kontakt zu Nijenhuis Bootsverleih. Adresse: Veneweg 199, Wanneperveen. Tel: 0522 281 528. Geöffnet April-Oktober, täglich 9:00-18:00. Kostenloses Parken." }
    },
    faq: {
      title: { nl: "Veelgestelde vragen", en: "Frequently asked questions", de: "Häufig gestellte Fragen" },
      desc: { nl: "Antwoorden op veelgestelde vragen over boot huren bij Nijenhuis. Prijzen, vaarbewijs, openingstijden, reserveren en meer informatie over botenverhuur in de Weerribben.", en: "Answers to frequently asked questions about boat hire at Nijenhuis. Prices, license, opening hours, reservations and more about boat rental in the Weerribben.", de: "Antworten auf häufig gestellte Fragen zum Bootsverleih bei Nijenhuis. Preise, Führerschein, Öffnungszeiten, Reservierungen und mehr zum Bootsverleih in den Weerribben." }
    },
    giethoorn: {
      title: { nl: "Nijenhuis Botenverhuur bij Giethoorn – ontdek het Venetië van het Noorden", en: "Nijenhuis Boat Rental at Giethoorn – discover the Venice of the North", de: "Nijenhuis Bootsverleih bei Giethoorn – entdecke das Venedig des Nordens" },
      desc: { nl: "Ontdek Giethoorn per boot. Huur een fluisterboot, zeilpunter of kano bij Nijenhuis Botenverhuur en vaar door de prachtige grachten van het Venetië van het Noorden. Reserveer online!", en: "Discover Giethoorn by boat. Rent an electric boat, sailpunter or canoe at Nijenhuis Boat Rental and cruise through the beautiful canals of the Venice of the North. Book online!", de: "Entdecke Giethoorn mit dem Boot. Miete ein Elektroboot, Segelpunter oder Kanu bei Nijenhuis Bootsverleih und fahre durch die malerischen Grachten des Venedigs des Nordens. Online buchen!" }
    },
    "belt-schutsloot": {
      title: { nl: "Belt-schutsloot | Alternatief Giethoorn", en: "Belt-schutsloot | Alternative to Giethoorn", de: "Belt-schutsloot | Alternative zu Giethoorn" },
      desc: { nl: "Ontdek Belt-schutsloot, een verborgen parel nabij Giethoorn. Minder toeristisch, maar met dezelfde idyllische charme: grachten, bruggetjes en rietgedekte huizen. Boot huren voor Belt-schutsloot bij Nijenhuis Botenverhuur.", en: "Discover Belt-schutsloot, a hidden gem near Giethoorn. Less touristic but with the same idyllic charm: canals, bridges and thatched houses. Rent a boat for Belt-schutsloot at Nijenhuis Boat Rental.", de: "Entdecke Belt-schutsloot, ein verborgenes Juwel bei Giethoorn. Weniger touristisch, aber mit dem gleichen idyllischen Charme: Grachten, Brücken und Reetdachhäuser. Boot mieten für Belt-schutsloot bei Nijenhuis Bootsverleih." }
    },
    wanneperveen: {
      title: { nl: "Bootverhuur Nijenhuis Wanneperveen: waarom bij ons", en: "Nijenhuis Boat Rental Wanneperveen: why choose us", de: "Nijenhuis Bootsverleih Wanneperveen: warum wir" },
      desc: { nl: "Geniet van rustig varen met Bootverhuur Nijenhuis Wanneperveen. Huur een boot in het vredige Wanneperveen en ontdek prachtige vaarwegen zonder drukte.", en: "Enjoy peaceful cruising with Nijenhuis Boat Rental Wanneperveen. Rent a boat in tranquil Wanneperveen and discover beautiful waterways without crowds.", de: "Genieße entspanntes Fahren mit Nijenhuis Bootsverleih Wanneperveen. Miete ein Boot im ruhigen Wanneperveen und entdecke wunderschöne Wasserstraßen ohne Trubel." }
    },
    booking: {
      title: { nl: "Boek je boot", en: "Book your boat", de: "Boot buchen" },
      desc: { nl: "Boek je boot bij Nijenhuis Botenverhuur. Voltooi je reservering voor een perfecte dag op het water in de Weerribben.", en: "Book your boat at Nijenhuis Boat Rental. Complete your reservation for a perfect day on the water in the Weerribben.", de: "Buch dein Boot bei Nijenhuis Bootsverleih. Schließe deine Reservierung für einen perfekten Tag auf dem Wasser in den Weerribben ab." }
    },
    checkout: {
      title: { nl: "Afrekenen", en: "Checkout", de: "Kasse" },
      desc: { nl: "Voltooi je reservering bij Nijenhuis Botenverhuur.", en: "Complete your reservation at Nijenhuis Boat Rental.", de: "Schließe deine Reservierung bei Nijenhuis Bootsverleih ab." }
    }
  };

  const OG_LOCALES = { nl: "nl_NL", en: "en_GB", de: "de_DE" };

  /* ---------- 2. STATE MANAGEMENT ---------------------------- */
  const DEFAULT_LANG = "nl";
  const LANG_KEY = "selected-language";
  let _loadingLang = null;

  function getStoredLang() {
    return localStorage.getItem(LANG_KEY) || DEFAULT_LANG;
  }

  function storeLang(lang) {
    localStorage.setItem(LANG_KEY, lang);
  }

  /* ---------- 2b. ASYNC DICTIONARY LOADER -------------------- */
  function loadDict(lang) {
    if (t[lang]) return Promise.resolve(t[lang]);
    if (_loadingLang === lang) return _loadingLang._promise;

    const url = I18N_BASE + lang + '.json?' + CACHE_BUST;
    const promise = fetch(url).then(r => {
      if (!r.ok) throw new Error('HTTP ' + r.status);
      return r.json();
    }).then(data => {
      t[lang] = data;
      _loadingLang = null;
      return data;
    }).catch(err => {
      console.warn('[i18n] Failed to load ' + lang + ':', err);
      _loadingLang = null;
      return t[lang] || t[DEFAULT_LANG] || {};
    });

    _loadingLang = lang;
    _loadingLang._promise = promise;
    return promise;
  }

  /* ---------- 3. APPLY TRANSLATION --------------------------- */
  function applyTranslations(lang) {
    const dict = t[lang] || t[DEFAULT_LANG];
    if (!dict) return;

    document.documentElement.lang = lang;

    if (!isBlogPage()) {
      const page = document.body?.getAttribute?.("data-page");
      const m = page && meta[page];
      if (m) {
        const title = m.title?.[lang] || m.title?.[DEFAULT_LANG];
        const desc = m.desc?.[lang] || m.desc?.[DEFAULT_LANG];
        if (title) document.title = title;
        const descMeta = document.querySelector('meta[name="description"]');
        if (descMeta && desc) descMeta.setAttribute("content", desc);
        const ogTitle = document.querySelector('meta[property="og:title"]');
        if (ogTitle && title) ogTitle.setAttribute("content", title);
        const ogDesc = document.querySelector('meta[property="og:description"]');
        if (ogDesc && desc) ogDesc.setAttribute("content", desc);
        const twitterTitle = document.querySelector('meta[name="twitter:title"]');
        if (twitterTitle && title) twitterTitle.setAttribute("content", title);
        const twitterDesc = document.querySelector('meta[name="twitter:description"]');
        if (twitterDesc && desc) twitterDesc.setAttribute("content", desc);
        const ogLocale = document.querySelector('meta[property="og:locale"]');
        if (ogLocale) ogLocale.setAttribute("content", OG_LOCALES[lang] || "nl_NL");
        const basePath = window.location.pathname || "/";
        const ogUrl = document.querySelector('meta[property="og:url"]');
        if (ogUrl) {
          const url = lang === "nl" ? window.location.origin + basePath : window.location.origin + basePath + (basePath.includes("?") ? "&" : "?") + "lang=" + lang;
          ogUrl.setAttribute("content", url);
        }
        const ogImgAlt = document.querySelector('meta[property="og:image:alt"]');
        if (ogImgAlt && desc) ogImgAlt.setAttribute("content", desc);
        const twitterImgAlt = document.querySelector('meta[name="twitter:image:alt"]');
        if (twitterImgAlt && desc) twitterImgAlt.setAttribute("content", desc);
      }
    }

    if (typeof gtag === "function") {
      try { gtag("set", "dimension1", lang); } catch (e) { /* ignore */ }
    }

    document.querySelectorAll('.lang-btn').forEach(btn => {
      btn.classList.remove('active');
      if (btn.dataset.lang === lang) {
        btn.classList.add('active');
      }
    });

    document.querySelectorAll("[data-i18n]").forEach(el => {
      const key = el.getAttribute("data-i18n");
      const value = dict[key];
      if (!value) return;

      const attrList = el.getAttribute("data-i18n-attr");
      const tag = el.tagName;
      if (attrList && (tag === 'INPUT' || tag === 'TEXTAREA' || tag === 'BUTTON')) {
        return;
      }

      let text = value;
      const paramsAttr = el.getAttribute("data-i18n-params");
      if (paramsAttr) {
        try {
          const params = JSON.parse(paramsAttr);
          Object.keys(params).forEach(param => {
            text = text.replace(`{${param}}`, params[param]);
          });
        } catch (e) {
          console.warn('Invalid JSON in data-i18n-params', e);
        }
      }

      const htmlAllowedKeys = new Set([
        'boats_intro_text', 'boats_bluf_summary', 'boats_why_flexible_desc', 'boats_cta_details', 'boats_fishing_p2', 'intro_h2_p', 'intro_h2_p2',
        'house_overview_p1', 'house_overview_p3', 'house_overview_p4',
        'footer_bottom', 'footer_rights', 'hero_book_badge',
        'camping_overview_seasonal_description',
        'boat_modal_description_title',
        'vaarkaart_footer_source',
        'vaarkaart_disclaimer_text',
        'vaarkaart_disclaimer_title',
        'about_location_desc', 'about_season_desc', 'about_fleet_desc', 'about_prices_desc',
        'fleet_hourly_note',
        'faq_page_html'
      ]);

      if (htmlAllowedKeys.has(key) || key.startsWith('vaarkaart_') || key.startsWith('boats_card_specs_')) {
        if (window.SecurityUtils && window.SecurityUtils.sanitizeHtml) {
          el.innerHTML = window.SecurityUtils.sanitizeHtml(text, {
            allowedTags: ['strong', 'em', 'b', 'i', 'u', 'br', 'p', 'a', 'span', 'div', 'h2', 'h3', 'ul', 'li'],
            allowedAttributes: {
              'a': ['href', 'target', 'rel'],
              'ul': ['id', 'class', 'style'],
              '*': ['class', 'style', 'data-faq-dynamic']
            }
          });
        } else {
          el.innerHTML = text;
        }
      } else {
        el.textContent = text.replace(/<[^>]*>/g, '');
      }
    });

    document.querySelectorAll("[data-i18n-attr]").forEach(el => {
      const key = el.getAttribute("data-i18n");
      const attrs = el.getAttribute("data-i18n-attr").split(",");
      attrs.forEach(attr => {
        if (dict[key]) el.setAttribute(attr.trim(), dict[key]);
      });
    });

    document.querySelectorAll("img[data-i18n-alt]").forEach(el => {
      const key = el.getAttribute("data-i18n-alt");
      const alt = dict[key];
      if (alt) el.setAttribute("alt", alt);
    });

    document
      .querySelectorAll("#languageSwitcher .lang-btn")
      .forEach(btn => btn.classList.toggle("active", btn.dataset.lang === lang));
  }

  /* ---------- 4. BLOG HELPERS -------------------------------- */
  function getBlogUrlForLang(targetLang) {
    const pathname = (window.location.pathname || '').replace(/\/$/, '');
    const blogMatch = pathname.match(/^\/(?:en|de)?\/?blog(?:\/([a-z0-9\-]+))?$/);
    if (!blogMatch) return null;
    const slug = blogMatch[1] || '';
    const prefix = targetLang === 'nl' ? '' : '/' + targetLang;
    return prefix + '/blog' + (slug ? '/' + slug : '');
  }

  function isBlogPage() {
    const pathname = (window.location.pathname || '').replace(/\/$/, '');
    return /^\/(?:en|de)?\/?blog(\/[a-z0-9\-]+)?$/.test(pathname);
  }

  function isBlogPageActive(pathname, code) {
    const p = (pathname || '').replace(/\/$/, '');
    if (code === 'nl') return /^\/blog(\/[a-z0-9\-]+)?$/.test(p);
    return new RegExp('^/' + code + '/blog').test(p);
  }

  /* ---------- 5. LANGUAGE SWITCHER UI ------------------------ */
  function buildSwitcher() {
    let switcher = document.getElementById("languageSwitcher");
    if (!switcher) {
      switcher = document.createElement('div');
      switcher.id = 'languageSwitcher';
      switcher.className = 'language-switcher';
      const navTarget = document.querySelector('.top-bar .top-bar-content') || document.querySelector('.nav-container') || document.body;
      if (navTarget === document.body) {
        switcher.style.position = 'fixed';
        switcher.style.top = '10px';
        switcher.style.right = '10px';
        switcher.style.zIndex = '9999';
      }
      navTarget.appendChild(switcher);
    }
    while (switcher.firstChild) switcher.removeChild(switcher.firstChild);

    const langs = [
      { code: "nl", flag: "nl.svg", label: "Nederlands" },
      { code: "de", flag: "de.svg", label: "Deutsch" },
      { code: "en", flag: "gb.svg", label: "English" }
    ];

    const onBlogPage = isBlogPage();
    const currentLang = getStoredLang();

    langs.forEach(({ code, flag, label }) => {
      const img = document.createElement('img');
      img.src = `/frontend/public/flags/${flag}`;
      img.alt = label;
      img.className = 'flag-icon';

      if (onBlogPage) {
        const blogUrl = getBlogUrlForLang(code);
        const active = isBlogPageActive(window.location.pathname, code);
        const a = document.createElement('a');
        a.href = blogUrl || '#';
        a.className = 'lang-btn' + (active ? ' active' : '');
        a.dataset.lang = code;
        a.setAttribute("aria-label", label);
        a.appendChild(img);
        switcher.appendChild(a);
      } else {
        const btn = document.createElement("button");
        btn.className = "lang-btn" + (currentLang === code ? ' active' : '');
        btn.dataset.lang = code;
        btn.setAttribute("aria-label", label);
        btn.appendChild(img);
        btn.addEventListener("click", () => {
          loadDict(code).then(() => {
            storeLang(code);
            updateUrlForLang(code);
            applyTranslations(code);
            window.dispatchEvent(new CustomEvent('languageChanged', { detail: { language: code } }));
          });
        });
        switcher.appendChild(btn);
      }
    });

    if (!onBlogPage) {
      document.querySelectorAll("#languageSwitcher .lang-btn").forEach(btn =>
        btn.classList.toggle("active", btn.dataset.lang === currentLang));
    }
  }

  /* ---------- 6. URL SYNC ------------------------------------ */
  function updateUrlForLang(lang) {
    if (isBlogPage()) return;
    const url = new URL(window.location.href);
    if (lang === 'nl') {
      url.searchParams.delete('lang');
    } else {
      url.searchParams.set('lang', lang);
    }
    const newUrl = url.pathname + (url.search || '') + (url.hash || '');
    window.history.replaceState({}, '', newUrl);
  }

  /* ---------- 7. INITIALISE ON DOM READY --------------------- */
  function initializeTranslation() {
    buildSwitcher();
    let lang = getStoredLang();
    if (isBlogPage()) {
      const pathname = (window.location.pathname || '').replace(/\/$/, '');
      if (/^\/en\/blog/.test(pathname)) lang = 'en';
      else if (/^\/de\/blog/.test(pathname)) lang = 'de';
      else lang = 'nl';
      storeLang(lang);
    } else {
      const urlLang = new URLSearchParams(window.location.search).get('lang');
      if (urlLang === 'en' || urlLang === 'de') {
        lang = urlLang;
        storeLang(lang);
      }
    }
    loadDict(lang).then(() => applyTranslations(lang));
  }

  if (document.readyState === 'loading') {
    document.addEventListener("DOMContentLoaded", initializeTranslation);
  } else {
    initializeTranslation();
  }

  setTimeout(() => {
    const switcher = document.getElementById("languageSwitcher");
    if (switcher && switcher.children.length === 0) {
      buildSwitcher();
    }
  }, 1000);

  /* ---------- 8. PUBLIC API ---------------------------------- */
  window.getTranslation = (key) => {
    const lang = getStoredLang();
    const dict = t[lang] || t[DEFAULT_LANG];
    return (dict && dict[key]) || key;
  };

  window.setLanguage = lang => {
    if (!LANGS.includes(lang)) return;
    loadDict(lang).then(() => {
      storeLang(lang);
      updateUrlForLang(lang);
      applyTranslations(lang);
      window.dispatchEvent(new CustomEvent('languageChanged', { detail: { language: lang } }));
    });
  };

  window.rebuildLanguageSwitcher = buildSwitcher;

  window.refreshI18n = () => {
    const lang = getStoredLang();
    if (t[lang]) {
      applyTranslations(lang);
    } else {
      loadDict(lang).then(() => applyTranslations(lang));
    }
  };
})();
