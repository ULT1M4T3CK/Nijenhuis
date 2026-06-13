<?php
require_once __DIR__ . '/portal-headers.php';
require_once __DIR__ . '/blog-auth.php';
require_once __DIR__ . '/../components/config.php';
$blogPortalNavActive = 'dashboard';
$blogPortalHighlightNew = false;
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Dashboard - <?php echo SITE_NAME; ?></title>
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
                <h1 class="blog-portal-page-title">Dashboard</h1>
                <p class="blog-portal-page-desc">Beheer je blogartikelen. Zoek, sorteer en open het bewerkscherm.</p>
            </div>
            <a href="/blog-portal/article/new" class="blog-portal-btn-primary">Nieuw artikel</a>
        </header>

        <div class="blog-portal-main">
            <div class="blog-portal-content">
                <div class="blog-portal-dashboard-toolbar">
                    <input type="search" id="dashboardSearch" placeholder="Zoeken op titel of slug..." class="blog-portal-search">
                    <label class="blog-portal-dashboard-sort">
                        <span class="blog-portal-sort-label">Sorteren</span>
                        <select id="dashboardSort" class="blog-portal-select blog-portal-select-inline">
                            <option value="date-desc">Datum (nieuwste eerst)</option>
                            <option value="date-asc">Datum (oudste eerst)</option>
                            <option value="title-asc">Titel A-Z</option>
                            <option value="title-desc">Titel Z-A</option>
                        </select>
                    </label>
                </div>
                <div id="articlesList" class="blog-portal-articles">
                    <p class="blog-portal-loading">Laden...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const apiUrl = window.location.origin + '/blog-portal/api.php';

    let allArticles = [];
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

    function renderArticles(articles) {
        const el = document.getElementById('articlesList');
        const search = (document.getElementById('dashboardSearch').value || '').toLowerCase();
        const sort = document.getElementById('dashboardSort').value;
        let filtered = articles.filter(a => {
            if (!search) return true;
            const slug = (a.slug || '').toLowerCase();
            const t = a.translations?.nl || a.translations?.en || a.translations?.de || {};
            const title = (t.title || '').toLowerCase();
            return slug.includes(search) || title.includes(search);
        });
        filtered = filtered.sort((a, b) => {
            const tA = a.translations?.nl || a.translations?.en || a.translations?.de || {};
            const tB = b.translations?.nl || b.translations?.en || b.translations?.de || {};
            const dateA = tA.date || '';
            const dateB = tB.date || '';
            const titleA = (tA.title || a.slug || '').toLowerCase();
            const titleB = (tB.title || b.slug || '').toLowerCase();
            if (sort === 'date-desc') return dateB.localeCompare(dateA);
            if (sort === 'date-asc') return dateA.localeCompare(dateB);
            if (sort === 'title-asc') return titleA.localeCompare(titleB);
            if (sort === 'title-desc') return titleB.localeCompare(titleA);
            return 0;
        });
        if (filtered.length === 0) {
            el.innerHTML = search ? '<p class="blog-portal-empty">Geen artikelen gevonden.</p>' : '<p class="blog-portal-empty">Nog geen artikelen. <a href="/blog-portal/article/new">Maak je eerste artikel</a>.</p>';
            return;
        }
        let html = '<div class="blog-portal-table-wrap"><table class="blog-portal-table"><thead><tr><th>Slug</th><th>Titel (NL)</th><th>Datum</th><th>Status</th><th>Talen</th><th>Acties</th></tr></thead><tbody>';
        filtered.forEach(a => {
                const slug = a.slug || '';
                const t = a.translations?.nl || a.translations?.en || a.translations?.de || {};
                const title = t.title || slug;
                const date = t.date || '-';
                const published = a.published !== false;
                const status = published ? '<span class="blog-portal-status-pub">Gepubliceerd</span>' : '<span class="blog-portal-status-draft">Concept</span>';
                const langs = [];
                if (a.translations?.nl) langs.push('NL');
                if (a.translations?.en) langs.push('EN');
                if (a.translations?.de) langs.push('DE');
                html += '<tr><td><code>' + escapeHtml(slug) + '</code></td><td>' + escapeHtml(title) + '</td><td>' + escapeHtml(date) + '</td><td>' + status + '</td><td>' + langs.join(', ') + '</td><td class="blog-portal-actions"><a href="/blog/' + slug + '" target="_blank" rel="noopener" class="blog-portal-btn-action blog-portal-btn-view">Bekijken</a><a href="/blog-portal/article/edit/' + slug + '" class="blog-portal-btn-action blog-portal-btn-edit">Bewerken</a><button type="button" class="blog-portal-btn-action blog-portal-btn-delete" data-slug="' + escapeHtml(slug) + '">Verwijderen</button></td></tr>';
            });
            html += '</tbody></table></div>';
            el.innerHTML = html;
            el.querySelectorAll('.blog-portal-btn-delete').forEach(btn => {
                btn.addEventListener('click', () => deleteArticle(btn.dataset.slug));
            });
    }

    async function loadArticles() {
        const el = document.getElementById('articlesList');
        try {
            const res = await fetch(apiUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                credentials: 'include',
                body: JSON.stringify({ action: 'listArticles' })
            });
            const data = await res.json();
            if (!data.success) {
                if (res.status === 401) {
                    window.location.href = '/blog-portal/login';
                    return;
                }
                el.innerHTML = '<p class="blog-portal-error">' + (data.message || 'Kon artikelen niet laden.') + '</p>';
                return;
            }
            allArticles = data.articles || [];
            document.getElementById('dashboardSearch').addEventListener('input', () => renderArticles(allArticles));
            document.getElementById('dashboardSort').addEventListener('change', () => renderArticles(allArticles));
            renderArticles(allArticles);
        } catch (err) {
            el.innerHTML = '<p class="blog-portal-error">Fout bij laden: ' + escapeHtml(err.message) + '</p>';
        }
    }

    function escapeHtml(s) {
        const d = document.createElement('div');
        d.textContent = s;
        return d.innerHTML;
    }

    async function deleteArticle(slug) {
        if (!confirm('Artikel "' + slug + '" definitief verwijderen?')) return;
        try {
            const res = await fetch(apiUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': blogCsrf },
                credentials: 'include',
                body: JSON.stringify({ action: 'deleteArticle', slug: slug })
            });
            const data = await res.json();
            if (data.success) {
                loadArticles();
            } else {
                alert(data.message || 'Verwijderen mislukt.');
            }
        } catch (err) {
            alert('Fout: ' + err.message);
        }
    }

    (async () => {
        await refreshBlogCsrf();
        await loadArticles();
    })();
</script>
</body>
</html>
