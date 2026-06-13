<?php
/**
 * BREADCRUMB Component - Generates breadcrumb navigation with Schema.org markup
 * 
 * Required variables:
 * - $breadcrumbs: Array of breadcrumb items, each with 'name' and 'url' keys
 *                 Example: [['name' => 'Home', 'url' => '/'], ['name' => 'Botenverhuur', 'url' => '/botenverhuur']]
 * 
 * Optional variables:
 * - $showBreadcrumb: Set to false to hide the visual breadcrumb (schema still outputs)
 */

// Don't output if no breadcrumbs defined
if (empty($breadcrumbs)) {
    return;
}

$baseUrl = defined('SITE_URL') ? SITE_URL : 'https://nijenhuis-botenverhuur.com';
$showBreadcrumb = $showBreadcrumb ?? true;

// Build Schema.org BreadcrumbList JSON-LD
$schemaItems = [];
foreach ($breadcrumbs as $index => $crumb) {
    $schemaItems[] = [
        '@type' => 'ListItem',
        'position' => $index + 1,
        'name' => $crumb['name'],
        'item' => $baseUrl . $crumb['url']
    ];
}

$breadcrumbSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => $schemaItems
];
?>
<!-- BreadcrumbList Schema.org Structured Data -->
<script type="application/ld+json">
<?php echo json_encode($breadcrumbSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT); ?>
</script>

<?php if ($showBreadcrumb): ?>
<!-- Visual Breadcrumb Navigation - Seamless Design -->
<nav class="breadcrumb-nav" aria-label="Breadcrumb">
    <div class="container">
        <ol class="breadcrumb-list" itemscope itemtype="https://schema.org/BreadcrumbList">
            <?php foreach ($breadcrumbs as $index => $crumb): ?>
            <li class="breadcrumb-item<?php echo ($index === count($breadcrumbs) - 1) ? ' active' : ''; ?>" 
                itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                <?php if ($index === count($breadcrumbs) - 1): ?>
                    <span itemprop="name"><?php echo htmlspecialchars($crumb['name']); ?></span>
                <?php else: ?>
                    <a href="<?php echo htmlspecialchars($crumb['url']); ?>" itemprop="item">
                        <span itemprop="name"><?php echo htmlspecialchars($crumb['name']); ?></span>
                    </a>
                    <span class="breadcrumb-separator" aria-hidden="true">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 18l6-6-6-6"/>
                        </svg>
                    </span>
                <?php endif; ?>
                <meta itemprop="position" content="<?php echo $index + 1; ?>">
            </li>
            <?php endforeach; ?>
        </ol>
    </div>
</nav>

<style>
/* Seamless Breadcrumb - Subtle, integrated design */
.breadcrumb-nav {
    background: linear-gradient(135deg, var(--secondary-color, #003366) 0%, rgba(0, 51, 102, 0.95) 100%);
    padding: 0.6rem 0;
    position: relative;
    z-index: 10;
}

.breadcrumb-nav::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1) 20%, rgba(255,255,255,0.1) 80%, transparent);
}

.breadcrumb-list {
    list-style: none;
    margin: 0;
    padding: 0;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 0.15rem;
    font-size: 0.85rem;
}

.breadcrumb-item {
    display: flex;
    align-items: center;
    gap: 0.15rem;
}

.breadcrumb-item a {
    color: rgba(255, 255, 255, 0.75);
    text-decoration: none;
    transition: color 0.2s ease;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
}

.breadcrumb-item a:hover {
    color: #ffffff;
    background: rgba(255, 255, 255, 0.1);
}

.breadcrumb-item.active span {
    color: rgba(255, 255, 255, 0.95);
    font-weight: 500;
    padding: 0.25rem 0.5rem;
}

.breadcrumb-separator {
    color: rgba(255, 255, 255, 0.4);
    display: flex;
    align-items: center;
}

.breadcrumb-separator svg {
    width: 14px;
    height: 14px;
}

/* Home icon for first item */
.breadcrumb-item:first-child a::before {
    content: '';
    display: inline-block;
    width: 14px;
    height: 14px;
    margin-right: 4px;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='rgba(255,255,255,0.75)' stroke-width='2'%3E%3Cpath d='M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z'/%3E%3Cpolyline points='9 22 9 12 15 12 15 22'/%3E%3C/svg%3E");
    background-size: contain;
    vertical-align: -2px;
}

.breadcrumb-item:first-child a:hover::before {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='white' stroke-width='2'%3E%3Cpath d='M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z'/%3E%3Cpolyline points='9 22 9 12 15 12 15 22'/%3E%3C/svg%3E");
}

@media (max-width: 768px) {
    .breadcrumb-nav {
        padding: 0.5rem 0;
    }
    
    .breadcrumb-list {
        font-size: 0.8rem;
    }
    
    .breadcrumb-item a {
        padding: 0.2rem 0.35rem;
    }
    
    .breadcrumb-separator svg {
        width: 12px;
        height: 12px;
    }
}
</style>
<?php endif; ?>
