<?php
/**
 * NAVIGATION Component - Main navigation bar
 */

$basePath = $basePath ?? getBasePath();
?>
<!-- Navigation -->
<nav class="main-nav">
    <div class="container">
        <div class="nav-container">
            <a href="/" class="logo-link" aria-label="<?php echo SITE_NAME; ?> - Home">
                <img src="<?php echo assetPath(ltrim(PATH_LOGO, '/')); ?>" alt="<?php echo SITE_NAME; ?> - Home" class="logo">
            </a>
            
            <ul class="nav-menu">
                <?php foreach ($NAV_ITEMS as $item): ?>
                <li><a href="<?php echo $item['href']; ?>" data-i18n="<?php echo $item['i18n']; ?>"><?php echo $item['label']; ?></a></li>
                <?php endforeach; ?>
                <li class="nav-dropdown-wrapper">
                    <button type="button" class="nav-dropdown-trigger" id="navMoreTrigger" aria-haspopup="true" aria-expanded="false" aria-controls="navMoreDropdown" data-i18n="nav_more">Meer</button>
                    <ul class="nav-dropdown" id="navMoreDropdown" role="menu">
                        <?php foreach ($MORE_NAV_ITEMS as $item): ?>
                        <li role="none"><a href="<?php echo $item['href']; ?>" role="menuitem" data-i18n="<?php echo $item['i18n']; ?>"><?php echo $item['label']; ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </li>
                <li><a href="/contact" data-i18n="nav_contact">Contact</a></li>
                <li class="nav-cart-item">
                    <a href="#" onclick="toggleCartSidebar(); return false;" class="cart-icon" aria-label="Winkelwagen">
                        🛒
                        <span class="cart-count" id="navCartCount" style="display: none;">0</span>
                    </a>
                </li>
            </ul>
            
            <button class="mobile-menu-toggle" id="mobileMenuToggle" aria-label="Toggle menu" aria-expanded="false" aria-controls="navMenu">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z"/>
                </svg>
            </button>
        </div>
    </div>
</nav>

