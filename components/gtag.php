<?php
/**
 * Google tag (gtag.js) — included when GA4_MEASUREMENT_ID is configured.
 */
if (defined('GA4_MEASUREMENT_ID') && GA4_MEASUREMENT_ID !== ''): ?>
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo htmlspecialchars(GA4_MEASUREMENT_ID); ?>"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', '<?php echo htmlspecialchars(GA4_MEASUREMENT_ID); ?>');
</script>
<?php endif; ?>
