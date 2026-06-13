<?php
/**
 * Payment Success Page - Nijenhuis Botenverhuur
 */
require_once __DIR__ . '/../components/config.php';
require_once __DIR__ . '/../components/csp.php';
nijenhuis_send_csp_header();
$arrivalLocation = SITE_NAME . ', ' . SITE_ADDRESS . ', ' . SITE_POSTAL . ' ' . SITE_COUNTRY;
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Betaling geslaagd - <?php echo SITE_NAME; ?></title>
    <meta name="description" content="Je bootreservering is bevestigd. Bedankt voor het kiezen van <?php echo SITE_NAME; ?>!">
    <meta name="robots" content="noindex, nofollow">
    <link rel="icon" type="image/svg+xml" href="<?php echo assetPath('frontend/Images/logo-white.svg'); ?>">
    <link rel="stylesheet" href="<?php echo assetPath('frontend/css/styles.css'); ?>">
    <link rel="stylesheet" href="<?php echo assetPath('frontend/css/pages/checkout.css'); ?>">
    <link rel="stylesheet" href="<?php echo assetPath('frontend/css/pages/payment-success.css'); ?>">
    <script src="<?php echo assetPath('frontend/src/js/core/security.js'); ?>"></script>
    <script src="<?php echo assetPath('frontend/src/js/core/shared.js'); ?>"></script>
    <script src="<?php echo assetPath('frontend/src/js/core/translation.js'); ?>"></script>
</head>
<body class="page-checkout page-payment-success" data-page="payment-success">
    <script type="application/json" id="siteBrandForPdf"><?php
        echo json_encode([
            'name' => SITE_NAME,
            'tagline' => SITE_TAGLINE,
            'phone' => SITE_PHONE,
            'siteUrl' => SITE_URL,
            'logoUrl' => assetPath('frontend/Images/logo-white.svg'),
        ], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS);
    ?></script>
    <?php include __DIR__ . '/../components/topbar.php'; ?>

    <div class="checkout-page-container payment-success-page-container">
        <div class="container container--checkout-wide">
            <div class="checkout-card payment-success-shell">
                <div class="error-message" id="errorMessage" role="alert"></div>

                <div class="loading payment-success-loading" id="loading">
                    <div class="spinner"></div>
                    <p data-i18n="payment_success_processing">Je betaling wordt verwerkt...</p>
                </div>

                <div id="successContent" class="payment-success-content" style="display: none;">
                <a href="/" class="checkout-brand payment-success-brand-in-card" aria-label="Nijenhuis — home">
                    <img src="<?php echo assetPath('frontend/Images/logo-white.svg'); ?>" alt="<?php echo htmlspecialchars(SITE_NAME); ?>" class="checkout-brand-logo" width="180" height="61">
                </a>
                    <div class="checkout-hero payment-success-hero">
                        <h2 class="checkout-secure-title" data-i18n="checkout_secure_title">Veilig afrekenen</h2>
                        <ol class="checkout-steps" aria-label="Stappen">
                            <li class="checkout-steps__item checkout-steps__item--done">
                                <span class="checkout-steps__num">1</span>
                                <span class="checkout-steps__label" data-i18n="checkout_step_details">Gegevens</span>
                            </li>
                            <li class="checkout-steps__sep" aria-hidden="true">›</li>
                            <li class="checkout-steps__item checkout-steps__item--done">
                                <span class="checkout-steps__num">2</span>
                                <span class="checkout-steps__label" data-i18n="checkout_step_payment">Betaling</span>
                            </li>
                            <li class="checkout-steps__sep" aria-hidden="true">›</li>
                            <li class="checkout-steps__item checkout-steps__item--active">
                                <span class="checkout-steps__num">3</span>
                                <span class="checkout-steps__label" data-i18n="checkout_step_confirm">Bevestiging</span>
                            </li>
                        </ol>
                    </div>

                    <div class="payment-success-headline">
                        <div class="success-icon payment-success-check" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                            </svg>
                        </div>
                        <h1 class="success-title" data-i18n="payment_success_title">Betaling geslaagd!</h1>
                        <p class="payment-success-ref">
                            <span data-i18n="payment_success_reference_label">Reserveringsnummer:</span>
                            <strong id="successReference">—</strong>
                        </p>
                        <p class="success-subtitle" data-i18n="payment_success_subtitle">Je bootreservering is bevestigd. Je ontvangt binnenkort een bevestigingsmail.</p>
                    </div>

                    <div class="payment-success-booking-wrap" id="bookingDetails"></div>

                    <section class="payment-success-arrival" aria-labelledby="arrivalTitle">
                        <h3 id="arrivalTitle" data-i18n="payment_success_arrival_title">Aankomst</h3>
                        <p>
                            <span class="payment-success-arrival-kicker" data-i18n="payment_success_arrival_location_label">Locatie</span>:
                            <span id="arrivalLocationText"><?php echo htmlspecialchars($arrivalLocation); ?></span>
                        </p>
                        <p>
                            <span class="payment-success-arrival-kicker" data-i18n="payment_success_arrival_time_label">Aankomsttijd</span>:
                            <span id="arrivalTimeText">—</span>
                        </p>
                        <p>
                            <span class="payment-success-arrival-kicker" data-i18n="payment_success_arrival_bring_label">Meenemen</span>:
                            <span data-i18n="payment_success_arrival_bring_text">Zonbescherming en comfortabele kleding. Borg betaal je contant bij aankomst volgens je reservering.</span>
                        </p>
                    </section>

                    <div class="payment-success-actions">
                        <button type="button" class="btn btn-secondary" id="downloadPdfBtn" disabled data-i18n="payment_success_download_pdf">Download als PDF</button>
                        <a href="/" class="btn btn-primary" data-i18n="payment_success_back">Terug naar home</a>
                        <a href="/contact" class="btn btn-secondary payment-success-btn-outline" data-i18n="payment_success_contact">Contact</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="<?php echo assetPath('frontend/src/js/vendor/jspdf.umd.min.js'); ?>"></script>
    <script src="<?php echo assetPath('js/boat-data-service.js'); ?>"></script>
    <script src="<?php echo assetPath('mollie_api.js'); ?>"></script>
    <script src="<?php echo assetPath('frontend/src/js/booking/mollie-payment.js'); ?>"></script>
    <script>
        class PaymentSuccessPage {
            constructor() {
                this.paymentSystem = window.molliePayment || null;
                this.bookingId = null;
                this.cartId = null;
                this.payOnArrival = false;
                this.depositTotalForPdf = 0;
                this.logoDataUrlForPdf = null;
                this.initPdfButton();
                this.init();
            }

            initPdfButton() {
                const btn = document.getElementById('downloadPdfBtn');
                if (btn) {
                    btn.addEventListener('click', async () => {
                        await this.downloadBookingPdf();
                    });
                }
            }

            t(key) {
                return window.getTranslation ? window.getTranslation(key) : key;
            }

            escapeHTML(str) {
                if (!str) return '';
                const div = document.createElement('div');
                div.textContent = str;
                return div.innerHTML;
            }

            normalizeBoatImageUrlFromPath(raw) {
                if (!raw) return '/frontend/Images/Boats/tender-720/tender-720-10-12.jpg';
                if (/^https?:\/\//i.test(raw)) return raw;
                let p = String(raw).replace(/\\/g, '/');
                if (p.startsWith('../')) p = p.slice(3);
                if (!p.startsWith('/')) p = '/' + p;
                return p;
            }

            async getBoatImageUrl(boatType) {
                if (window.BoatDataService && boatType) {
                    try {
                        const boat = await window.BoatDataService.getBoatById(boatType);
                        if (boat && (boat.image || boat.headerImage)) {
                            return this.normalizeBoatImageUrlFromPath(boat.image || boat.headerImage);
                        }
                    } catch (e) {
                        console.warn('Boat image load failed:', e);
                    }
                }
                try {
                    const stored = localStorage.getItem('nijenhuis_boats');
                    if (stored) {
                        const boats = JSON.parse(stored);
                        const boat = boats.find(b => b.id === boatType);
                        if (boat && (boat.image || boat.headerImage)) {
                            return this.normalizeBoatImageUrlFromPath(boat.image || boat.headerImage);
                        }
                    }
                } catch (e) { /* noop */ }
                return this.normalizeBoatImageUrlFromPath('');
            }

            setReferenceAndArrival() {
                const refEl = document.getElementById('successReference');
                if (refEl) {
                    refEl.textContent = this.cartId || this.bookingId || this.booking?.id || '—';
                }
                const primary = this.booking || (this.bookings && this.bookings[0]);
                this.fillArrivalDetails(primary);
            }

            fillArrivalDetails(booking) {
                const el = document.getElementById('arrivalTimeText');
                if (!el) return;
                if (!booking) {
                    el.textContent = '—';
                    return;
                }
                const time = booking.arrivalTime || '';
                const dateStr = booking.date ? this.formatDate(booking.date) : '';
                if (time && dateStr) {
                    el.textContent = `${time}, ${dateStr}`;
                } else if (time) {
                    el.textContent = time;
                } else if (dateStr) {
                    el.textContent = dateStr;
                } else {
                    el.textContent = '—';
                }
            }

            getArrivalTimeLine() {
                const el = document.getElementById('arrivalTimeText');
                return el ? el.textContent.trim() : '—';
            }

            getArrivalLocationLine() {
                const el = document.getElementById('arrivalLocationText');
                return el ? el.textContent.trim() : '';
            }

            /**
             * ASCII-safe lines for jsPDF default font (avoids missing glyphs for Dutch diacritics).
             */
            pdfSafe(s) {
                if (!s) return '';
                return String(s)
                    .normalize('NFD')
                    .replace(/[\u0300-\u036f]/g, '')
                    .replace(/[^\x20-\x7E\u20AC\r\n]/g, '?');
            }

            /**
             * Draw € and the amount as two separate text calls. Some mobile PDF
             * engines place U+20AC on top of the following digit when both sit in one string.
             */
            pdfDrawEuroAmount(doc, x, y, amount) {
                const numStr = typeof amount === 'number'
                    ? amount.toFixed(2).replace('.', ',')
                    : this.pdfSafe(String(amount));
                const euro = this.pdfSafe('\u20AC');
                doc.text(euro, x, y);
                let euroW = typeof doc.getTextWidth === 'function' ? doc.getTextWidth(euro) : 0;
                if (!Number.isFinite(euroW) || euroW < 0.01) {
                    const fs = doc.internal && doc.internal.getFontSize ? doc.internal.getFontSize() : 12;
                    euroW = fs * 0.22;
                }
                const gap = 0.7;
                doc.text(numStr, x + euroW + gap, y);
            }

            /** Right-align € amount to x = right edge (mm). */
            pdfDrawEuroAmountAlignRight(doc, rightX, y, amount) {
                const numStr = typeof amount === 'number'
                    ? amount.toFixed(2).replace('.', ',')
                    : this.pdfSafe(String(amount));
                const euro = this.pdfSafe('\u20AC');
                const gap = 0.7;
                const gw = typeof doc.getTextWidth === 'function' ? doc.getTextWidth : null;
                let euroW = gw ? gw.call(doc, euro) : 2.2;
                let numW = gw ? gw.call(doc, numStr) : 12;
                if (!Number.isFinite(euroW)) euroW = 2.2;
                if (!Number.isFinite(numW)) numW = 12;
                const xStart = rightX - euroW - gap - numW;
                this.pdfDrawEuroAmount(doc, xStart, y, amount);
            }

            getJsPdfConstructor() {
                const j = window.jspdf;
                if (j && typeof j.jsPDF === 'function') return j.jsPDF;
                if (j && typeof j.default === 'function') return j.default;
                if (typeof window.jsPDF === 'function') return window.jsPDF;
                return null;
            }

            readBrandForPdf() {
                const el = document.getElementById('siteBrandForPdf');
                const fallback = {
                    name: 'Nijenhuis Botenverhuur',
                    tagline: '',
                    phone: '0522 281 528',
                    siteUrl: 'https://nijenhuis-botenverhuur.com',
                    logoUrl: '/frontend/Images/logo-white.svg',
                };
                if (!el) return fallback;
                try {
                    const o = JSON.parse(el.textContent);
                    return {
                        name: o.name || fallback.name,
                        tagline: o.tagline || '',
                        phone: o.phone || fallback.phone,
                        siteUrl: o.siteUrl || fallback.siteUrl,
                        logoUrl: o.logoUrl || fallback.logoUrl,
                    };
                } catch (e) {
                    return fallback;
                }
            }

            /** Rasterize logo SVG to PNG data URL for jsPDF (same-origin fetch + canvas). */
            async rasterizeLogoToPngDataUrl(svgPath) {
                const path = svgPath.startsWith('/') ? svgPath : `/${svgPath.replace(/^\//, '')}`;
                const url = svgPath.startsWith('http') ? svgPath : `${window.location.origin}${path}`;
                const res = await fetch(url, { credentials: 'same-origin' });
                if (!res.ok) throw new Error('Logo request failed');
                const blob = await res.blob();
                const objectUrl = URL.createObjectURL(blob);
                try {
                    return await new Promise((resolve, reject) => {
                        const img = new Image();
                        img.onload = () => {
                            try {
                                const w = img.naturalWidth || 200;
                                const h = img.naturalHeight || 68;
                                const scale = 3;
                                const canvas = document.createElement('canvas');
                                canvas.width = Math.round(w * scale);
                                canvas.height = Math.round(h * scale);
                                const ctx = canvas.getContext('2d');
                                if (ctx) {
                                    ctx.imageSmoothingEnabled = true;
                                    ctx.imageSmoothingQuality = 'high';
                                    ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
                                }
                                resolve(canvas.toDataURL('image/png'));
                            } catch (e) {
                                reject(e);
                            }
                        };
                        img.onerror = () => reject(new Error('Logo image decode failed'));
                        img.src = objectUrl;
                    });
                } finally {
                    URL.revokeObjectURL(objectUrl);
                }
            }

            async preloadPdfLogo() {
                try {
                    let path = this.readBrandForPdf().logoUrl || '/frontend/Images/logo-white.svg';
                    if (!path.startsWith('/')) path = `/${path.replace(/^\//, '')}`;
                    this.logoDataUrlForPdf = await this.rasterizeLogoToPngDataUrl(path);
                } catch (e) {
                    console.warn('PDF logo preload failed:', e);
                    this.logoDataUrlForPdf = null;
                }
            }

            async ensurePdfLogo() {
                if (this.logoDataUrlForPdf) return;
                await this.preloadPdfLogo();
            }

            async loadRasterImageDataUrlForPdf(url) {
                if (!url) return null;
                const abs = /^https?:\/\//i.test(url) ? url : `${window.location.origin}${url.startsWith('/') ? url : '/' + url}`;
                return new Promise((resolve) => {
                    const img = new Image();
                    img.crossOrigin = 'anonymous';
                    img.onload = () => {
                        try {
                            const w = img.naturalWidth || 400;
                            const h = img.naturalHeight || 300;
                            const canvas = document.createElement('canvas');
                            canvas.width = w;
                            canvas.height = h;
                            const ctx = canvas.getContext('2d');
                            if (!ctx) {
                                resolve(null);
                                return;
                            }
                            ctx.drawImage(img, 0, 0);
                            resolve(canvas.toDataURL('image/jpeg', 0.9));
                        } catch (e) {
                            resolve(null);
                        }
                    };
                    img.onerror = () => resolve(null);
                    img.src = abs;
                });
            }

            pdfEnsureSpace(doc, y, needMm, margin) {
                const pageH = doc.internal.pageSize.getHeight();
                if (y + needMm > pageH - 16) {
                    doc.addPage();
                    return margin + 8;
                }
                return y;
            }

            pdfDrawSectionTitle(doc, title, margin, y) {
                doc.setFont('helvetica', 'bold');
                doc.setFontSize(11);
                doc.setTextColor(0, 51, 102);
                doc.text(this.pdfSafe(title), margin, y);
                doc.setTextColor(33, 37, 41);
                doc.setFont('helvetica', 'normal');
                doc.setFontSize(10);
                return y + 6.5;
            }

            pdfDrawMutedRule(doc, margin, y, pageW) {
                doc.setDrawColor(200, 210, 220);
                doc.setLineWidth(0.25);
                doc.line(margin, y, pageW - margin, y);
                doc.setDrawColor(0, 0, 0);
            }

            /** Light tile plus vector clock or map-pin (jsPDF default font has no icon glyphs). */
            pdfDrawArrivalIconTile(doc, x, y, sizeMm, kind) {
                doc.setFillColor(212, 227, 255);
                doc.setDrawColor(175, 198, 228);
                doc.setLineWidth(0.06);
                doc.rect(x, y, sizeMm, sizeMm, 'FD');
                const inset = sizeMm * 0.2;
                const is = sizeMm - inset * 2;
                const ix = x + inset;
                const iy = y + inset;
                if (kind === 'clock') {
                    this.pdfDrawPdfIconClock(doc, ix, iy, is);
                } else {
                    this.pdfDrawPdfIconMapPin(doc, ix, iy, is);
                }
            }

            /** Content height for merged borg + aankomst column (POA under image). */
            pdfEstimateMergedBorgArrivalColumnHeight(borgHead, borgBodyLines, depositPositive, arrHead, timeBlk, locBlk) {
                const pad = 5;
                let h = pad + 4;
                h += borgHead.length * 5 + 1;
                if (depositPositive) h += 10;
                h += borgBodyLines.length * 4;
                h += 5 + 6;
                h += arrHead.length * 5 + 2;
                h += 3 + timeBlk.length * 4 + 5;
                h += locBlk.length * 4 + 8;
                h += pad + 4;
                return h;
            }

            pdfDrawMergedBorgArrivalColumn(doc, boxX, boxY, colW, boxH, borgHead, borgBodyLines, depositAmount, arrHead, timeBlk, locBlk) {
                const pad = 5;
                const textX = boxX + pad + 1;
                doc.setFillColor(243, 243, 249);
                doc.rect(boxX, boxY, colW, boxH, 'F');
                doc.setDrawColor(194, 198, 209);
                doc.setLineWidth(0.2);
                doc.rect(boxX, boxY, colW, boxH, 'S');
                doc.setDrawColor(0, 49, 94);
                doc.setLineWidth(0.7);
                doc.line(boxX, boxY, boxX, boxY + boxH);
                let yy = boxY + pad + 4;
                doc.setFont('helvetica', 'bold');
                doc.setFontSize(11);
                doc.setTextColor(0, 49, 94);
                doc.text(borgHead, textX, yy);
                yy += borgHead.length * 5 + 1;
                if (depositAmount > 0) {
                    doc.setFontSize(16);
                    this.pdfDrawEuroAmount(doc, textX, yy, depositAmount);
                    yy += 10;
                }
                doc.setFont('helvetica', 'normal');
                doc.setFontSize(8.5);
                doc.setTextColor(66, 71, 80);
                doc.text(borgBodyLines, textX, yy);
                yy += borgBodyLines.length * 4 + 5;
                doc.setDrawColor(229, 231, 235);
                doc.setLineWidth(0.35);
                doc.line(textX, yy, boxX + colW - pad, yy);
                yy += 6;
                doc.setFont('helvetica', 'bold');
                doc.setFontSize(11);
                doc.setTextColor(0, 49, 94);
                doc.text(arrHead, textX, yy);
                yy += arrHead.length * 5 + 2;
                const sq = 4.3;
                const iconX = textX;
                this.pdfDrawArrivalIconTile(doc, iconX, yy - 3, sq, 'clock');
                doc.setFont('helvetica', 'bold');
                doc.setFontSize(6.5);
                doc.setTextColor(66, 71, 80);
                doc.text(this.pdfSafe(this.t('payment_success_pdf_checkin_label').toUpperCase()), textX + sq + 3, yy);
                yy += 3;
                doc.setFont('helvetica', 'normal');
                doc.setFontSize(9);
                doc.setTextColor(25, 28, 32);
                doc.text(timeBlk, textX + sq + 3, yy);
                yy += timeBlk.length * 4 + 5;
                this.pdfDrawArrivalIconTile(doc, iconX, yy - 3, sq, 'pin');
                doc.setFont('helvetica', 'bold');
                doc.setFontSize(6.5);
                doc.setTextColor(66, 71, 80);
                doc.text(this.pdfSafe(this.t('payment_success_arrival_location_label').toUpperCase()), textX + sq + 3, yy);
                yy += 3;
                doc.setFont('helvetica', 'normal');
                doc.setFontSize(9);
                doc.text(locBlk, textX + sq + 3, yy);
            }

            pdfDrawPdfIconClock(doc, x, y, s) {
                const cx = x + s / 2;
                const cy = y + s / 2;
                const r = s * 0.34;
                doc.setDrawColor(0, 49, 94);
                doc.setLineWidth(0.14);
                doc.circle(cx, cy, r, 'S');
                doc.setLineWidth(0.17);
                doc.line(cx, cy, cx, cy - r * 0.52);
                doc.line(cx, cy, cx + r * 0.4, cy + r * 0.12);
            }

            pdfDrawPdfIconMapPin(doc, x, y, s) {
                const cx = x + s / 2;
                const headCy = y + s * 0.38;
                const pr = s * 0.24;
                doc.setDrawColor(0, 49, 94);
                doc.setLineWidth(0.14);
                doc.circle(cx, headCy, pr, 'S');
                doc.line(cx - pr * 0.05, headCy + pr * 0.75, cx, y + s - 0.15);
                doc.line(cx + pr * 0.05, headCy + pr * 0.75, cx, y + s - 0.15);
            }

            async getBoatDepositAmount(boatType) {
                if (!boatType) return 0;
                if (window.BoatDataService) {
                    try {
                        const boat = await window.BoatDataService.getBoatById(boatType);
                        if (boat && boat.deposit != null) return Number(boat.deposit) || 0;
                    } catch (e) { /* noop */ }
                }
                try {
                    const stored = localStorage.getItem('nijenhuis_boats');
                    if (stored) {
                        const boats = JSON.parse(stored);
                        const boat = boats.find(b => b.id === boatType);
                        if (boat && boat.deposit != null) return Number(boat.deposit) || 0;
                    }
                } catch (e) { /* noop */ }
                return 0;
            }

            /**
             * Trip total for PDF hero: for pay-on-arrival reservations, rental + admin on deposit slice only
             * (matches checkout / email). Falls back to booking.amount or Mollie payment amount.
             */
            computePdfHeroTotalAmount() {
                if (this.booking) {
                    const b = this.booking;
                    if (b.payOnArrivalReservation && b.rentalAmount != null) {
                        return Math.round(Number(b.rentalAmount) * 100) / 100;
                    }
                    return Number(b.amount || 0);
                }
                if (this.bookings && this.bookings.length) {
                    const first = this.bookings[0];
                    if (first && first.payOnArrivalReservation) {
                        let s = 0;
                        for (const x of this.bookings) {
                            s += Number(x.rentalAmount != null ? x.rentalAmount : 0);
                        }
                        return Math.round(s * 100) / 100;
                    }
                    return Number(this.payment?.amount?.value || 0);
                }
                return 0;
            }

            isPayOnArrivalPdfMode() {
                if (this.booking && this.booking.payOnArrivalReservation) return true;
                if (this.bookings && this.bookings.length && this.bookings[0].payOnArrivalReservation) return true;
                return false;
            }

            /**
             * POA price overview rows (card layout: two columns, navy sections, arrival highlight box).
             * @returns {Array<{type: string, label?: string, amount?: number, textRight?: string, text?: string, showInclusief?: boolean}>|null}
             */
            getPoaPdfBreakdownRows() {
                if (!this.isPayOnArrivalPdfMode()) return null;
                const dep = this.depositTotalForPdf;
                const build = (rental, reservationFee, balance) => {
                    const rows = [];
                    rows.push({ type: 'twocol', label: this.t('payment_success_breakdown_rental'), amount: rental });
                    if (dep > 0) {
                        rows.push({ type: 'twocol', label: this.t('payment_success_pdf_deposit_heading'), amount: dep });
                    } else {
                        rows.push({
                            type: 'twocol',
                            label: this.t('payment_success_pdf_deposit_heading'),
                            textRight: this.t('price_deposit_none'),
                        });
                    }
                    rows.push({ type: 'hrule' });
                    rows.push({ type: 'section_navy', text: this.t('payment_success_pdf_poa_paid_at_reservation') });
                    rows.push({
                        type: 'total_paid_row',
                        label: this.t('payment_success_pdf_poa_total_paid_nonrefund'),
                        amount: reservationFee,
                    });
                    const totArrival = Math.round((balance + dep) * 100) / 100;
                    rows.push({
                        type: 'arrival_box',
                        amount: totArrival,
                        showInclusief: dep > 0,
                    });
                    return rows;
                };
                if (this.booking) {
                    const b = this.booking;
                    if (b.rentalAmount == null || b.reservationFee == null || b.balanceDueOnArrival == null) {
                        return null;
                    }
                    return build(
                        Number(b.rentalAmount),
                        Number(b.reservationFee),
                        Number(b.balanceDueOnArrival)
                    );
                }
                if (this.bookings && this.bookings.length) {
                    let rSum = 0;
                    let fSum = 0;
                    let amtSum = 0;
                    let resSum = 0;
                    let balSum = 0;
                    for (const b of this.bookings) {
                        rSum += Number(b.rentalAmount != null ? b.rentalAmount : 0);
                        fSum += Number(b.administrationFee != null ? b.administrationFee : 0);
                        amtSum += Number(b.amount != null ? b.amount : 0);
                        resSum += Number(b.reservationFee != null ? b.reservationFee : 0);
                        balSum += Number(b.balanceDueOnArrival != null ? b.balanceDueOnArrival : 0);
                    }
                    if (rSum <= 0 && fSum <= 0 && amtSum <= 0) return null;
                    return build(rSum, resSum, balSum);
                }
                return null;
            }

            /** ~420px template width on A4 */
            pdfPoaTemplateCardWidthMm(maxW) {
                return Math.min(111, maxW);
            }

            estimatePoaOverviewCardHeight(rows, hasDuration, templateMode = true, tripDateLong = '') {
                const pad = templateMode ? 8.5 : 4.2;
                let h = pad * 2 + (templateMode ? 18 : 14);
                if (tripDateLong) {
                    const safe = this.pdfSafe(tripDateLong);
                    const lineChars = templateMode ? 38 : 36;
                    const extraLines = Math.max(1, Math.ceil(safe.length / lineChars));
                    h += templateMode ? 5 + extraLines * 4.8 + 5 : 4 + extraLines * 4.2 + 4;
                }
                if (hasDuration) h += templateMode ? 9 : 7;
                h += templateMode ? 5 : 3;
                for (const r of rows) {
                    if (r.type === 'twocol' || r.type === 'twocol_dup') h += templateMode ? 5.8 : 6.2;
                    else if (r.type === 'hrule') h += templateMode ? 6.5 : 5.5;
                    else if (r.type === 'section_navy') h += templateMode ? 8 : 7.5;
                    else if (r.type === 'total_paid_row') h += templateMode ? 7.5 : 7;
                    else if (r.type === 'arrival_box') h += (r.showInclusief ? 24 : 18);
                }
                return h + (templateMode ? 6 : 5);
            }

            pdfPoaRoundedRect(doc, x, y, w, h, r, style) {
                if (typeof doc.roundedRect === 'function') {
                    doc.roundedRect(x, y, w, h, r, r, style);
                } else {
                    doc.rect(x, y, w, h, style);
                }
            }

            /** Main rows: gray-700 label, #0C2D48 amount; fixed amount column like flex justify-between. */
            pdfDrawPoaTwoColRow(doc, x, y, rightX, label, amount, textRight, bodyPt = 10.5) {
                const amtColW = 28;
                const gap = 2;
                const labelMaxW = Math.max(20, rightX - amtColW - gap - x);
                const rowH = bodyPt * 0.52 + 1.2;
                doc.setFont('helvetica', 'normal', bodyPt);
                doc.setTextColor(55, 65, 81);
                const labelLines = doc.splitTextToSize(this.pdfSafe(label), labelMaxW);
                doc.text(labelLines, x, y);
                const lineH = Math.max(rowH, labelLines.length * (bodyPt * 0.45));
                doc.setFont('helvetica', 'normal', bodyPt);
                doc.setTextColor(12, 45, 72);
                if (amount != null && Number.isFinite(Number(amount))) {
                    this.pdfDrawEuroAmountAlignRight(doc, rightX, y, Number(amount));
                } else if (textRight != null) {
                    doc.text(this.pdfSafe(textRight), rightX, y, { align: 'right' });
                }
                return y + lineH + (bodyPt >= 11 ? 0.6 : 0.9);
            }

            pdfDrawPoaTwoColDupRow(doc, x, y, rightX, label, amount, bodyPt = 10.5) {
                const n = Number(amount);
                const numStr = Number.isFinite(n) ? n.toFixed(2).replace('.', ',') : '';
                const leftStr = this.pdfSafe(`${label}: \u20AC ${numStr}`);
                const amtColW = 28;
                const leftMax = Math.max(20, rightX - amtColW - 2 - x);
                doc.setFont('helvetica', 'normal', bodyPt);
                doc.setTextColor(55, 65, 81);
                const leftLines = doc.splitTextToSize(leftStr, leftMax);
                doc.text(leftLines, x, y);
                const lineH = Math.max(bodyPt * 0.52 + 1.2, leftLines.length * (bodyPt * 0.45));
                doc.setFont('helvetica', 'normal', bodyPt);
                doc.setTextColor(12, 45, 72);
                this.pdfDrawEuroAmountAlignRight(doc, rightX, y, n);
                return y + lineH + 0.6;
            }

            pdfDrawPoaTotalPaidRow(doc, x, y, rightX, label, amount, bodyPt = 10.5) {
                const labelWithColon = /:\s*$/.test(label) ? label : `${label}:`;
                const labelMaxW = Math.max(22, rightX - x - 30);
                doc.setFont('helvetica', 'bold', bodyPt);
                doc.setTextColor(12, 45, 72);
                const labelLines = doc.splitTextToSize(this.pdfSafe(labelWithColon), labelMaxW);
                doc.text(labelLines, x, y);
                const lineH = Math.max(bodyPt * 0.55 + 1.2, labelLines.length * (bodyPt * 0.45));
                doc.setFont('helvetica', 'bold', bodyPt);
                this.pdfDrawEuroAmountAlignRight(doc, rightX, y, Number(amount));
                return y + lineH + 1.2;
            }

            pdfDrawPoaArrivalTotalBox(doc, x, y, w, amount, showFooter) {
                const padIn = 4;
                const boxH = showFooter ? 22 : 16;
                const r = 1.2;
                doc.setFillColor(217, 234, 245);
                doc.setDrawColor(12, 45, 72);
                doc.setLineWidth(0.35);
                this.pdfPoaRoundedRect(doc, x, y, w, boxH, r, 'FD');
                const rowY = y + padIn + 3.5;
                doc.setFont('helvetica', 'bold', 15);
                doc.setTextColor(12, 45, 72);
                const lab = this.pdfSafe(`${this.t('payment_success_pdf_poa_total_arrival')}:`);
                doc.text(lab, x + padIn, rowY);
                doc.setFont('helvetica', 'bold', 18);
                this.pdfDrawEuroAmountAlignRight(doc, x + w - padIn, rowY, amount);
                if (showFooter) {
                    doc.setFont('helvetica', 'normal', 9.5);
                    doc.setTextColor(12, 45, 72);
                    doc.text(this.pdfSafe(this.t('payment_success_pdf_poa_including_deposit')), x + w - padIn, y + boxH - 2.8, { align: 'right' });
                }
                return y + boxH + 3;
            }

            /**
             * @param {{ template?: boolean }} [opts] template=true: p-[32px], rounded card, shadow (HTML reference).
             */
            pdfDrawPoaPriceOverviewBlock(doc, cardX, topY, cardW, durationHint, rows, opts = {}) {
                if (!rows || !rows.length) return topY;
                const template = opts.template !== false;
                const tripDateLong = opts.tripDateLong || '';
                const h = this.estimatePoaOverviewCardHeight(rows, !!durationHint, template, tripDateLong);
                const alignOut = opts && opts.alignBottomOut;
                const rad = 1.4;
                if (template) {
                    doc.setFillColor(220, 222, 226);
                    this.pdfPoaRoundedRect(doc, cardX + 0.5, topY + 0.9, cardW, h, rad, 'F');
                }
                doc.setFillColor(255, 255, 255);
                doc.setDrawColor(209, 213, 219);
                doc.setLineWidth(0.3);
                this.pdfPoaRoundedRect(doc, cardX, topY, cardW, h, rad, 'FD');
                const pad = template ? 8.5 : 4.2;
                const innerLeft = cardX + pad;
                const innerRight = cardX + cardW - pad;
                let y = topY + pad;
                const titlePt = template ? 17 : 14;
                const subPt = template ? 12 : 11;
                const bodyPt = template ? 10.5 : 10;
                if (tripDateLong) {
                    doc.setFont('helvetica', 'bold');
                    doc.setFontSize(7);
                    doc.setTextColor(78, 95, 121);
                    doc.text(this.pdfSafe(this.t('payment_success_pdf_hero_date_label').toUpperCase()), innerLeft, y);
                    y += 5;
                    doc.setFontSize(template ? 12 : 11);
                    doc.setTextColor(0, 49, 94);
                    const dateLines = doc.splitTextToSize(this.pdfSafe(tripDateLong), innerRight - innerLeft);
                    doc.text(dateLines, innerLeft, y);
                    y += dateLines.length * (template ? 4.6 : 4.2);
                    y += template ? 5 : 4;
                }
                doc.setFont('helvetica', 'bold', titlePt);
                doc.setTextColor(12, 45, 72);
                doc.text(this.pdfSafe(this.t('payment_success_pdf_price_breakdown_title')), innerLeft, y);
                y += template ? 8 : 7;
                if (durationHint) {
                    doc.setFont('helvetica', 'bold', subPt);
                    doc.setTextColor(12, 45, 72);
                    doc.text(this.pdfSafe(durationHint), innerLeft, y);
                    y += template ? 7 : 6.5;
                }
                y += template ? 4 : 2;
                for (const row of rows) {
                    if (row.type === 'twocol') {
                        y = this.pdfDrawPoaTwoColRow(doc, innerLeft, y, innerRight, row.label, row.amount, row.textRight, bodyPt);
                    } else if (row.type === 'twocol_dup') {
                        y = this.pdfDrawPoaTwoColDupRow(doc, innerLeft, y, innerRight, row.label, row.amount, bodyPt);
                    } else if (row.type === 'hrule') {
                        y += template ? 2 : 0.5;
                        doc.setDrawColor(229, 231, 235);
                        doc.setLineWidth(0.35);
                        doc.line(innerLeft, y + 0.5, innerRight, y + 0.5);
                        y += template ? 5.5 : 5.2;
                    } else if (row.type === 'section_navy') {
                        y += template ? 1 : 0.5;
                        doc.setFont('helvetica', 'bold', subPt);
                        doc.setTextColor(12, 45, 72);
                        doc.text(this.pdfSafe(row.text), innerLeft, y);
                        y += template ? 6.5 : 6.2;
                    } else if (row.type === 'total_paid_row') {
                        y += template ? 1.2 : 0.5;
                        y = this.pdfDrawPoaTotalPaidRow(doc, innerLeft, y, innerRight, row.label, row.amount, bodyPt);
                    } else if (row.type === 'arrival_box') {
                        y += template ? 2 : 1.5;
                        y = this.pdfDrawPoaArrivalTotalBox(doc, innerLeft, y, innerRight - innerLeft, row.amount, row.showInclusief);
                    }
                }
                const innerBottomPad = template ? 8.5 : 5;
                const cardRectBottom = topY + h;
                const contentBottom = y + innerBottomPad;
                if (alignOut && typeof alignOut === 'object') {
                    alignOut.y = Math.max(cardRectBottom, contentBottom);
                }
                return topY + h + 3;
            }

            /** Centered card max ~111mm (Tailwind max-w-[420px]). */
            pdfDrawPoaPriceOverviewFullWidth(doc, margin, maxW, topY, durationHint, rows) {
                if (!rows || !rows.length) return topY;
                const cardW = this.pdfPoaTemplateCardWidthMm(maxW);
                const cardX = margin + (maxW - cardW) / 2;
                return this.pdfDrawPoaPriceOverviewBlock(doc, cardX, topY, cardW, durationHint || null, rows, { template: true });
            }

            pdfDrawPoaBreakdownSection(doc, margin, y, pageW, maxW, rows, durationHint = '') {
                if (!rows || !rows.length) return y;
                const need = this.estimatePoaOverviewCardHeight(rows, !!durationHint, true) + 12;
                y = this.pdfEnsureSpace(doc, y, need, margin);
                this.pdfDrawMutedRule(doc, margin, y, pageW);
                y += 6;
                return this.pdfDrawPoaPriceOverviewFullWidth(doc, margin, maxW, y, durationHint || null, rows);
            }

            async computeDepositTotalForPdf() {
                this.depositTotalForPdf = 0;
                if (this.booking && this.booking.boatType) {
                    const q = Math.max(1, parseInt(this.booking.quantity, 10) || 1);
                    const d = await this.getBoatDepositAmount(this.booking.boatType);
                    this.depositTotalForPdf += d * q;
                    return;
                }
                if (this.bookings && this.bookings.length) {
                    for (const b of this.bookings) {
                        const q = Math.max(1, parseInt(b.quantity, 10) || 1);
                        const d = await this.getBoatDepositAmount(b.boatType);
                        this.depositTotalForPdf += d * q;
                    }
                }
            }

            async downloadBookingPdf() {
                const JsPDF = this.getJsPdfConstructor();
                if (!JsPDF) {
                    window.alert(this.t('payment_success_pdf_unavailable'));
                    return;
                }
                const refRaw = this.cartId || this.bookingId || this.booking?.id;
                const hasLoadedBooking = !!(this.booking || (Array.isArray(this.bookings) && this.bookings.length));
                if (!hasLoadedBooking && !this.bookingId && !this.cartId) {
                    return;
                }
                try {
                    await this.ensurePdfLogo();
                    await this.computeDepositTotalForPdf();
                    const brand = this.readBrandForPdf();
                    const doc = new JsPDF({ unit: 'mm', format: 'a4' });
                    const ref = this.pdfSafe(refRaw || 'booking');
                    let pageW = doc.internal.pageSize.getWidth();
                    let pageH = doc.internal.pageSize.getHeight();
                    const margin = 18;
                    const line = 5;
                    const maxW = pageW - margin * 2;
                    const colGap = 7;

                    doc.setFillColor(249, 249, 255);
                    doc.rect(0, 0, pageW, pageH, 'F');

                    const badgeW = 34;
                    const badgeH = 15;
                    const topY = 12;
                    doc.setFillColor(0, 51, 102);
                    doc.rect(margin, topY, badgeW, badgeH, 'F');
                    if (this.logoDataUrlForPdf) {
                        try {
                            const lw = badgeW - 6;
                            const lh = lw * (67.5 / 200);
                            const lx = margin + (badgeW - lw) / 2;
                            const ly = topY + (badgeH - lh) / 2;
                            doc.addImage(this.logoDataUrlForPdf, 'PNG', lx, ly, lw, lh);
                        } catch (e) {
                            console.warn('PDF logo:', e);
                        }
                    }

                    const headerRightX = pageW - margin;
                    doc.setFont('helvetica', 'bold');
                    doc.setFontSize(14);
                    doc.text(this.pdfSafe(this.t('payment_success_pdf_heading')), headerRightX, topY + 5, { align: 'right' });
                    doc.setFontSize(9);
                    doc.setFont('helvetica', 'normal');
                    doc.setTextColor(66, 71, 80);
                    doc.text(this.pdfSafe(`${this.t('payment_success_reference_label')} ${ref}`), headerRightX, topY + 11, { align: 'right' });
                    doc.setFontSize(7.5);
                    doc.text(this.pdfSafe(`${this.t('payment_success_pdf_date_generated')} ${new Date().toISOString().slice(0, 10)}`), headerRightX, topY + 16, { align: 'right' });

                    doc.setTextColor(25, 28, 32);
                    doc.setFont('helvetica', 'normal');

                    let boatImgUrl = '';
                    let boatChip = '';
                    let heroDate = '';
                    let heroDateLong = '';
                    let durationHint = '';
                    if (this.booking) {
                        boatImgUrl = await this.getBoatImageUrl(this.booking.boatType);
                        boatChip = this.booking.boatName || this.booking.boatType || '';
                        heroDate = this.formatDate(this.booking.date);
                        heroDateLong = heroDate;
                        const d = this.booking.numberOfDays || 1;
                        durationHint = `${d} ${d === 1 ? this.t('checkout_day') : this.t('checkout_days')}`;
                    } else if (this.bookings && this.bookings.length) {
                        boatImgUrl = await this.getBoatImageUrl(this.bookings[0].boatType);
                        const names = await Promise.all(this.bookings.map((b) => this.getBoatName(b.boatType)));
                        boatChip = names.filter(Boolean).join(' | ');
                        heroDate = this.bookings.length === 1
                            ? this.formatDate(this.bookings[0].date)
                            : this.bookings.map((b) => b.date).filter(Boolean).join(', ');
                        heroDateLong = this.bookings.length === 1
                            ? this.formatDate(this.bookings[0].date)
                            : this.bookings.map((b) => b.date).filter(Boolean).map((d) => this.formatDate(d)).join(', ');
                        const d0 = this.bookings[0].numberOfDays || 1;
                        durationHint = this.bookings.length === 1
                            ? `${d0} ${d0 === 1 ? this.t('checkout_day') : this.t('checkout_days')}`
                            : '';
                    }
                    const poaBreakdownRows = this.getPoaPdfBreakdownRows();
                    const poaPriceInHero = !!(poaBreakdownRows && poaBreakdownRows.length);
                    const heroTotal = poaPriceInHero ? 0 : this.computePdfHeroTotalAmount();

                    const imgData = await this.loadRasterImageDataUrlForPdf(boatImgUrl);
                    let y = topY + badgeH + 11;
                    const heroImgW = poaPriceInHero ? maxW * 0.47 : maxW * 0.56;
                    const heroImgH = heroImgW * (poaPriceInHero ? 0.68 : 0.75);
                    const imgX = margin;
                    const imgY = y;

                    doc.setFillColor(237, 237, 243);
                    doc.rect(imgX, imgY, heroImgW, heroImgH, 'F');
                    if (imgData) {
                        try {
                            doc.addImage(imgData, 'JPEG', imgX, imgY, heroImgW, heroImgH);
                        } catch (e) {
                            console.warn('PDF boat image:', e);
                        }
                    }

                    doc.setFont('helvetica', 'bold');
                    doc.setFontSize(9);
                    const chipMaxW = heroImgW - 8;
                    const chipLines = doc.splitTextToSize(this.pdfSafe(boatChip || '—'), chipMaxW);
                    const chipPad = 3.5;
                    const chipW = Math.min(heroImgW - 4, chipMaxW + chipPad * 2);
                    const chipH = 4.5 + chipLines.length * 4;
                    const chipX = imgX + heroImgW - chipW - 3;
                    const chipY = imgY + heroImgH - chipH + 1.5;
                    doc.setFillColor(255, 219, 200);
                    doc.rect(chipX, chipY, chipW, chipH, 'F');
                    doc.setTextColor(50, 19, 0);
                    doc.text(chipLines, chipX + chipPad, chipY + 5);

                    const rx = margin + heroImgW + colGap;
                    const cardW = maxW - heroImgW - colGap;
                    let ry = imgY + 5;
                    let heroBottom;
                    /** Bottom edge of POA price card rect (excludes trailing mm after rounded card). */
                    let poaPriceCardBottomY = null;
                    if (poaPriceInHero && poaBreakdownRows && poaBreakdownRows.length) {
                        const alignBottomOut = {};
                        const cardBottom = this.pdfDrawPoaPriceOverviewBlock(
                            doc,
                            rx,
                            imgY,
                            cardW,
                            durationHint,
                            poaBreakdownRows,
                            {
                                template: true,
                                tripDateLong: heroDateLong || heroDate || '—',
                                alignBottomOut,
                            }
                        );
                        poaPriceCardBottomY = typeof alignBottomOut.y === 'number' ? alignBottomOut.y : cardBottom - 3;
                        heroBottom = Math.max(imgY + heroImgH, cardBottom, poaPriceCardBottomY);
                    } else {
                        doc.setFont('helvetica', 'bold');
                        doc.setFontSize(7);
                        doc.setTextColor(78, 95, 121);
                        doc.text(this.pdfSafe(this.t('payment_success_pdf_hero_date_label').toUpperCase()), rx, ry);
                        ry += 5;
                        doc.setFontSize(14);
                        doc.setTextColor(0, 49, 94);
                        doc.text(this.pdfSafe(heroDate || '—'), rx, ry);
                        ry += 9;
                        if (durationHint && !poaPriceInHero) {
                            doc.setFontSize(8);
                            doc.setFont('helvetica', 'normal');
                            doc.setTextColor(66, 71, 80);
                            doc.text(this.pdfSafe(durationHint), rx, ry);
                            ry += 6;
                        }
                        if (!this.isPayOnArrivalPdfMode()) {
                            doc.setFont('helvetica', 'bold');
                            doc.setFontSize(7);
                            doc.text(this.pdfSafe(this.t('payment_success_pdf_hero_total_label').toUpperCase()), rx, ry);
                            ry += 8;
                            doc.setFontSize(22);
                            doc.setTextColor(0, 49, 94);
                            this.pdfDrawEuroAmount(doc, rx, ry, heroTotal);
                            ry += 11;
                            doc.setFont('helvetica', 'normal');
                            doc.setFontSize(8);
                            doc.setTextColor(66, 71, 80);
                            const noteLines = doc.splitTextToSize(this.pdfSafe(this.t('payment_success_pdf_hero_total_note')), maxW - heroImgW - colGap);
                            doc.text(noteLines, rx, ry);
                            heroBottom = Math.max(imgY + heroImgH, ry + noteLines.length * 4.2);
                        } else {
                            heroBottom = Math.max(imgY + heroImgH, ry);
                        }
                    }
                    const pad = 5;
                    const depBody = this.depositTotalForPdf > 0
                        ? this.t('checkout_deposit_note').replace('{amount}', this.depositTotalForPdf.toFixed(2))
                        : this.t('price_deposit_none');

                    const poaUnderImageMerged =
                        poaPriceInHero && poaBreakdownRows && poaBreakdownRows.length;

                    if (poaUnderImageMerged) {
                        const colTextW = heroImgW - 2 * pad - 2;
                        const borgHead = doc.splitTextToSize(this.pdfSafe(this.t('payment_success_pdf_deposit_heading')), colTextW);
                        const borgBodyLines = doc.splitTextToSize(this.pdfSafe(depBody), colTextW);
                        const arrHead = doc.splitTextToSize(this.pdfSafe(this.t('payment_success_arrival_title')), colTextW);
                        const timeBlk = doc.splitTextToSize(this.pdfSafe(this.getArrivalTimeLine()), colTextW - 8);
                        const locBlk = doc.splitTextToSize(this.pdfSafe(this.getArrivalLocationLine()), colTextW - 8);
                        const contentH = this.pdfEstimateMergedBorgArrivalColumnHeight(
                            borgHead,
                            borgBodyLines,
                            this.depositTotalForPdf > 0,
                            arrHead,
                            timeBlk,
                            locBlk
                        );
                        const underY = imgY + heroImgH + 4;
                        /** Align left column bottom to POA price card bottom (not hero image bottom). */
                        const rightPanelBottomY =
                            typeof poaPriceCardBottomY === 'number' ? poaPriceCardBottomY : underY;
                        let mergeDrawY = underY;
                        const alignToCardH = Math.max(0, rightPanelBottomY - mergeDrawY);
                        let boxH = Math.max(contentH, alignToCardH, 48);
                        mergeDrawY = this.pdfEnsureSpace(doc, mergeDrawY, boxH + 14, margin);
                        if (mergeDrawY !== underY) {
                            /** New page: price card stayed on previous page — only fit content. */
                            boxH = Math.max(contentH, 48);
                        } else {
                            boxH = Math.max(
                                contentH,
                                48,
                                Math.max(0, rightPanelBottomY - mergeDrawY)
                            );
                        }
                        pageH = doc.internal.pageSize.getHeight();
                        pageW = doc.internal.pageSize.getWidth();
                        this.pdfDrawMergedBorgArrivalColumn(
                            doc,
                            imgX,
                            mergeDrawY,
                            heroImgW,
                            boxH,
                            borgHead,
                            borgBodyLines,
                            this.depositTotalForPdf,
                            arrHead,
                            timeBlk,
                            locBlk
                        );
                        y = Math.max(heroBottom, mergeDrawY + boxH) + 12;
                    } else {
                        y = heroBottom + 11;
                        const half = (maxW - colGap) / 2;
                        const leftX = margin;
                        const rightX = margin + half + colGap;
                        const leftW = half;
                        const rightW = half;

                        y = this.pdfEnsureSpace(doc, y, 72, margin);
                        pageH = doc.internal.pageSize.getHeight();
                        pageW = doc.internal.pageSize.getWidth();

                        const borgHead = doc.splitTextToSize(this.pdfSafe(this.t('payment_success_pdf_deposit_heading')), leftW - 2 * pad - 2);
                        const borgBodyLines = doc.splitTextToSize(this.pdfSafe(depBody), leftW - 2 * pad - 2);
                        let leftH = pad + borgHead.length * 5 + (this.depositTotalForPdf > 0 ? 11 : 4) + borgBodyLines.length * 4 + pad + 2;

                        const arrHead = doc.splitTextToSize(this.pdfSafe(this.t('payment_success_arrival_title')), rightW - 2 * pad);
                        const timeBlk = doc.splitTextToSize(this.pdfSafe(this.getArrivalTimeLine()), rightW - 2 * pad - 8);
                        const locBlk = doc.splitTextToSize(this.pdfSafe(this.getArrivalLocationLine()), rightW - 2 * pad - 8);
                        const rightH = pad + arrHead.length * 5 + 3 + 4 + timeBlk.length * 4 + 6 + locBlk.length * 4 + pad + 4;
                        const rowH = Math.max(leftH, rightH, 48);

                        doc.setFillColor(243, 243, 249);
                        doc.rect(leftX, y, leftW, rowH, 'F');
                        doc.setDrawColor(0, 49, 94);
                        doc.setLineWidth(0.7);
                        doc.line(leftX, y, leftX, y + rowH);

                        let yy = y + pad + 4;
                        doc.setFont('helvetica', 'bold');
                        doc.setFontSize(11);
                        doc.setTextColor(0, 49, 94);
                        doc.text(borgHead, leftX + pad + 1, yy);
                        yy += borgHead.length * 5 + 1;
                        if (this.depositTotalForPdf > 0) {
                            doc.setFontSize(16);
                            this.pdfDrawEuroAmount(doc, leftX + pad + 1, yy, this.depositTotalForPdf);
                            yy += 10;
                        }
                        doc.setFont('helvetica', 'normal');
                        doc.setFontSize(8.5);
                        doc.setTextColor(66, 71, 80);
                        doc.text(borgBodyLines, leftX + pad + 1, yy);

                        doc.setFillColor(255, 255, 255);
                        doc.setDrawColor(194, 198, 209);
                        doc.setLineWidth(0.2);
                        doc.rect(rightX, y, rightW, rowH, 'FD');

                        yy = y + pad + 4;
                        doc.setFont('helvetica', 'bold');
                        doc.setFontSize(11);
                        doc.setTextColor(0, 49, 94);
                        doc.text(arrHead, rightX + pad + 1, yy);
                        yy += arrHead.length * 5 + 2;
                        const sq = 4.3;
                        const iconX = rightX + pad + 1;
                        this.pdfDrawArrivalIconTile(doc, iconX, yy - 3, sq, 'clock');
                        doc.setFont('helvetica', 'bold');
                        doc.setFontSize(6.5);
                        doc.setTextColor(66, 71, 80);
                        doc.text(this.pdfSafe(this.t('payment_success_pdf_checkin_label').toUpperCase()), rightX + pad + sq + 3, yy);
                        yy += 3;
                        doc.setFont('helvetica', 'normal');
                        doc.setFontSize(9);
                        doc.setTextColor(25, 28, 32);
                        doc.text(timeBlk, rightX + pad + sq + 3, yy);
                        yy += timeBlk.length * 4 + 5;
                        this.pdfDrawArrivalIconTile(doc, iconX, yy - 3, sq, 'pin');
                        doc.setFont('helvetica', 'bold');
                        doc.setFontSize(6.5);
                        doc.setTextColor(66, 71, 80);
                        doc.text(this.pdfSafe(this.t('payment_success_arrival_location_label').toUpperCase()), rightX + pad + sq + 3, yy);
                        yy += 3;
                        doc.setFont('helvetica', 'normal');
                        doc.setFontSize(9);
                        doc.text(locBlk, rightX + pad + sq + 3, yy);

                        y += rowH + 12;
                    }

                    y = this.pdfEnsureSpace(doc, y, 62, margin);
                    pageH = doc.internal.pageSize.getHeight();
                    doc.setDrawColor(194, 198, 209);
                    doc.setLineWidth(0.15);
                    doc.line(margin, y, pageW - margin, y);
                    y += 8;

                    doc.setFont('helvetica', 'bold');
                    doc.setFontSize(9);
                    doc.setTextColor(0, 49, 94);
                    doc.text(this.pdfSafe(this.t('checkout_policy_title').toUpperCase()), margin, y);
                    y += 7;

                    const cw = (maxW - 10) / 3;
                    const cTitles = [
                        this.t('payment_success_pdf_col_cancellation'),
                        this.t('payment_success_pdf_col_bring'),
                        this.t('payment_success_pdf_col_practical'),
                    ];
                    const cBodies = [
                        this.isPayOnArrivalPdfMode()
                            ? this.t('checkout_policy_cancellation_poa')
                            : this.t('checkout_policy_cancellation'),
                        this.t('payment_success_arrival_bring_text'),
                    ];
                    let cBottom = y;
                    for (let i = 0; i < 3; i++) {
                        const cx = margin + i * (cw + 5);
                        doc.setFont('helvetica', 'bold');
                        doc.setFontSize(9);
                        doc.setTextColor(25, 28, 32);
                        const tl = doc.splitTextToSize(this.pdfSafe(cTitles[i]), cw);
                        doc.text(tl, cx, y);
                        let cy = y + tl.length * 4 + 2;
                        doc.setFont('helvetica', 'normal');
                        doc.setFontSize(7.5);
                        doc.setTextColor(66, 71, 80);
                        let bl;
                        if (i === 2) {
                            bl = [
                                ...doc.splitTextToSize(this.pdfSafe(this.t('checkout_policy_contact')), cw),
                                ...doc.splitTextToSize(this.pdfSafe(this.t('checkout_policy_location')), cw),
                            ];
                        } else {
                            bl = doc.splitTextToSize(this.pdfSafe(cBodies[i]), cw);
                        }
                        doc.text(bl, cx, cy);
                        cy += bl.length * 3.5;
                        cBottom = Math.max(cBottom, cy);
                    }
                    y = cBottom + 8;

                    y = this.pdfEnsureSpace(doc, y, 28, margin);
                    pageH = doc.internal.pageSize.getHeight();
                    const wishLines = doc.splitTextToSize(this.pdfSafe(this.t('payment_success_pdf_footer_wish')), maxW - 8);
                    const fh = 5 + wishLines.length * 4.1;
                    doc.setFillColor(0, 72, 132);
                    doc.rect(margin, y, maxW, fh, 'F');
                    doc.setTextColor(190, 215, 255);
                    doc.setFont('helvetica', 'bold');
                    doc.setFontSize(9);
                    doc.text(wishLines, margin + 4, y + 6);
                    y += fh + 8;

                    pageH = doc.internal.pageSize.getHeight();
                    const footY = Math.min(y + 6, pageH - 8);
                    doc.setDrawColor(210, 210, 220);
                    doc.line(margin, footY, pageW - margin, footY);
                    doc.setFont('helvetica', 'normal');
                    doc.setFontSize(7.5);
                    doc.setTextColor(114, 119, 129);
                    const footUrl = String(brand.siteUrl || '').replace(/^https?:\/\//, '');
                    const footLine = this.pdfSafe(`${footUrl}${brand.phone ? `  |  Tel. ${brand.phone}` : ''}`);
                    doc.text(footLine, pageW / 2, footY + 4.5, { align: 'center' });

                    doc.save(`nijenhuis-booking-${ref.replace(/[^a-zA-Z0-9_-]+/g, '_')}.pdf`);
                } catch (err) {
                    console.error('PDF generation failed:', err);
                    window.alert(this.t('payment_success_pdf_unavailable'));
                }
            }

            async init() {
                await Promise.all([
                    this.preloadPdfLogo(),
                    this.loadBookingData(),
                ]);
                this.processPayment();
            }

            async loadBookingData() {
                const urlParams = new URLSearchParams(window.location.search);
                const bookingId = urlParams.get('bookingId');
                const cartId = urlParams.get('cartId');
                const paymentId = urlParams.get('payment_id');

                this.bookingId = bookingId;
                this.cartId = cartId;

                if (!bookingId && !cartId) {
                    this.showError('Invalid booking reference. Please contact us for assistance.');
                    return;
                }

                try {
                    this.showLoading(true);
                    const statusUrl = bookingId
                        ? `/mollie_api.php?action=getBookingStatus&bookingId=${encodeURIComponent(bookingId)}`
                        : `/mollie_api.php?action=getCartStatus&cartId=${encodeURIComponent(cartId)}`;

                    let response;
                    let result;

                    try {
                        response = await fetch(statusUrl);
                        result = await response.json();
                    } catch (fetchError) {
                        console.warn('Primary fetch failed, trying JavaScript fallback:', fetchError);
                        if (paymentId && typeof getMolliePaymentStatusJS === 'function') {
                            try {
                                const paymentStatus = await getMolliePaymentStatusJS(paymentId, { bookingId, cartId });
                                result = {
                                    success: true,
                                    payment: paymentStatus,
                                    booking: { id: bookingId || cartId }
                                };
                            } catch (jsError) {
                                console.error('JavaScript fallback also failed:', jsError);
                                throw fetchError;
                            }
                        } else {
                            throw fetchError;
                        }
                    }

                    if (result.success && result.payOnArrival) {
                        this.payOnArrival = true;
                        this.payment = result.payment || null;
                        if (Array.isArray(result.bookings) && result.bookings.length) {
                            this.bookings = result.bookings;
                            await this.displayCartDetails();
                        } else if (result.booking) {
                            this.booking = result.booking;
                            await this.displayBookingDetails();
                        } else {
                            throw new Error(result.message || 'Booking not found');
                        }
                        this.setReferenceAndArrival();
                        await this.computeDepositTotalForPdf();
                        const titleEl = document.querySelector('.success-title');
                        if (titleEl) titleEl.textContent = this.t('payment_success_title_pay_on_arrival');
                        const subEl = document.querySelector('.success-subtitle');
                        if (subEl) subEl.textContent = this.t('payment_success_subtitle_pay_on_arrival');
                        this.showLoading(false);
                        const successBlock = document.getElementById('successContent');
                        if (successBlock) successBlock.style.display = '';
                        return;
                    }

                    if (result.success && result.booking) {
                        this.booking = result.booking;
                        this.payment = result.payment;
                        await this.displayBookingDetails();
                    } else if (result.success && Array.isArray(result.bookings)) {
                        this.bookings = result.bookings;
                        this.payment = result.payment;
                        await this.displayCartDetails();
                    } else {
                        throw new Error(result.message || 'Booking not found');
                    }

                    if (
                        this.payment &&
                        this.payment.status === 'paid' &&
                        ((this.booking &&
                            this.booking.paymentMethod === 'pay_on_arrival' &&
                            this.booking.payOnArrivalReservation) ||
                            (Array.isArray(this.bookings) &&
                                this.bookings[0] &&
                                this.bookings[0].paymentMethod === 'pay_on_arrival' &&
                                this.bookings[0].payOnArrivalReservation))
                    ) {
                        const titleElPoa = document.querySelector('.success-title');
                        const subElPoa = document.querySelector('.success-subtitle');
                        if (titleElPoa) titleElPoa.textContent = this.t('payment_success_title_pay_on_arrival');
                        if (subElPoa) subElPoa.textContent = this.t('payment_success_subtitle_pay_on_arrival');
                    }

                    this.setReferenceAndArrival();
                    await this.computeDepositTotalForPdf();

                    if (this.paymentSystem) {
                        const status = this.payment?.status || result.payment?.status;
                        if (this.paymentSystem.isPaymentCompleted(status)) {
                            this.showLoading(false);
                        } else if (this.paymentSystem.isPaymentFailed(status)) {
                            const params = new URLSearchParams();
                            if (bookingId) params.set('bookingId', bookingId);
                            if (cartId) params.set('cartId', cartId);
                            if (this.payment?.id) params.set('payment_id', this.payment.id);
                            window.location.href = `payment-failure.php?${params.toString()}`;
                            return;
                        } else if (status === 'open' || status === 'pending') {
                            this.showLoading(false);
                        } else {
                            this.showError('Payment status is: ' + (status || 'unknown') + '. Please check your email or contact us.');
                        }
                    } else {
                        if (this.payment?.status === 'paid' || this.payment?.status === 'open' || this.payment?.status === 'pending') {
                            this.showLoading(false);
                        } else {
                            this.showError('Payment status is: ' + (this.payment?.status || 'unknown') + '. Please check your email or contact us.');
                        }
                    }
                } catch (error) {
                    console.error('Error loading booking status:', error);
                    // Intentionally no localStorage fallback: client-side
                    // booking caches used to include customer PII (name,
                    // email, phone, address) and have been removed. If the
                    // server is unreachable, surface an error rather than
                    // showing stale, unverified data.
                    this.showError('Booking not found. Please contact us for assistance.');
                }
            }

            async displayBookingDetails() {
                const wrap = document.getElementById('bookingDetails');
                const boatName = this.escapeHTML(this.booking.boatName || await this.getBoatName(this.booking.boatType));
                const imgUrl = await this.getBoatImageUrl(this.booking.boatType);
                const price = Number(this.booking.amount || await this.calculatePrice(this.booking.boatType)).toFixed(2);
                const duration = this.booking.numberOfDays || 1;
                const dayWord = duration === 1 ? this.t('checkout_day') : this.t('checkout_days');
                const poaBook = this.booking.paymentMethod === 'pay_on_arrival';
                const poaRes = !!this.booking.payOnArrivalReservation && poaBook;
                const viaKey = this.payOnArrival || poaBook
                    ? 'payment_success_price_pay_on_arrival'
                    : 'payment_success_price_via_mollie';
                const via = this.escapeHTML(this.t(viaKey));
                const rentalAmt = this.booking.rentalAmount;
                const adminAmt = this.booking.administrationFee;
                const resFeeAmt = this.booking.reservationFee;
                const balanceDue = this.booking.balanceDueOnArrival;
                const totalLabelKey = this.payOnArrival || poaBook
                    ? 'payment_success_breakdown_total_due'
                    : 'payment_success_breakdown_total';
                let priceDl = '';
                if (poaRes && rentalAmt != null && resFeeAmt != null && balanceDue != null) {
                    const resPaid = Number(this.payment?.amount?.value || resFeeAmt).toFixed(2);
                    priceDl = `
                                <div>
                                    <dt>${this.escapeHTML(this.t('payment_success_breakdown_rental'))}</dt>
                                    <dd>€${Number(rentalAmt).toFixed(2)}</dd>
                                </div>
                                <div>
                                    <dt>${this.escapeHTML(this.t('payment_success_breakdown_reservation_fee'))}</dt>
                                    <dd>€${resPaid}</dd>
                                </div>
                                <div>
                                    <dt>${this.escapeHTML(this.t('payment_success_breakdown_balance_arrival'))}</dt>
                                    <dd>€${Number(balanceDue).toFixed(2)}</dd>
                                </div>`;
                } else if (rentalAmt != null && (Number(rentalAmt) > 0 || (adminAmt != null && Number(adminAmt) >= 0))) {
                    priceDl = `
                                <div>
                                    <dt>${this.escapeHTML(this.t('payment_success_breakdown_rental'))}</dt>
                                    <dd>€${Number(rentalAmt).toFixed(2)}</dd>
                                </div>
                                <div>
                                    <dt>${this.escapeHTML(this.t(totalLabelKey))}<span class="payment-success-price-note">${via}</span></dt>
                                    <dd>€${price}</dd>
                                </div>`;
                } else {
                    priceDl = `
                                <div>
                                    <dt>${this.escapeHTML(this.t('payment_success_price'))}<span class="payment-success-price-note">${via}</span></dt>
                                    <dd>€${price}</dd>
                                </div>`;
                }

                wrap.innerHTML = `
                    <article class="payment-success-card">
                        <div class="payment-success-card-media">
                            <img src="${imgUrl}" alt="${boatName}" width="400" height="192" loading="lazy" decoding="async">
                        </div>
                        <div class="payment-success-card-body">
                            <h3>${boatName}</h3>
                            <dl class="payment-success-dl">
                                <div>
                                    <dt>${this.escapeHTML(this.t('payment_success_date'))}</dt>
                                    <dd>${this.escapeHTML(this.formatDate(this.booking.date))}</dd>
                                </div>
                                <div>
                                    <dt>${this.escapeHTML(this.t('payment_success_duration'))}</dt>
                                    <dd>${duration} ${this.escapeHTML(dayWord)}</dd>
                                </div>
                                ${priceDl}
                            </dl>
                        </div>
                    </article>
                `;
            }

            async displayCartDetails() {
                const wrap = document.getElementById('bookingDetails');
                const first = this.bookings[0];
                const imgUrl = await this.getBoatImageUrl(first?.boatType);
                const titleParts = await Promise.all(this.bookings.map(async (b) =>
                    this.escapeHTML(b.boatName || await this.getBoatName(b.boatType))
                ));
                const title = titleParts.length === 1 ? titleParts[0] : titleParts.join(' · ');
                const cartIsPoa = this.payOnArrival || (first && first.paymentMethod === 'pay_on_arrival');
                const cartPoaWithRes = cartIsPoa && first && first.payOnArrivalReservation;
                let totalAmount;
                let via;
                let breakdownDl = '';
                if (cartIsPoa && !this.payment) {
                    totalAmount = this.bookings.reduce((s, b) => s + Number(b.amount || 0), 0).toFixed(2);
                    via = this.escapeHTML(this.t('payment_success_price_pay_on_arrival'));
                    const rSum = this.bookings.reduce((s, b) => s + Number(b.rentalAmount != null ? b.rentalAmount : 0), 0);
                    const totalLabelKey = 'payment_success_breakdown_total_due';
                    if (rSum > 0) {
                        breakdownDl = `
                                <div>
                                    <dt>${this.escapeHTML(this.t('payment_success_breakdown_rental'))}</dt>
                                    <dd>€${rSum.toFixed(2)}</dd>
                                </div>
                                <div>
                                    <dt>${this.escapeHTML(this.t(totalLabelKey))}<span class="payment-success-price-note">${via}</span></dt>
                                    <dd>€${totalAmount}</dd>
                                </div>`;
                    } else {
                        breakdownDl = `
                                <div>
                                    <dt>${this.escapeHTML(this.t('payment_success_price'))}<span class="payment-success-price-note">${via}</span></dt>
                                    <dd>€${totalAmount}</dd>
                                </div>`;
                    }
                } else if (cartPoaWithRes && this.payment) {
                    totalAmount = this.bookings.reduce((s, b) => s + Number(b.amount || 0), 0).toFixed(2);
                    via = this.escapeHTML(this.t('payment_success_price_pay_on_arrival'));
                    const rSum = this.bookings.reduce((s, b) => s + Number(b.rentalAmount != null ? b.rentalAmount : 0), 0);
                    const resSum = this.bookings.reduce((s, b) => s + Number(b.reservationFee != null ? b.reservationFee : 0), 0);
                    const balSum = this.bookings.reduce((s, b) => s + Number(b.balanceDueOnArrival != null ? b.balanceDueOnArrival : 0), 0);
                    const resPaid = Number(this.payment?.amount?.value || resSum).toFixed(2);
                    breakdownDl = `
                                <div>
                                    <dt>${this.escapeHTML(this.t('payment_success_breakdown_rental'))}</dt>
                                    <dd>€${rSum.toFixed(2)}</dd>
                                </div>
                                <div>
                                    <dt>${this.escapeHTML(this.t('payment_success_breakdown_reservation_fee'))}</dt>
                                    <dd>€${resPaid}</dd>
                                </div>
                                <div>
                                    <dt>${this.escapeHTML(this.t('payment_success_breakdown_balance_arrival'))}</dt>
                                    <dd>€${balSum.toFixed(2)}</dd>
                                </div>`;
                } else {
                    totalAmount = Number(this.payment?.amount?.value || 0).toFixed(2);
                    via = this.escapeHTML(this.t('payment_success_price_via_mollie'));
                    const meta = this.payment?.metadata || {};
                    const metaSub = meta.rentalSubtotal != null ? Number(meta.rentalSubtotal) : null;
                    if (metaSub != null && !isNaN(metaSub)) {
                        breakdownDl = `
                                <div>
                                    <dt>${this.escapeHTML(this.t('payment_success_breakdown_rental'))}</dt>
                                    <dd>€${metaSub.toFixed(2)}</dd>
                                </div>
                                <div>
                                    <dt>${this.escapeHTML(this.t('payment_success_breakdown_total'))}<span class="payment-success-price-note">${via}</span></dt>
                                    <dd>€${totalAmount}</dd>
                                </div>`;
                    } else {
                        breakdownDl = `
                                <div>
                                    <dt>${this.escapeHTML(this.t('payment_success_price'))}<span class="payment-success-price-note">${via}</span></dt>
                                    <dd>€${totalAmount}</dd>
                                </div>`;
                    }
                }

                const lines = await Promise.all(this.bookings.map(async (booking) => {
                    const boatName = this.escapeHTML(booking.boatName || await this.getBoatName(booking.boatType));
                    const dateRange = this.escapeHTML(this.formatDateRange(booking.date, booking.endDate));
                    const price = Number(booking.amount || 0).toFixed(2);
                    return `<div class="payment-success-cart-line">${boatName} — ${dateRange} · €${price}</div>`;
                }));

                wrap.innerHTML = `
                    <article class="payment-success-card">
                        <div class="payment-success-card-media">
                            <img src="${imgUrl}" alt="" width="400" height="192" loading="lazy" decoding="async">
                        </div>
                        <div class="payment-success-card-body">
                            <h3>${title}</h3>
                            <div class="payment-success-cart-lines">
                                ${lines.join('')}
                            </div>
                            <dl class="payment-success-dl">
                                ${breakdownDl}
                            </dl>
                        </div>
                    </article>
                `;
            }

            async processPayment() {
                if (this.booking && this.booking.id) {
                    const bookings = JSON.parse(localStorage.getItem('nijenhuis_bookings') || '[]');
                    const bookingIndex = bookings.findIndex(b => b.id === this.booking.id);

                    if (bookingIndex !== -1) {
                        const status = (this.payment?.status || '').toLowerCase();
                        if (status === 'paid') {
                            bookings[bookingIndex].status = 'paid';
                            bookings[bookingIndex].paymentDate = new Date().toISOString();
                        } else if (['failed', 'expired', 'canceled'].includes(status)) {
                            bookings[bookingIndex].status = 'canceled';
                        } else if (status) {
                            bookings[bookingIndex].status = status;
                        }
                        localStorage.setItem('nijenhuis_bookings', JSON.stringify(bookings));
                    }
                }
            }

            async getBoatName(boatType) {
                if (window.BoatDataService) {
                    try {
                        return await window.BoatDataService.getBoatDisplayName(boatType);
                    } catch (e) {
                        console.warn('Error getting boat name from service:', e);
                    }
                }

                try {
                    const stored = localStorage.getItem('nijenhuis_boats');
                    if (stored) {
                        const boats = JSON.parse(stored);
                        const boat = boats.find(b => b.id === boatType);
                        if (boat) return boat.name;
                    }
                } catch (e) {
                    console.warn('Error loading boat from localStorage:', e);
                }

                return boatType;
            }

            async calculatePrice(boatType) {
                if (window.BoatDataService) {
                    try {
                        return await window.BoatDataService.getBoatPrice(boatType, 1);
                    } catch (e) {
                        console.warn('Error getting boat price from service:', e);
                    }
                }

                try {
                    const stored = localStorage.getItem('nijenhuis_boats');
                    if (stored) {
                        const boats = JSON.parse(stored);
                        const boat = boats.find(b => b.id === boatType);
                        if (boat) return boat.pricePerDay || 0;
                    }
                } catch (e) {
                    console.warn('Error loading boat price from localStorage:', e);
                }

                return 50;
            }

            formatDate(dateString) {
                const date = new Date(dateString);
                const lang = localStorage.getItem('selected-language') || 'nl';
                const locales = { nl: 'nl-NL', de: 'de-DE', en: 'en-US' };
                return date.toLocaleDateString(locales[lang] || 'nl-NL', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
            }

            formatDateRange(startDate, endDate) {
                if (!endDate || endDate === startDate) {
                    return this.formatDate(startDate);
                }
                return `${this.formatDate(startDate)} – ${this.formatDate(endDate)}`;
            }

            showError(message) {
                const errorDiv = document.getElementById('errorMessage');
                errorDiv.textContent = message;
                errorDiv.style.display = 'block';
                const sc = document.getElementById('successContent');
                if (sc) sc.style.display = 'none';
                document.getElementById('loading').style.display = 'none';
                const pdfBtn = document.getElementById('downloadPdfBtn');
                if (pdfBtn) pdfBtn.disabled = true;
            }

            showLoading(show) {
                document.getElementById('loading').style.display = show ? 'block' : 'none';
                const sc = document.getElementById('successContent');
                if (sc) sc.style.display = show ? 'none' : 'block';
                const pdfBtn = document.getElementById('downloadPdfBtn');
                if (pdfBtn) pdfBtn.disabled = show;
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            new PaymentSuccessPage();
        });
    </script>
</body>
</html>