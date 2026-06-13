<?php
require_once __DIR__ . '/portal-headers.php';
require_once __DIR__ . '/blog-auth.php';
require_once __DIR__ . '/../components/config.php';

$slug = $_GET['slug'] ?? '';
$isNew = ($slug === '' || $slug === 'new');
$pageTitle = $isNew ? 'Nieuw artikel' : 'Artikel bewerken';
$blogPortalNavActive = 'editor';
$blogPortalHighlightNew = $isNew;
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?> - Blog Portal - <?php echo SITE_NAME; ?></title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="icon" type="image/svg+xml" href="<?php echo assetPath('frontend/Images/logo-white.svg'); ?>">
    <link rel="apple-touch-icon" href="<?php echo assetPath('frontend/Images/logo-white.svg'); ?>">
    <link rel="stylesheet" href="<?php echo assetPath('blog-portal/blog-portal.css'); ?>?v=7">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/easymde@2.18.0/dist/easymde.min.css">
    <link rel="stylesheet" href="<?php echo assetPath('frontend/css/pages/blog.css'); ?>">
</head>
<body class="blog-portal-dark blog-portal-editor-page">
<div class="blog-portal-app-shell">
    <?php include __DIR__ . '/partials/sidebar.php'; ?>

    <div class="blog-portal-workspace">
        <header class="blog-portal-page-head ute-page-header">
            <div class="blog-portal-page-head-text">
                <span class="blog-portal-kicker ute-eyebrow">Website content</span>
                <h1 class="blog-portal-page-title ute-page-title"><?php echo htmlspecialchars($pageTitle); ?></h1>
                <p class="blog-portal-page-desc ute-page-copy">Schrijf en publiceer artikelen voor de openbare blog.</p>
            </div>
            <div class="blog-portal-toolbar-actions ute-header-actions">
                <a href="/blog-portal/dashboard" class="blog-portal-btn-outline">← Terug naar artikelen</a>
                <button type="button" class="blog-portal-btn-outline" id="btnPreview" title="Open de publieke pagina">Voorbeeld</button>
                <button type="button" class="blog-portal-btn-outline" id="btnImportMd">Import Markdown</button>
                <input type="file" id="importMdInput" accept=".md,text/markdown" style="display:none" aria-hidden="true">
                <?php if (!$isNew): ?>
                <button type="button" class="blog-portal-btn-danger-outline" id="btnDelete">Verwijderen</button>
                <?php endif; ?>
            </div>
        </header>

        <div class="blog-portal-edit-shell ute-page-section">
            <form id="articleForm" class="blog-portal-edit-grid ute-editor-grid" novalidate>
                <div class="blog-portal-center-pane ute-editor-main">
                    <div class="blog-portal-card ute-panel">
                        <div class="ute-form-grid ute-slug-grid">
                            <div class="ute-field">
                                <label for="slug" class="ute-field-label">Slug</label>
                                <input type="text" id="slug" name="slug" class="blog-portal-input" required pattern="[a-z0-9\-]+" placeholder="bijv. boot-huren-giethoorn" autocomplete="off">
                            </div>
                            <div class="ute-field">
                                <span class="ute-field-label">Gebruik titel</span>
                                <button type="button" class="blog-portal-btn-ghost ute-button-full" id="btnGenerateSlug" title="Slug uit titel (actieve taal)">Slug uit actieve taal</button>
                            </div>
                        </div>

                        <div class="blog-portal-lang-tabs" role="tablist">
                            <button type="button" class="blog-portal-tab active" data-lang="nl" role="tab" aria-selected="true">Nederlands</button>
                            <button type="button" class="blog-portal-tab" data-lang="en" role="tab" aria-selected="false">English</button>
                            <button type="button" class="blog-portal-tab" data-lang="de" role="tab" aria-selected="false">Deutsch</button>
                        </div>

                        <?php foreach (['nl', 'en', 'de'] as $lang): ?>
                        <div class="blog-portal-lang-panel" data-lang="<?php echo $lang; ?>" style="<?php echo $lang === 'nl' ? '' : 'display:none'; ?>">
                            <div class="blog-portal-copy-row">
                                <?php foreach (['nl' => 'NL', 'en' => 'EN', 'de' => 'DE'] as $src => $label): ?>
                                <?php if ($src !== $lang): ?>
                                <button type="button" class="blog-portal-copy-btn" data-from="<?php echo $src; ?>" data-to="<?php echo $lang; ?>">Kopiëren van <?php echo $label; ?></button>
                                <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                            <div class="ute-form-grid">
                                <div class="ute-field ute-field-span-2">
                                    <label for="title_<?php echo $lang; ?>" class="ute-field-label">Titel</label>
                                    <input type="text" id="title_<?php echo $lang; ?>" name="title_<?php echo $lang; ?>" class="lang-field blog-portal-input" data-lang="<?php echo $lang; ?>" data-field="title" placeholder="Titel">
                                </div>
                                <div class="ute-field ute-field-span-2">
                                    <label for="excerpt_<?php echo $lang; ?>" class="ute-field-label">Samenvatting</label>
                                    <textarea id="excerpt_<?php echo $lang; ?>" name="excerpt_<?php echo $lang; ?>" class="lang-field blog-portal-textarea blog-portal-textarea-sm" data-lang="<?php echo $lang; ?>" data-field="excerpt" rows="3" placeholder="Korte intro voor kaarten en previews"></textarea>
                                </div>
                                <div class="ute-field ute-field-span-2">
                                    <label for="keyTakeaways_<?php echo $lang; ?>" class="ute-field-label">Belangrijkste punten</label>
                                    <div class="blog-portal-body-actions blog-portal-takeaways-actions">
                                        <button type="button" class="blog-portal-btn-ghost" data-lang="<?php echo $lang; ?>" data-takeaways-bold title="Selectie omsluiten met ** (Markdown vet)">Vet</button>
                                    </div>
                                    <textarea id="keyTakeaways_<?php echo $lang; ?>" name="keyTakeaways_<?php echo $lang; ?>" class="lang-field blog-portal-textarea blog-portal-textarea-sm" data-lang="<?php echo $lang; ?>" data-field="keyTakeaways" rows="4" placeholder="Één punt per regel (gebruik **vet** binnen een regel)"></textarea>
                                </div>
                            </div>
                            <section class="ute-editor-section">
                                <header class="ute-editor-header">
                                    <h2 class="ute-section-title">Artikelinhoud (Markdown)</h2>
                                    <p class="ute-section-copy">Gebruik koppen, lijsten, citaten, links, codeblokken en inline afbeeldingen.</p>
                                </header>
                                <div class="blog-portal-body-actions">
                                    <button type="button" class="blog-portal-btn-ghost" data-lang="<?php echo $lang; ?>" data-body-upload>Afbeelding</button>
                                    <input type="file" accept="image/*" class="blog-portal-upload-input" data-lang="<?php echo $lang; ?>" style="display:none">
                                    <button type="button" class="blog-portal-btn-ghost" data-lang="<?php echo $lang; ?>" data-cheatsheet>Markdown-hulp</button>
                                </div>
                                <div class="blog-portal-cheatsheet-panel" data-lang="<?php echo $lang; ?>" style="display:none">
                                    <pre class="blog-portal-cheatsheet"># H1  ## H2  *vet*  [link](url)  ![img](url)</pre>
                                </div>
                                <div class="ute-mde-shell">
                                    <textarea id="body_<?php echo $lang; ?>" name="body_<?php echo $lang; ?>" class="lang-field lang-body" data-lang="<?php echo $lang; ?>" data-field="body"></textarea>
                                </div>
                            </section>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <details class="blog-portal-card blog-portal-advanced ute-panel">
                        <summary>Artikelstijl (geavanceerd)</summary>
                        <div class="blog-portal-form-row">
                            <label for="styleLinkColor">Linkkleur</label>
                            <input type="color" id="styleLinkColor" value="#1f6fac">
                            <input type="text" id="styleLinkColorText" value="#1f6fac" class="blog-portal-color-text">
                        </div>
                        <div class="blog-portal-form-row">
                            <label for="styleMaxWidth">Max. breedte (px)</label>
                            <input type="number" id="styleMaxWidth" class="blog-portal-input blog-portal-input-narrow" value="" min="400" placeholder="Standaard">
                        </div>
                    </details>
                </div>

                <aside class="blog-portal-rail ute-editor-sidebar">
                    <div class="blog-portal-card blog-portal-rail-block ute-panel ute-form-stack">
                        <h2 class="blog-portal-rail-title ute-section-title">Publiceren</h2>
                        <div class="blog-portal-form-row">
                            <label for="postStatus">Status</label>
                            <select id="postStatus" class="blog-portal-select">
                                <option value="draft">Concept</option>
                                <option value="published">Gepubliceerd</option>
                            </select>
                        </div>
                        <div class="blog-portal-form-row">
                            <label for="publishAt">Publicatiedatum &amp; tijd</label>
                            <input type="datetime-local" id="publishAt" class="blog-portal-input">
                        </div>
                        <div class="blog-portal-rail-actions">
                            <button type="button" class="blog-portal-btn-gradient" id="btnSaveDraft">Concept opslaan</button>
                            <button type="button" class="blog-portal-btn-primary" id="btnPublishPost">Publiceren</button>
                            <a href="/blog-portal/dashboard" class="blog-portal-link-quiet">Annuleren</a>
                        </div>
                    </div>

                    <div class="blog-portal-card blog-portal-rail-block ute-panel ute-form-stack">
                        <h2 class="blog-portal-rail-title ute-section-title">Omslagafbeelding</h2>
                        <div class="blog-portal-form-row">
                            <label for="featuredImage">Afbeeldings-URL</label>
                            <input type="text" id="featuredImage" class="blog-portal-input" placeholder="/frontend/Images/blog/...">
                        </div>
                        <button type="button" class="blog-portal-btn-outline blog-portal-btn-block" id="featuredImageUploadBtn">Uploaden</button>
                        <input type="file" accept="image/*" id="featuredImageInput" style="display:none" aria-hidden="true">
                        <div id="featuredImagePreview" class="blog-portal-cover-preview ute-image-preview" style="display:none"></div>
                    </div>

                    <?php foreach (['nl', 'en', 'de'] as $lang): ?>
                    <div class="blog-portal-card blog-portal-rail-block blog-portal-seo-panel ute-panel ute-form-stack" data-lang="<?php echo $lang; ?>" style="<?php echo $lang === 'nl' ? '' : 'display:none'; ?>">
                        <h2 class="blog-portal-rail-title ute-section-title">SEO <span class="blog-portal-lang-badge"><?php echo strtoupper($lang); ?></span></h2>
                        <div class="blog-portal-form-row">
                            <label for="metaTitle_<?php echo $lang; ?>">Meta-titel</label>
                            <input type="text" id="metaTitle_<?php echo $lang; ?>" class="lang-field blog-portal-input" data-lang="<?php echo $lang; ?>" data-field="metaTitle" placeholder="Optioneel voor browsertitel / Zoekresultaat">
                        </div>
                        <div class="blog-portal-form-row">
                            <label for="description_<?php echo $lang; ?>">Meta-omschrijving</label>
                            <textarea id="description_<?php echo $lang; ?>" class="lang-field blog-portal-textarea blog-portal-textarea-sm" data-lang="<?php echo $lang; ?>" data-field="description" rows="3" placeholder="Zoekmachines en social preview"></textarea>
                        </div>
                        <div class="blog-portal-form-row">
                            <label for="keywords_<?php echo $lang; ?>">Keywords</label>
                            <input type="text" id="keywords_<?php echo $lang; ?>" class="lang-field blog-portal-input" data-lang="<?php echo $lang; ?>" data-field="keywords" placeholder="woord1, woord2">
                        </div>
                        <div class="blog-portal-form-row">
                            <label for="metaImage_<?php echo $lang; ?>">Sociale preview-afbeelding URL</label>
                            <input type="text" id="metaImage_<?php echo $lang; ?>" class="lang-field blog-portal-input" data-lang="<?php echo $lang; ?>" data-field="metaImage" placeholder="https://… of pad op deze site">
                        </div>
                    </div>
                    <?php endforeach; ?>

                    <div class="blog-portal-card blog-portal-rail-block ute-panel ute-form-stack">
                        <h2 class="blog-portal-rail-title ute-section-title">URL &amp; indexering</h2>
                        <div class="blog-portal-form-row">
                            <label for="canonicalUrl">Canonical URL</label>
                            <input type="text" id="canonicalUrl" class="blog-portal-input" placeholder="<?php echo htmlspecialchars(rtrim(SITE_URL, '/') . '/blog/voorbeeld'); ?>">
                        </div>
                        <label class="blog-portal-check">
                            <input type="checkbox" id="noindex" value="1">
                            <span>Zoekmachines dit bericht laten negeren (noindex)</span>
                        </label>
                    </div>
                </aside>
            </form>
        </div>
    </div>
</div>

<div id="imageUploadModal" class="blog-portal-modal" style="display:none" aria-hidden="true">
    <div id="imageUploadModalOverlay" class="blog-portal-modal-overlay"></div>
    <div class="blog-portal-modal-content">
        <div class="blog-portal-modal-header">
            <h3>Afbeelding invoegen</h3>
            <button type="button" id="imageUploadModalClose" class="blog-portal-modal-close" aria-label="Sluiten">&times;</button>
        </div>
        <div class="blog-portal-modal-body">
            <img id="imageUploadPreview" src="" alt="" style="max-width:100%;max-height:200px;object-fit:contain;margin-bottom:1rem">
            <label for="imageUploadAlt">Alt-tekst</label>
            <input type="text" id="imageUploadAlt" class="blog-portal-input" placeholder="Beschrijving">
        </div>
        <div class="blog-portal-modal-footer">
            <button type="button" class="blog-portal-btn-ghost" onclick="document.getElementById('imageUploadModal').style.display='none'">Sluiten</button>
            <button type="button" class="blog-portal-btn-primary" id="imageUploadInsert">Invoegen</button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/easymde@2.18.0/dist/easymde.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/turndown@7.2.0/dist/turndown.js"></script>
<script>
(function () {
    const apiUrl = window.location.origin + '/blog-portal/api.php';
    const isNew = <?php echo $isNew ? 'true' : 'false'; ?>;
    const editSlug = <?php echo json_encode($isNew ? '' : $slug); ?>;
    const bodyEditors = {};
    let formDirty = false;
    let activeLang = 'nl';
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

    function slugify(s) {
        return ('' + s).trim().toLowerCase()
            .normalize('NFKD').replace(/[\u0300-\u036f]/g, '')
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/-+/g, '-')
            .replace(/^-|-$/g, '');
    }

    /** Shared HTML → Markdown for rich paste (headings, lists, bold, links, etc.). */
    let uteTurndown = null;
    function getTurndown() {
        if (uteTurndown) return uteTurndown;
        if (typeof TurndownService === 'undefined') return null;
        uteTurndown = new TurndownService({
            headingStyle: 'atx',
            bulletListMarker: '-',
            codeBlockStyle: 'fenced',
            emDelimiter: '*',
            strongDelimiter: '**',
        });
        return uteTurndown;
    }

    function pastePlainLooksLikeMarkdown(plain) {
        if (!plain || plain.length < 2) return false;
        return /^#{1,6}\s/m.test(plain) ||
            /^[-*+]\s/m.test(plain) ||
            /^\d+\.\s/m.test(plain) ||
            /^>\s/m.test(plain) ||
            /^```/m.test(plain) ||
            /\[[^\]]+\]\([^)]+\)/.test(plain);
    }

    function pasteHtmlLooksStructured(html) {
        if (!html || html.length < 8) return false;
        var h = html.toLowerCase();
        if (/mso-|urn:schemas-microsoft-com:/i.test(html) || /class="?mso/i.test(html)) return true;
        return /<h[1-6][\s/>]/.test(h) ||
            /<p[\s/>]/.test(h) ||
            /<div[\s/>]/.test(h) ||
            /<blockquote[\s/>]/.test(h) ||
            /<ul[\s/>]/.test(h) ||
            /<ol[\s/>]/.test(h) ||
            /<li[\s/>]/.test(h) ||
            /<table[\s/>]/.test(h) ||
            /<br\s*\/?>/.test(h) ||
            /<strong[\s/>]/.test(h) ||
            /<b[\s/>]/.test(h) ||
            /<em[\s/>]/.test(h) ||
            /<i[\s/>]/.test(h) ||
            /<a\s+[^>]*href/.test(h) ||
            /<span[^>]*style=/.test(h);
    }

    function attachRichPaste(cm) {
        var input = cm.getInputField();
        if (!input || input._uteRichPaste) return;
        input._uteRichPaste = true;
        input.addEventListener('paste', function (e) {
            if (!e.clipboardData) return;
            var html = (e.clipboardData.getData('text/html') || '').trim();
            var plain = e.clipboardData.getData('text/plain') || '';
            if (!html) return;
            if (pastePlainLooksLikeMarkdown(plain) && !pasteHtmlLooksStructured(html)) return;
            if (!pasteHtmlLooksStructured(html)) return;
            var td = getTurndown();
            if (!td) return;
            try {
                var md = td.turndown(html).replace(/\n{3,}/g, '\n\n').trim();
                if (!md) return;
                e.preventDefault();
                e.stopPropagation();
                cm.replaceSelection(md + (md.endsWith('\n') ? '' : '\n'), 'around', 'paste');
            } catch (err) {
                console.warn('Rich paste conversion failed', err);
            }
        }, true);
    }

    /** Match ultimAItech admin PostEditorPage Quill toolbar (rows → one row + separators). */
    const UTE_MDE_TOOLBAR = [
        'heading-1', 'heading-2', 'heading-3',
        '|',
        'bold', 'italic',
        {
            name: 'underline',
            action: function (editor) {
                var cm = editor.codemirror;
                var sel = cm.getSelection();
                cm.replaceSelection('<u>' + sel + '</u>');
            },
            className: 'fa fa-underline',
            title: 'Underline',
        },
        'strikethrough',
        '|',
        {
            name: 'align-left',
            action: function (editor) {
                var cm = editor.codemirror;
                var sel = cm.getSelection();
                cm.replaceSelection('<p>' + sel + '</p>');
            },
            className: 'fa fa-align-left',
            title: 'Align left',
        },
        {
            name: 'align-center',
            action: function (editor) {
                var cm = editor.codemirror;
                var sel = cm.getSelection();
                cm.replaceSelection('<p style="text-align:center">' + sel + '</p>');
            },
            className: 'fa fa-align-center',
            title: 'Align center',
        },
        {
            name: 'align-right',
            action: function (editor) {
                var cm = editor.codemirror;
                var sel = cm.getSelection();
                cm.replaceSelection('<p style="text-align:right">' + sel + '</p>');
            },
            className: 'fa fa-align-right',
            title: 'Align right',
        },
        {
            name: 'align-justify',
            action: function (editor) {
                var cm = editor.codemirror;
                var sel = cm.getSelection();
                cm.replaceSelection('<p style="text-align:justify">' + sel + '</p>');
            },
            className: 'fa fa-align-justify',
            title: 'Justify',
        },
        '|',
        'ordered-list', 'unordered-list',
        '|',
        'quote', 'code',
        '|',
        'link', 'image',
        '|',
        'clean-block',
    ];

    function initBodyEditor(lang) {
        const ta = document.getElementById('body_' + lang);
        if (!ta || bodyEditors[lang]) return bodyEditors[lang];
        bodyEditors[lang] = new EasyMDE({
            element: ta,
            spellChecker: false,
            autosave: { enabled: false },
            placeholder: '# Kop\n\nTekst…',
            toolbar: UTE_MDE_TOOLBAR,
            previewClass: ['content-prose', 'blog-article-content'],
            minHeight: '320px',
            openLinksInNewWindow: true,
            imageAccept: 'image/jpeg,image/png,image/webp,image/gif',
            imageUploadFunction: function (file, onSuccess, onError) {
                (async () => {
                    try {
                        await refreshBlogCsrf();
                        const fd = new FormData();
                        fd.append('action', 'uploadBlogImage');
                        fd.append('csrfToken', blogCsrf);
                        fd.append('image', file);
                        const res = await fetch(apiUrl, { method: 'POST', credentials: 'include', body: fd });
                        const data = await res.json().catch(() => ({}));
                        if (data.success && data.url) {
                            onSuccess(data.url);
                        } else {
                            const msg = data.message || 'Upload mislukt.';
                            if (typeof onError === 'function') onError(msg);
                            else alert(msg);
                        }
                    } catch (e) {
                        const msg = (e && e.message) ? e.message : 'Upload mislukt.';
                        if (typeof onError === 'function') onError(msg);
                        else alert(msg);
                    }
                })();
            }
        });
        attachRichPaste(bodyEditors[lang].codemirror);
        return bodyEditors[lang];
    }

    function setActiveLangTab(lang) {
        activeLang = lang;
        document.querySelectorAll('.blog-portal-tab').forEach(t => {
            const on = t.dataset.lang === lang;
            t.classList.toggle('active', on);
            t.setAttribute('aria-selected', on ? 'true' : 'false');
        });
        document.querySelectorAll('.blog-portal-lang-panel').forEach(p => {
            p.style.display = p.dataset.lang === lang ? 'block' : 'none';
        });
        document.querySelectorAll('.blog-portal-seo-panel').forEach(p => {
            p.style.display = p.dataset.lang === lang ? 'block' : 'none';
        });
        const ed = bodyEditors[lang];
        if (ed && ed.codemirror) ed.codemirror.refresh();
    }

    document.querySelectorAll('.blog-portal-tab').forEach(tab => {
        tab.addEventListener('click', () => setActiveLangTab(tab.dataset.lang));
    });

    document.querySelectorAll('.blog-portal-copy-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const from = btn.dataset.from;
            const to = btn.dataset.to;
            const fields = ['title', 'excerpt', 'keyTakeaways', 'description', 'keywords', 'metaTitle', 'metaImage'];
            fields.forEach(field => {
                const srcEl = document.querySelector('.lang-field[data-lang="' + from + '"][data-field="' + field + '"]');
                const dstEl = document.querySelector('.lang-field[data-lang="' + to + '"][data-field="' + field + '"]');
                if (!dstEl && field !== 'body') return;
                if (field === 'body') {
                    if (bodyEditors[from] && bodyEditors[to]) {
                        bodyEditors[to].value(bodyEditors[from].value());
                    }
                    return;
                }
                const val = srcEl ? srcEl.value : '';
                dstEl.value = val;
            });
        });
    });

    document.querySelectorAll('[data-cheatsheet]').forEach(btn => {
        btn.addEventListener('click', () => {
            const lang = btn.dataset.lang;
            const panel = document.querySelector('.blog-portal-cheatsheet-panel[data-lang="' + lang + '"]');
            panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
        });
    });

    document.querySelectorAll('[data-takeaways-bold]').forEach(btn => {
        btn.addEventListener('click', () => {
            const lang = btn.dataset.lang;
            const ta = document.getElementById('keyTakeaways_' + lang);
            if (!ta) return;
            const start = ta.selectionStart;
            const end = ta.selectionEnd;
            const v = ta.value;
            const sel = v.slice(start, end);
            const replacement = sel ? ('**' + sel + '**') : '****';
            ta.value = v.slice(0, start) + replacement + v.slice(end);
            if (sel) {
                ta.setSelectionRange(start + replacement.length, start + replacement.length);
            } else {
                ta.setSelectionRange(start + 2, start + 2);
            }
            ta.focus();
            formDirty = true;
        });
    });

    ['nl', 'en', 'de'].forEach(lang => initBodyEditor(lang));

    document.getElementById('btnGenerateSlug').addEventListener('click', () => {
        const titleInp = document.querySelector('.lang-field[data-lang="' + activeLang + '"][data-field="title"]');
        const raw = titleInp ? titleInp.value : '';
        const s = slugify(raw);
        if (s) document.getElementById('slug').value = s;
    });

    document.getElementById('btnPreview').addEventListener('click', () => {
        const s = document.getElementById('slug').value.trim();
        if (!s) {
            alert('Sla het artikel eerst op met een slug, of vul een slug in.');
            return;
        }
        window.open('/blog/' + encodeURIComponent(s), '_blank', 'noopener');
    });

    document.getElementById('btnImportMd').addEventListener('click', () => document.getElementById('importMdInput').click());
    document.getElementById('importMdInput').addEventListener('change', () => {
        const f = document.getElementById('importMdInput').files[0];
        if (!f) return;
        const r = new FileReader();
        r.onload = () => {
            const ed = bodyEditors[activeLang];
            if (ed) ed.value(String(r.result || ''));
            formDirty = true;
        };
        r.readAsText(f);
        document.getElementById('importMdInput').value = '';
    });

    <?php if (!$isNew): ?>
    document.getElementById('btnDelete').addEventListener('click', async () => {
        if (!confirm('Dit artikel permanent verwijderen?')) return;
        try {
            await refreshBlogCsrf();
            const res = await fetch(apiUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': blogCsrf },
                credentials: 'include',
                body: JSON.stringify({ action: 'deleteArticle', slug: editSlug })
            });
            const data = await res.json();
            if (data.success) {
                window.location.href = '/blog-portal/dashboard';
            } else {
                alert(data.message || 'Verwijderen mislukt.');
            }
        } catch (e) {
            alert(e.message);
        }
    });
    <?php endif; ?>

    function watchDirty() {
        document.getElementById('articleForm').querySelectorAll('input, textarea, select').forEach(el => {
            el.addEventListener('change', () => { formDirty = true; });
            el.addEventListener('input', () => { formDirty = true; });
        });
        ['nl', 'en', 'de'].forEach(lang => {
            if (bodyEditors[lang] && bodyEditors[lang].codemirror) {
                bodyEditors[lang].codemirror.on('change', () => { formDirty = true; });
            }
        });
    }
    watchDirty();
    window.addEventListener('beforeunload', e => {
        if (formDirty) e.preventDefault();
    });

    function syncStatusSelect() {
        const pub = document.getElementById('postStatus').value === 'published';
        /* used on save */
    }
    document.getElementById('postStatus').addEventListener('change', syncStatusSelect);

    async function submitArticle(published) {
        await refreshBlogCsrf();
        const slug = document.getElementById('slug').value.trim().toLowerCase().replace(/[^a-z0-9\-]/g, '-').replace(/-+/g, '-').replace(/^-|-$/g, '');
        if (!slug) {
            alert('Vul een geldige slug in.');
            return;
        }
        const publishAtEl = document.getElementById('publishAt');
        const publishAt = publishAtEl.value ? publishAtEl.value : '';
        let date = publishAt ? publishAt.slice(0, 10) : new Date().toISOString().slice(0, 10);

        const translations = {};
        ['nl', 'en', 'de'].forEach(lang => {
            const title = document.querySelector('.lang-field[data-lang="' + lang + '"][data-field="title"]')?.value?.trim() || '';
            const description = document.querySelector('.lang-field[data-lang="' + lang + '"][data-field="description"]')?.value?.trim() || '';
            const keywords = document.querySelector('.lang-field[data-lang="' + lang + '"][data-field="keywords"]')?.value?.trim() || '';
            const excerpt = document.querySelector('.lang-field[data-lang="' + lang + '"][data-field="excerpt"]')?.value?.trim() || '';
            const keyTakeaways = document.querySelector('.lang-field[data-lang="' + lang + '"][data-field="keyTakeaways"]')?.value?.trim() || '';
            const metaTitle = document.querySelector('.lang-field[data-lang="' + lang + '"][data-field="metaTitle"]')?.value?.trim() || '';
            const metaImage = document.querySelector('.lang-field[data-lang="' + lang + '"][data-field="metaImage"]')?.value?.trim() || '';
            const body = bodyEditors[lang] ? bodyEditors[lang].value() : '';
            if (title || body) {
                translations[lang] = { title, description, keywords, excerpt, keyTakeaways, metaTitle, metaImage, body };
            }
        });
        if (Object.keys(translations).length === 0) {
            alert('Vul minimaal titel of inhoud in voor één taal.');
            return;
        }
        const payload = {
            action: 'saveArticle',
            article: {
                slug,
                originalSlug: isNew ? null : editSlug,
                translations,
                date,
                publishAt: publishAt || null,
                featuredImage: document.getElementById('featuredImage').value.trim() || null,
                published,
                noindex: document.getElementById('noindex').checked,
                canonicalUrl: document.getElementById('canonicalUrl').value.trim() || null,
                styleOverrides: (() => {
                    const sc = document.getElementById('styleLinkColor').value;
                    const sw = document.getElementById('styleMaxWidth').value.trim();
                    const o = {};
                    if (sc && sc !== '#1f6fac') o.articleLinkColor = sc;
                    if (sw) o.articleMaxWidth = sw + 'px';
                    return Object.keys(o).length ? o : null;
                })()
            }
        };
        const submitBtns = [document.getElementById('btnSaveDraft'), document.getElementById('btnPublishPost')];
        submitBtns.forEach(b => { if (b) b.disabled = true; });
        try {
            const res = await fetch(apiUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': blogCsrf },
                credentials: 'include',
                body: JSON.stringify(payload)
            });
            const data = await res.json();
            if (data.success) {
                formDirty = false;
                window.location.href = '/blog-portal/dashboard';
            } else {
                alert(data.message || 'Opslaan mislukt.');
            }
        } catch (err) {
            alert('Fout: ' + err.message);
        }
        submitBtns.forEach(b => { if (b) b.disabled = false; });
    }

    document.getElementById('btnSaveDraft').addEventListener('click', () => {
        document.getElementById('postStatus').value = 'draft';
        submitArticle(false);
    });
    document.getElementById('btnPublishPost').addEventListener('click', () => {
        document.getElementById('postStatus').value = 'published';
        submitArticle(true);
    });

    function toDatetimeLocal(dateStr, publishAtRaw) {
        if (publishAtRaw && publishAtRaw.length >= 16) {
            const normalized = publishAtRaw.replace(' ', 'T').slice(0, 16);
            return normalized;
        }
        const d = (dateStr || '').split('T')[0] || new Date().toISOString().slice(0, 10);
        return d + 'T12:00';
    }

    if (!isNew && editSlug) {
        (async () => {
            try {
                await refreshBlogCsrf();
                const res = await fetch(apiUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    credentials: 'include',
                    body: JSON.stringify({ action: 'getArticle', slug: editSlug })
                });
                const data = await res.json();
                if (!data.success) {
                    if (res.status === 401) {
                        window.location.href = '/blog-portal/login';
                        return;
                    }
                    alert(data.message || 'Kon artikel niet laden.');
                    return;
                }
                const a = data.article;
                document.getElementById('slug').value = a.slug || '';
                const date = (a.translations?.nl?.date || a.translations?.en?.date || a.translations?.de?.date || '').split('T')[0];
                document.getElementById('publishAt').value = toDatetimeLocal(date, a.publishAt || '');
                document.getElementById('postStatus').value = a.published !== false ? 'published' : 'draft';
                document.getElementById('noindex').checked = !!a.noindex;
                document.getElementById('canonicalUrl').value = a.canonicalUrl || '';

                const so = a.styleOverrides || {};
                if (so.articleLinkColor) {
                    document.getElementById('styleLinkColor').value = so.articleLinkColor;
                    document.getElementById('styleLinkColorText').value = so.articleLinkColor;
                }
                if (so.articleMaxWidth) {
                    const n = parseInt(String(so.articleMaxWidth).replace('px', ''), 10);
                    if (!isNaN(n)) document.getElementById('styleMaxWidth').value = n;
                }
                const feat = a.featuredImage || '';
                document.getElementById('featuredImage').value = feat;
                const prev = document.getElementById('featuredImagePreview');
                if (feat) {
                    prev.style.display = 'block';
                    prev.innerHTML = '<img src="' + feat + '" alt="">';
                } else {
                    prev.style.display = 'none';
                    prev.innerHTML = '';
                }
                ['nl', 'en', 'de'].forEach(lang => {
                    const t = a.translations?.[lang] || {};
                    const setVal = (field, v) => {
                        const el = document.querySelector('.lang-field[data-lang="' + lang + '"][data-field="' + field + '"]');
                        if (el) el.value = v || '';
                    };
                    setVal('title', t.title);
                    setVal('description', t.description);
                    setVal('keywords', t.keywords);
                    setVal('excerpt', t.excerpt);
                    setVal('keyTakeaways', t.keyTakeaways);
                    setVal('metaTitle', t.metaTitle);
                    setVal('metaImage', t.metaImage);
                    if (bodyEditors[lang]) bodyEditors[lang].value(t.body || '');
                });
                formDirty = false;
            } catch (err) {
                alert('Fout bij laden: ' + err.message);
            }
        })();
    } else {
        document.getElementById('publishAt').value = toDatetimeLocal(new Date().toISOString().slice(0, 10), '');
        refreshBlogCsrf();
    }

    document.querySelectorAll('[data-body-upload]').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelector('.blog-portal-upload-input[data-lang="' + btn.dataset.lang + '"]').click();
        });
    });

    document.getElementById('featuredImageUploadBtn').addEventListener('click', () => document.getElementById('featuredImageInput').click());
    document.getElementById('featuredImageInput').addEventListener('change', async () => {
        const file = document.getElementById('featuredImageInput').files[0];
        if (!file) return;
        await refreshBlogCsrf();
        const fd = new FormData();
        fd.append('action', 'uploadBlogImage');
        fd.append('csrfToken', blogCsrf);
        fd.append('image', file);
        try {
            const res = await fetch(apiUrl, { method: 'POST', credentials: 'include', body: fd });
            const data = await res.json();
            if (data.success) {
                document.getElementById('featuredImage').value = data.url;
                const prev = document.getElementById('featuredImagePreview');
                prev.style.display = 'block';
                prev.innerHTML = '<img src="' + data.url + '" alt="">';
                formDirty = true;
            } else alert(data.message || 'Upload mislukt.');
        } catch (err) { alert('Fout: ' + err.message); }
        document.getElementById('featuredImageInput').value = '';
    });

    document.getElementById('styleLinkColorText').addEventListener('input', () => {
        const v = document.getElementById('styleLinkColorText').value;
        if (/^#[0-9A-Fa-f]{6}$/.test(v)) document.getElementById('styleLinkColor').value = v;
    });
    document.getElementById('featuredImage').addEventListener('input', () => {
        const url = document.getElementById('featuredImage').value.trim();
        const prev = document.getElementById('featuredImagePreview');
        if (url) {
            prev.style.display = 'block';
            prev.innerHTML = '<img src="' + url + '" alt="" onerror="this.parentNode.style.display=\'none\'">';
        } else {
            prev.style.display = 'none';
            prev.innerHTML = '';
        }
    });

    function showImageModal(url, lang, defaultAlt) {
        const modal = document.getElementById('imageUploadModal');
        document.getElementById('imageUploadPreview').src = url;
        document.getElementById('imageUploadAlt').value = defaultAlt || '';
        modal.dataset.url = url;
        modal.dataset.lang = lang;
        modal.style.display = 'flex';
    }
    function hideImageModal() {
        document.getElementById('imageUploadModal').style.display = 'none';
    }
    document.getElementById('imageUploadModalClose').addEventListener('click', hideImageModal);
    document.getElementById('imageUploadModalOverlay').addEventListener('click', hideImageModal);
    document.getElementById('imageUploadInsert').addEventListener('click', () => {
        const modal = document.getElementById('imageUploadModal');
        const url = modal.dataset.url;
        const lang = modal.dataset.lang;
        const alt = document.getElementById('imageUploadAlt').value.trim() || 'afbeelding';
        const editor = bodyEditors[lang];
        if (editor) editor.codemirror.replaceSelection('![' + alt + '](' + url + ')');
        document.querySelector('.blog-portal-upload-input[data-lang="' + lang + '"]').value = '';
        hideImageModal();
    });

    document.querySelectorAll('.blog-portal-upload-input').forEach(input => {
        input.addEventListener('change', async () => {
            const file = input.files[0];
            if (!file) return;
            await refreshBlogCsrf();
            const lang = input.dataset.lang;
            const fd = new FormData();
            fd.append('action', 'uploadBlogImage');
            fd.append('csrfToken', blogCsrf);
            fd.append('image', file);
            try {
                const res = await fetch(apiUrl, { method: 'POST', credentials: 'include', body: fd });
                const data = await res.json();
                if (!data.success) {
                    alert(data.message || 'Upload mislukt.');
                    return;
                }
                const defaultAlt = file.name.replace(/\.[^.]+$/, '');
                showImageModal(data.url, lang, defaultAlt);
            } catch (err) {
                alert('Fout bij upload: ' + err.message);
            }
            input.value = '';
        });
    });
})();
</script>
</body>
</html>
