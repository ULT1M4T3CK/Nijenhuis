<?php
/**
 * ADMIN HEAD Component - Common <head> section for admin pages
 * 
 * Required variables:
 * - $adminPageTitle: Page title (will be appended to "Admin - Nijenhuis")
 * 
 * Optional variables:
 * - $basePath: Base path for assets (defaults to '..')
 */

$basePath = $basePath ?? '..';
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($adminPageTitle); ?> - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo assetPath('frontend/css/styles.css'); ?>">
    <link rel="stylesheet" href="admin-consolidated.css">
    <link rel="icon" type="image/svg+xml" href="<?php echo assetPath('frontend/Images/logo-white.svg'); ?>">
    <!-- Boat Data Service (Single Source of Truth) -->
    <script src="<?php echo assetPath('js/boat-data-service.js'); ?>"></script>
</head>

