/**
 * FAQ dynamic prices - populates price list and deposit from boats data.
 * Runs after translation; listens for languageChanged to update when lang switches.
 */
(function () {
  function getLang() {
    return localStorage.getItem("selected-language") || document.documentElement.lang || "nl";
  }

  function populateFaqPrices() {
    const data = window.__FAQ_DATA__;
    if (!data || !data.priceListByLang) return;

    const lang = getLang();
    const priceListHtml = data.priceListByLang[lang] || data.priceListByLang.nl;

    const listEl = document.getElementById("faq-price-list");
    if (listEl && priceListHtml) {
      listEl.innerHTML = priceListHtml;
    }

    const depositRangeEl = document.querySelector("[data-faq-dynamic='deposit-range']");
    if (depositRangeEl && data.depositRange) {
      depositRangeEl.textContent = data.depositRange;
    }

    const depositSloepEl = document.querySelector("[data-faq-dynamic='deposit-sloep']");
    if (depositSloepEl && data.depositSloep != null) {
      depositSloepEl.textContent = "€" + data.depositSloep;
    }

    const depositZeilbootEl = document.querySelector("[data-faq-dynamic='deposit-zeilboot']");
    if (depositZeilbootEl && data.depositZeilboot != null) {
      depositZeilbootEl.textContent = "€" + data.depositZeilboot;
    }
  }

  function init() {
    populateFaqPrices();
    window.addEventListener("languageChanged", populateFaqPrices);
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", function () {
      setTimeout(init, 50);
    });
  } else {
    setTimeout(init, 50);
  }
})();
