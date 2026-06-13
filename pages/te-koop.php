<?php
/**
 * Te Koop Page - Nijenhuis Botenverhuur
 * Displays boats and caravans for sale from the admin-managed for-sale.json
 */
require_once __DIR__ . '/../components/config.php';
$basePath = '..';
$pageTitle = 'Chalets & stacaravans te koop';
$pageDescription = 'Bekijk chalets en stacaravans te koop bij Camping Nijenhuis in Wanneperveen. Direct aan het water in de Weerribben. Actueel aanbod met prijzen.';
$pageKeywords = 'chalet te koop weerribben, stacaravan te koop wanneperveen, camping staplaats kopen giethoorn';
$headerTitle = 'Te koop';
$headerTitleI18n = 'te_koop_h1';
$headerDescription = 'Bekijk hier onze nieuwste aanbiedingen.';
$headerDescriptionI18n = 'te_koop_p1';

// Breadcrumbs for this page
$breadcrumbs = [
    ['name' => 'Home', 'url' => '/'],
    ['name' => 'Te koop', 'url' => '/te-koop']
];
// Items loaded client-side via ForSaleDataService (keeps HTML small, avoids base64 in document)
?>
<!DOCTYPE html>
<html lang="nl">
<?php include __DIR__ . '/../components/head.php'; ?>
<body data-page="te-koop">
    <?php include __DIR__ . '/../components/topbar.php'; ?>
    <?php include __DIR__ . '/../components/nav.php'; ?>
    <?php include __DIR__ . '/../components/breadcrumb.php'; ?>
    <?php include __DIR__ . '/../components/page-header.php'; ?>

    <main>
        <section class="content-section bg-secondary">
            <div class="container">
                <div class="section-title">
                    <h2 data-i18n="te_koop_intro_h2">Chalets en stacaravans in de Weerribben</h2>
                </div>
                <div class="content-prose">
                    <p data-i18n="te_koop_intro_p1">Bij Camping Nijenhuis komen regelmatig chalets en stacaravans vrij die te koop staan op een vaste staanplaats in Nationaal Park Weerribben-Wieden. Een eigen chalet of stacaravan op onze camping betekent een vaste plek aan het water, direct toegang tot de vaarroutes naar Giethoorn en de Weerribben, en een rustige omgeving waar je het hele jaar kunt genieten van de natuur.</p>
                    <p data-i18n="te_koop_intro_p2">Kopers krijgen een seizoensplaats met alle voorzieningen: water, elektriciteit, riool en een eigen aanlegplaats. De caravans en chalets mogen het hele jaar op de plaats blijven staan. Door de kleinschaligheid van de camping is het aanbod beperkt – nieuw aanbod wordt op deze pagina geplaatst zodra het beschikbaar is.</p>
                </div>
            </div>
        </section>

        <section class="content-section">
            <div class="container">
                <div class="section-title">
                    <h2 data-i18n="te_koop_h2">Chalets & Stacaravans</h2>
                </div>

                <!-- Grid populated by JS (avoids embedding item data/base64 images in HTML) -->
                <div class="no-boats-message" style="display: none;">
                    <div class="message-content">
                        <h3 data-i18n="te_koop_h3">Geen aanbod beschikbaar</h3>
                        <p data-i18n="te_koop_p2">Op dit moment hebben wij geen chalets of stacaravans te koop. Zodra er nieuw aanbod is, vind je dat hier terug.</p>
                        <p data-i18n="te_koop_p3">Heb je interesse in een chalet of stacaravan in de toekomst? Neem gerust contact met ons op voor meer informatie of om op de wachtlijst te komen.</p>
                        <a href="tel:+31522281528" class="btn-call" data-i18n="btn_call">Bel ons</a>
                    </div>
                </div>
                <div class="for-sale-grid"><!-- Items rendered by te-koop.js --></div>

                <div class="section-title" style="margin-top: 3rem;">
                    <h2 data-i18n="te_koop_why_h2">Waarom bij Nijenhuis kopen?</h2>
                </div>
                <div class="content-prose">
                    <p data-i18n="te_koop_why_p1">Camping Nijenhuis is een familiebedrijf met meer dan 50 jaar ervaring in de Weerribben. Onze camping biedt een unieke locatie direct aan het water, met eigen aanlegplaatsen en alle moderne voorzieningen. Chalets en stacaravans die hier te koop staan, hebben een bewezen staanplaats in een gewild natuurgebied. Geïnteresseerd? Neem contact op voor beschikbaarheid, prijzen en de mogelijkheid om op de wachtlijst te komen voor toekomstig aanbod.</p>
                </div>
            </div>
        </section>
    </main>

    <!-- Item Details Modal -->
    <div id="itemDetailsModal" class="item-modal" style="display: none;">
        <div class="item-modal-content">
            <button class="item-modal-close" onclick="closeItemDetails()">&times;</button>
            <div id="itemDetailsContent">
                <!-- Content will be populated by JavaScript -->
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/../components/footer.php'; ?>

    <!-- Te Koop page - data via ForSaleDataService, rendering in te-koop.js (no inline JSON) -->
    <script src="<?php echo assetPath('js/for-sale-data-service.js'); ?>"></script>
    <script>window.forSaleConfig={phone:'<?php echo addslashes(SITE_PHONE); ?>',email:''};</script>
    <script src="<?php echo assetPath(ltrim(PATH_JS_TE_KOOP, '/')); ?>"></script>
</body>
</html>
