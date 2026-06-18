<?php
/**
 * Google Tag Manager (head) — included as early as possible in <head>.
 * Falls back to direct GA4 gtag.js when GTM is not configured.
 */
if (defined('GTM_CONTAINER_ID') && GTM_CONTAINER_ID !== '') {
    $gtmId = htmlspecialchars(GTM_CONTAINER_ID, ENT_QUOTES, 'UTF-8');
    ?>
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','<?php echo $gtmId; ?>');</script>
<!-- End Google Tag Manager -->
    <?php
    return;
}

if (defined('GA4_MEASUREMENT_ID') && GA4_MEASUREMENT_ID !== '') {
    $gaId = htmlspecialchars(GA4_MEASUREMENT_ID, ENT_QUOTES, 'UTF-8');
    ?>
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo $gaId; ?>"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', '<?php echo $gaId; ?>');
</script>
    <?php
}
