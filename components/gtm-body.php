<?php
/**
 * Google Tag Manager — noscript fallback (immediately after opening <body>).
 */
if (defined('GTM_BODY_INCLUDED') || !defined('GTM_CONTAINER_ID') || GTM_CONTAINER_ID === '') {
    return;
}
define('GTM_BODY_INCLUDED', true);
$gtmId = htmlspecialchars(GTM_CONTAINER_ID, ENT_QUOTES, 'UTF-8');
?>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?php echo $gtmId; ?>"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
