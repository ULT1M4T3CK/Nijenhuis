<?php
/**
 * Jachthaven Page - Nijenhuis Botenverhuur
 */
require_once __DIR__ . '/../components/config.php';
$basePath = '..';
$pageTitle = 'Jachthaven';
$pageDescription = 'Professionele Jachthaven diensten in het natuurgebied Weerribben. Veilige ligplaatsen en onderhoud.';
$headerTitle = 'Jachthaven';
$headerTitleI18n = 'jachthaven_title';
$headerDescription = 'Professionele jachthaven diensten voor booteigenaren in het natuurgebied Weerribben';
$headerDescriptionI18n = 'jachthaven_description';
?>
<!DOCTYPE html>
<html lang="nl">
<?php include __DIR__ . '/../components/head.php'; ?>
<body data-page="jachthaven">
    <?php include __DIR__ . '/../components/topbar.php'; ?>
    <?php include __DIR__ . '/../components/nav.php'; ?>
    <?php include __DIR__ . '/../components/page-header.php'; ?>

    <main>
        <section class="content-section">
            <div class="container">
                <div class="section-title">
                    <h2 data-i18n="jachthaven_h2">Ligplaatsen</h2>
                </div>

                <div class="no-boats-message">
                    <div class="message-content">
                        <h3 data-i18n="jachthaven_ligplaatsen_title">Jachthaven ligplaatsen</h3>
                        <p data-i18n="jachthaven_ligplaatsen_description">Ligplaatsen in de jachthaven zijn alleen voor permanente jaarlijkse ligplaatsen. Ze zijn uitgerust met water, elektriciteit, douche en toilet. De maximale diepgang is +/- 1,00 m. De jachthaven is direct gelegen aan het Belterwijde meer, van waaruit u in alle richtingen kunt varen.</p>
                        <p data-i18n="jachthaven_ligplaatsen_cta">Neem gerust <a href="/contact">contact</a> op voor meer informatie over beschikbaarheid en voorwaarden.</p>
                        <a href="tel:+31522281528" class="btn-call" data-i18n="btn_call">Bel ons</a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php include __DIR__ . '/../components/footer.php'; ?>
</body>
</html>

