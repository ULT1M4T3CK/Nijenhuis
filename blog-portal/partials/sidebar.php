<?php
/**
 * Left sidebar for blog portal (dark admin shell).
 *
 * @var string $blogPortalNavActive dashboard | style | editor
 */
$blogPortalNavActive = $blogPortalNavActive ?? 'dashboard';
$blogPortalUser = getenv('BLOG_USERNAME') ?: ($_ENV['BLOG_USERNAME'] ?? '');
if ($blogPortalUser === '') {
    $blogPortalUser = 'Blog admin';
}
$blogPortalLogoutCsrf = $_SESSION['blog_csrf_token'] ?? '';
?>
<aside class="blog-portal-sidebar" aria-label="Hoofdnavigatie">
    <div class="blog-portal-sidebar-brand">
        <a href="/blog-portal/dashboard" class="blog-portal-sidebar-logo">
            <img src="<?php echo assetPath('frontend/Images/logo-white.svg'); ?>" alt="" width="32" height="32">
            <span class="blog-portal-sidebar-title"><?php echo htmlspecialchars(SITE_NAME); ?></span>
        </a>
        <span class="blog-portal-sidebar-sub">Blogbeheer</span>
    </div>
    <nav class="blog-portal-sidebar-nav">
        <a href="/blog-portal/dashboard" class="blog-portal-side-link<?php echo $blogPortalNavActive === 'dashboard' ? ' is-active' : ''; ?>">
            <span class="blog-portal-side-icon" aria-hidden="true">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><path d="M9 22V12h6v10"/></svg>
            </span>
            Dashboard
        </a>
        <a href="/blog-portal/article/new" class="blog-portal-side-link<?php echo !empty($blogPortalHighlightNew) ? ' is-active' : ''; ?>">
            <span class="blog-portal-side-icon" aria-hidden="true">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
            </span>
            Nieuw artikel
        </a>
        <a href="/blog-portal/style" class="blog-portal-side-link nav-indent<?php echo ($blogPortalNavActive ?? '') === 'style' ? ' is-active' : ''; ?>">
            <span class="blog-portal-side-icon" aria-hidden="true">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/></svg>
            </span>
            Stijl &amp; typografie
        </a>
    </nav>
    <div class="blog-portal-sidebar-footer">
        <p class="blog-portal-user-email"><?php echo htmlspecialchars($blogPortalUser); ?></p>
        <p class="blog-portal-user-role">Website blogbeheer</p>
        <a href="/blog-portal/logout?csrf=<?php echo urlencode($blogPortalLogoutCsrf); ?>" class="blog-portal-sidebar-logout">Uitloggen</a>
    </div>
</aside>
