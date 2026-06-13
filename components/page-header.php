<?php
/**
 * PAGE HEADER Component - Hero section for content pages
 * 
 * Required variables:
 * - $headerTitle: Main title for the page
 * - $headerTitleI18n: i18n key for the title
 * - $headerDescription: Description text
 * - $headerDescriptionI18n: i18n key for the description
 * 
 * Optional variables:
 * - $headerClass: Additional CSS class for the header
 */
?>
<!-- Page Header -->
<header class="page-header<?php echo !empty($headerClass) ? ' ' . $headerClass : ''; ?>">
    <div class="container">
        <h1 data-i18n="<?php echo htmlspecialchars($headerTitleI18n); ?>"><?php echo htmlspecialchars($headerTitle); ?></h1>
        <p data-i18n="<?php echo htmlspecialchars($headerDescriptionI18n); ?>"><?php echo htmlspecialchars($headerDescription); ?></p>
    </div>
</header>

