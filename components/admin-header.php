<?php
/**
 * ADMIN HEADER Component - Admin page header section
 * 
 * Required variables:
 * - $adminTitle: Main title
 * - $adminSubtitle: Subtitle
 */
?>
<!-- Admin Header Section -->
<div class="admin-header">
    <div class="admin-container">
        <h1 class="admin-title"><?php echo htmlspecialchars($adminTitle); ?></h1>
        <p class="admin-subtitle"><?php echo htmlspecialchars($adminSubtitle); ?></p>
    </div>
</div>

