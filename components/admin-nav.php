<?php
/**
 * ADMIN NAVIGATION Component
 * 
 * Optional variables:
 * - $currentAdminPage: Current page for active highlighting ('dashboard', 'boats', 'bookings', 'history')
 */

$basePath = $basePath ?? '..';
$currentAdminPage = $currentAdminPage ?? '';
?>
<!-- Admin Navigation -->
<nav class="admin-nav">
    <div class="admin-nav-container">
        <img src="<?php echo assetPath('frontend/Images/logo-white.svg'); ?>" alt="<?php echo SITE_NAME; ?>" class="admin-nav-logo">
        <div class="admin-nav-links">
            <a href="admin-static.php" class="admin-nav-link<?php echo $currentAdminPage === 'dashboard' ? ' active' : ''; ?>">Dashboard</a>
            <a href="boat-management.php" class="admin-nav-link<?php echo $currentAdminPage === 'boats' ? ' active' : ''; ?>">Bootbeheer</a>
            <a href="booking-management.php" class="admin-nav-link<?php echo $currentAdminPage === 'bookings' ? ' active' : ''; ?>">Reserveringsbeheer</a>
            <a href="booking-history.php" class="admin-nav-link<?php echo $currentAdminPage === 'history' ? ' active' : ''; ?>">Boekingsgeschiedenis</a>
            <a href="for-sale-management.php" class="admin-nav-link<?php echo $currentAdminPage === 'forsale' ? ' active' : ''; ?>">Te koop</a>
        </div>
        <button class="admin-nav-logout" onclick="logout()">Uitloggen</button>
    </div>
</nav>

