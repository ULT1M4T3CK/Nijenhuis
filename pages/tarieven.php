<?php
/**
 * Tarieven & prijzen Page - Nijenhuis Botenverhuur
 * Unique pricing content (avoids duplicate homepage via nginx catch-all)
 */
require_once __DIR__ . '/../components/config.php';
require_once __DIR__ . '/../components/faq_price_helper.php';

$basePath = getBasePath();
$pageTitleFull = 'Tarieven & prijzen bootverhuur | Nijenhuis Botenverhuur';
$pageDescription = 'Actuele tarieven bootverhuur Giethoorn & Weerribben. Sloepen vanaf €175/dag, kano\'s vanaf €20/dag. Meerdaags korting, borg en prijslijst per boottype bij Nijenhuis Botenverhuur.';
$pageKeywords = 'tarieven bootverhuur, prijzen boot huren giethoorn, sloep huren kosten, fluisterboot prijs, bootverhuur weerribben tarieven';

$boats = faq_load_boats();
$faqPriceAnswerText = faq_get_price_answer_text($boats);
$faqDepositSnippet = faq_get_deposit_snippet($boats);
$depositBreakdown = faq_get_deposit_breakdown($boats);
$depositRange = faq_get_deposit_range($boats);
$priceListHtml = faq_render_price_list_html($boats, 'nl');

$headerTitle = 'Tarieven bootverhuur Giethoorn & Weerribben';
$headerTitleI18n = '';
$headerDescription = 'Overzicht van huurprijzen, borg en meerdaagse kortingen voor al onze boten';
$headerDescriptionI18n = '';

$breadcrumbs = [
    ['name' => 'Home', 'url' => '/'],
    ['name' => 'Tarieven', 'url' => '/tarieven'],
];
?>
<!DOCTYPE html>
<html lang="nl">
<?php include __DIR__ . '/../components/head.php'; ?>

<body data-page="tarieven">
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "FAQPage",
    "mainEntity": [
        {
            "@type": "Question",
            "name": "Wat kost het om een boot te huren bij Nijenhuis?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": <?php echo json_encode($faqPriceAnswerText, JSON_UNESCAPED_UNICODE); ?>
            }
        },
        {
            "@type": "Question",
            "name": "Hoeveel borg moet ik betalen?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": <?php echo json_encode('De borg is ' . $faqDepositSnippet . ' afhankelijk van het boottype. U ontvangt de borg terug bij onbeschadigde retour van het vaartuig.', JSON_UNESCAPED_UNICODE); ?>
            }
        },
        {
            "@type": "Question",
            "name": "Is er korting bij meerdaagse verhuur?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Ja, bij huur van meerdere dagen geldt een lagere dagprijs. Een week huren is voordeliger dan zeven losse dagen. Bekijk de prijslijst per boot voor actuele tarieven per huurperiode."
            }
        }
    ]
}
</script>
    <?php include __DIR__ . '/../components/topbar.php'; ?>
    <?php include __DIR__ . '/../components/nav.php'; ?>
    <?php include __DIR__ . '/../components/breadcrumb.php'; ?>
    <?php include __DIR__ . '/../components/page-header.php'; ?>

    <main>
        <section class="content-section">
            <div class="container content-prose">
                <p class="tarieven-intro">Op deze pagina vindt u de actuele <strong>tarieven voor bootverhuur</strong> bij Nijenhuis Botenverhuur in Wanneperveen, direct aan Nationaal Park Weerribben-Wieden. Of u nu een <strong>fluisterboot</strong>, electrosloep, zeilboot, kano of SUP wilt huren richting Giethoorn — hieronder staat per boottype de prijs per dag en meerdaagse tarieven.</p>

                <h2>Prijslijst per boottype</h2>
                <p>Klik op een boot voor direct reserveren. Alle prijzen zijn indicatief voor het vaarseizoen (1 april – 31 oktober).</p>
                <?php echo $priceListHtml; ?>

                <h2>Meerdaagse verhuur en korting</h2>
                <p>Bij huur van <strong>2 tot 7 dagen</strong> geldt een lagere dagprijs dan bij één dag. Hoe langer u huurt, hoe voordeliger de prijs per dag. Dit geldt voor sloepen, electroboten, zeilboten, kano's, kajaks en SUP-boards. Voor exacte meerdaagse tarieven per boot, open de detailpagina of reserveer via onze <a href="/">online boekingspagina</a>.</p>
                <p>Boten kunnen ook voor langere periodes worden gehuurd. Neem contact op via <a href="tel:0522281528">0522 281 528</a> of het <a href="/contact">contactformulier</a> voor maatwerk.</p>

                <h2>Borg</h2>
                <?php if (!empty($depositBreakdown)): ?>
                <ul>
                    <?php foreach ($depositBreakdown as $item): ?>
                    <li><strong><?php echo htmlspecialchars(ucfirst($item['label'])); ?>:</strong> €<?php echo (int) $item['amount']; ?> borg</li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>
                <p>De borg (<?php echo htmlspecialchars($faqDepositSnippet); ?>) betaalt u ter plaatse en ontvangt u terug bij onbeschadigde retour. Kano's, kajaks, SUP-boards en enkele kleinere boten hebben geen borg.</p>

                <h2>Wat is inbegrepen?</h2>
                <ul>
                    <li>Vaartuig met volgeladen accu (elektrische boten) of complete uitrusting (zeil/kano)</li>
                    <li>Reddingsvesten voor alle passagiers</li>
                    <li>Vaarkaart van het Weerribbengebied</li>
                    <li>Korte instructie en vaarregels vóór vertrek</li>
                </ul>

                <h2>Direct boeken</h2>
                <p>Bekijk het volledige aanbod met foto's en specificaties op onze <a href="/botenverhuur">botenverhuur pagina</a>, of start direct met reserveren op de <a href="/#booking">homepage</a>. Vragen over prijzen? Zie ook onze <a href="/veelgestelde-vragen">veelgestelde vragen</a>.</p>

                <p style="margin-top: 2rem;">
                    <a href="/botenverhuur" class="btn">Bekijk alle boten</a>
                    <a href="/#booking" class="btn btn-outline" style="margin-left: 0.75rem;">Direct boeken</a>
                </p>
            </div>
        </section>
    </main>

    <?php include __DIR__ . '/../components/footer.php'; ?>
</body>
</html>
