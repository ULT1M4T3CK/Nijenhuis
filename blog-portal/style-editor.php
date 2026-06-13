<?php
require_once __DIR__ . '/portal-headers.php';
require_once __DIR__ . '/blog-auth.php';
require_once __DIR__ . '/../components/config.php';
$blogPortalNavActive = 'style';
$blogPortalHighlightNew = false;
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stijl aanpassen - Blog Portal - <?php echo SITE_NAME; ?></title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="icon" type="image/svg+xml" href="<?php echo assetPath('frontend/Images/logo-white.svg'); ?>">
    <link rel="apple-touch-icon" href="<?php echo assetPath('frontend/Images/logo-white.svg'); ?>">
    <link rel="stylesheet" href="<?php echo assetPath('blog-portal/blog-portal.css'); ?>?v=6">
</head>
<body class="blog-portal-dark">
<div class="blog-portal-app-shell">
    <?php include __DIR__ . '/partials/sidebar.php'; ?>

    <div class="blog-portal-workspace">
        <header class="blog-portal-page-head">
            <div class="blog-portal-page-head-text">
                <span class="blog-portal-kicker">Website content</span>
                <h1 class="blog-portal-page-title">Stijl &amp; typografie</h1>
                <p class="blog-portal-page-desc">Pas het uiterlijk van blogartikelen aan. Wijzigingen zijn direct zichtbaar op de website.</p>
            </div>
        </header>

        <div class="blog-portal-main">
            <div class="blog-portal-style-editor">
                <div class="blog-portal-style-form blog-portal-card">
                    <div class="blog-portal-form-row">
                        <label for="articleLinkColor">Linkkleur</label>
                        <input type="color" id="articleLinkColor" value="#0071BB">
                        <input type="text" id="articleLinkColorText" value="#0071BB" class="blog-portal-color-text color-text">
                    </div>
                    <div class="blog-portal-form-row">
                        <label for="articleH1Size">H1 grootte (rem)</label>
                        <input type="number" id="articleH1Size" class="blog-portal-input blog-portal-input-narrow" value="1.75" step="0.05" min="1">
                    </div>
                    <div class="blog-portal-form-row">
                        <label for="articleH2Size">H2 grootte (rem)</label>
                        <input type="number" id="articleH2Size" class="blog-portal-input blog-portal-input-narrow" value="1.35" step="0.05" min="1">
                    </div>
                    <div class="blog-portal-form-row">
                        <label for="articleH3Size">H3 grootte (rem)</label>
                        <input type="number" id="articleH3Size" class="blog-portal-input blog-portal-input-narrow" value="1.15" step="0.05" min="1">
                    </div>
                    <div class="blog-portal-form-row">
                        <label for="articleBodyLineHeight">Tekstregelhoogte</label>
                        <input type="number" id="articleBodyLineHeight" class="blog-portal-input blog-portal-input-narrow" value="1.7" step="0.1" min="1">
                    </div>
                    <div class="blog-portal-form-row">
                        <label for="articleMaxWidth">Artikelbreedte (px)</label>
                        <input type="number" id="articleMaxWidth" class="blog-portal-input blog-portal-input-narrow" value="800" min="400">
                    </div>
                    <div class="blog-portal-form-row">
                        <label for="articleTextColor">Tekstkleur</label>
                        <input type="color" id="articleTextColor" value="#333333">
                        <input type="text" id="articleTextColorText" value="#333333" class="blog-portal-color-text color-text">
                    </div>
                    <div class="blog-portal-form-row">
                        <label for="articleBlockquoteBg">Blockquote achtergrond</label>
                        <input type="color" id="articleBlockquoteBg" value="#f8f9fa">
                        <input type="text" id="articleBlockquoteBgText" value="#f8f9fa" class="blog-portal-color-text color-text">
                    </div>
                    <div class="blog-portal-form-actions">
                        <button type="button" class="blog-portal-btn-primary" id="saveStylesBtn">Opslaan</button>
                        <span id="saveStatus" class="blog-portal-save-status"></span>
                    </div>
                </div>
                <div class="blog-portal-style-preview">
                    <h3 class="blog-portal-rail-title">Voorbeeld</h3>
                    <div class="blog-portal-preview-box">
                        <div id="stylePreview" class="blog-article-content content-prose">
                            <h1>Voorbeeld titel</h1>
                            <p>Dit is voorbeeldtekst met een <a href="/blog">link</a> erin. De stijl die je hierboven aanpast wordt direct in dit voorbeeld weergegeven.</p>
                            <h2>Onderkop</h2>
                            <p>Meer tekst met <strong>vetgedrukte</strong> woorden.</p>
                            <blockquote>Dit is een citaat of tipsectie.</blockquote>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style id="previewStyleInject"></style>
<script>
    const apiUrl = window.location.origin + '/blog-portal/api.php';
    let blogCsrf = '';
    async function refreshBlogCsrf() {
        const res = await fetch(apiUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            credentials: 'include',
            body: JSON.stringify({ action: 'blogSession' })
        });
        const d = await res.json();
        if (d.csrfToken) blogCsrf = d.csrfToken;
    }
    const fields = ['articleLinkColor', 'articleH1Size', 'articleH2Size', 'articleH3Size', 'articleBodyLineHeight', 'articleMaxWidth', 'articleTextColor', 'articleBlockquoteBg'];

    function getValues() {
        const v = {};
        v.articleLinkColor = document.getElementById('articleLinkColor').value;
        document.getElementById('articleLinkColorText').value = v.articleLinkColor;
        v.articleH1Size = document.getElementById('articleH1Size').value + 'rem';
        v.articleH2Size = document.getElementById('articleH2Size').value + 'rem';
        v.articleH3Size = document.getElementById('articleH3Size').value + 'rem';
        v.articleBodyLineHeight = document.getElementById('articleBodyLineHeight').value;
        v.articleMaxWidth = document.getElementById('articleMaxWidth').value + 'px';
        v.articleTextColor = document.getElementById('articleTextColor').value;
        document.getElementById('articleTextColorText').value = v.articleTextColor;
        v.articleBlockquoteBg = document.getElementById('articleBlockquoteBg').value;
        document.getElementById('articleBlockquoteBgText').value = v.articleBlockquoteBg;
        return v;
    }

    function updatePreview() {
        const v = getValues();
        const css = `
            #stylePreview { max-width: ${v.articleMaxWidth}; }
            #stylePreview a { color: ${v.articleLinkColor}; }
            #stylePreview h1 { font-size: ${v.articleH1Size}; }
            #stylePreview h2 { font-size: ${v.articleH2Size}; }
            #stylePreview h3 { font-size: ${v.articleH3Size}; }
            #stylePreview p { line-height: ${v.articleBodyLineHeight}; color: ${v.articleTextColor}; }
            #stylePreview blockquote { background: ${v.articleBlockquoteBg}; }
        `;
        document.getElementById('previewStyleInject').textContent = css;
    }

    function bindInputs() {
        fields.forEach(f => {
            const el = document.getElementById(f);
            if (el) el.addEventListener('input', updatePreview);
            const textEl = document.getElementById(f + 'Text');
            if (textEl) {
                textEl.addEventListener('input', () => {
                    if (/^#[0-9A-Fa-f]{6}$/.test(textEl.value)) {
                        document.getElementById(f).value = textEl.value;
                        updatePreview();
                    }
                });
            }
        });
    }

    async function loadStyles() {
        try {
            const res = await fetch(apiUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                credentials: 'include',
                body: JSON.stringify({ action: 'getBlogStyles' })
            });
            const data = await res.json();
            if (data.success && data.styles) {
                const s = data.styles;
                if (s.articleLinkColor) document.getElementById('articleLinkColor').value = s.articleLinkColor;
                if (s.articleH1Size) document.getElementById('articleH1Size').value = parseFloat(s.articleH1Size);
                if (s.articleH2Size) document.getElementById('articleH2Size').value = parseFloat(s.articleH2Size);
                if (s.articleH3Size) document.getElementById('articleH3Size').value = parseFloat(s.articleH3Size);
                if (s.articleBodyLineHeight) document.getElementById('articleBodyLineHeight').value = s.articleBodyLineHeight;
                if (s.articleMaxWidth) document.getElementById('articleMaxWidth').value = parseInt(s.articleMaxWidth, 10);
                if (s.articleTextColor) document.getElementById('articleTextColor').value = s.articleTextColor;
                if (s.articleBlockquoteBg) document.getElementById('articleBlockquoteBg').value = s.articleBlockquoteBg;
                document.getElementById('articleLinkColorText').value = document.getElementById('articleLinkColor').value;
                document.getElementById('articleTextColorText').value = document.getElementById('articleTextColor').value;
                document.getElementById('articleBlockquoteBgText').value = document.getElementById('articleBlockquoteBg').value;
            }
        } catch (_) {}
        updatePreview();
    }

    document.getElementById('saveStylesBtn').addEventListener('click', async () => {
        const v = getValues();
        const status = document.getElementById('saveStatus');
        status.textContent = 'Bezig...';
        try {
            await refreshBlogCsrf();
            const res = await fetch(apiUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': blogCsrf },
                credentials: 'include',
                body: JSON.stringify({ action: 'saveBlogStyles', styles: v })
            });
            const data = await res.json();
            if (data.success) {
                status.textContent = 'Opgeslagen!';
                setTimeout(() => status.textContent = '', 2000);
            } else {
                status.textContent = data.message || 'Opslaan mislukt.';
            }
        } catch (err) {
            status.textContent = 'Fout: ' + err.message;
        }
    });

    bindInputs();
    loadStyles();
</script>
</body>
</html>
