<?php
/**
 * Veelgestelde Vragen (FAQ) Page - Nijenhuis Botenverhuur
 * Optimized for AI visibility with structured FAQ schema
 * Prices and deposits are loaded from boats.json (single source of truth)
 */
require_once __DIR__ . '/../components/config.php';
require_once __DIR__ . '/../components/faq_price_helper.php';
$basePath = getBasePath();
$pageTitle = 'Veelgestelde vragen';

$faqBoats = faq_load_boats();
$faqPriceAnswerText = faq_get_price_answer_text($faqBoats);
$faqDepositSnippet = faq_get_deposit_snippet($faqBoats);
$faqClientData = faq_get_client_data($faqBoats);
$pageDescription = 'Antwoorden op veelgestelde vragen over boot huren bij Nijenhuis. Prijzen, vaarbewijs, openingstijden, reserveren en botenverhuur in de Weerribben.';
$pageKeywords = 'veelgestelde vragen botenverhuur, boot huren giethoorn kosten, vaarbewijs nodig, weerribben vaarroutes';
$headerTitle = 'Veelgestelde vragen';
$headerTitleI18n = 'faq_header_h1';
$headerDescription = 'Alles wat je moet weten over boot huren bij Nijenhuis';
$headerDescriptionI18n = 'faq_header_p';

// Breadcrumbs for this page
$breadcrumbs = [
    ['name' => 'Home', 'url' => '/'],
    ['name' => 'Veelgestelde vragen', 'url' => '/veelgestelde-vragen']
];
$additionalStyles = ['/frontend/css/pages/faq.css'];
$additionalScripts = ['/frontend/src/js/pages/faq-prices.js'];
?>
<!DOCTYPE html>
<html lang="nl">
<?php include __DIR__ . '/../components/head.php'; ?>

<body data-page="faq">
    <?php include __DIR__ . '/../components/gtm-body.php'; ?>
<!-- FAQ Structured Data for AI and Search Engines -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "FAQPage",
    "speakable": {
        "@type": "SpeakableSpecification",
        "cssSelector": [".faq-intro-block p", ".faq-answer", ".faq-contact-cta p"]
    },
    "mainEntity": [
        {
            "@type": "Question",
            "name": "Wat kost het om een boot te huren bij Nijenhuis?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "<?php echo htmlspecialchars($faqPriceAnswerText); ?>"
            }
        },
        {
            "@type": "Question",
            "name": "Heb ik een vaarbewijs nodig om een boot te huren?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Nee, voor alle boten bij Nijenhuis Botenverhuur is geen vaarbewijs vereist. Onze elektrische sloepen en boten varen langzaam (maximaal 6 km/u) en zijn eenvoudig te bedienen. Voor vertrek krijg je een korte instructie over de bediening en de vaarregels in het Weerribbengebied."
            }
        },
        {
            "@type": "Question",
            "name": "Wanneer is Nijenhuis Botenverhuur open?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Het vaarseizoen loopt van 1 april tot en met 31 oktober. Wij zijn dagelijks geopend van 09:00 tot 18:00 uur. Reserveringen voor het nieuwe seizoen kunnen vanaf 1 januari gemaakt worden."
            }
        },
        {
            "@type": "Question",
            "name": "Waar ligt Nijenhuis Botenverhuur?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Nijenhuis Botenverhuur ligt aan de Veneweg 199 in Wanneperveen, Overijssel. Dit is ongeveer 10 km van Giethoorn en direct aan de rand van Nationaal Park Weerribben-Wieden. Er is gratis parkeergelegenheid bij ons aan de waterkant."
            }
        },
        {
            "@type": "Question",
            "name": "Moet ik vooraf reserveren?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Reserveren wordt sterk aanbevolen, vooral in het hoogseizoen (juli-augustus) en in het weekend. Je kunt online reserveren via onze website of telefonisch via 0522 281 528. Zonder reservering is beschikbaarheid niet gegarandeerd."
            }
        },
        {
            "@type": "Question",
            "name": "Hoeveel personen passen er op een boot?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Dit varieert per boottype: Kajak (1-2 personen), Canadese kano (3 personen), Electroboot (5 personen), Electrosloep 8 personen, Electrosloep 10 personen, en de Classic Tender voor 10-12 personen. Voor grotere groepen kun je meerdere boten huren."
            }
        },
        {
            "@type": "Question",
            "name": "Wat is inbegrepen bij de huur?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Bij de huurprijs is inbegrepen: het vaartuig met volgeladen accu, reddingsvesten, een vaarkaart van het gebied, en een korte instructie. Voor kano's en kajaks zijn peddels inbegrepen. De borg (<?php echo htmlspecialchars($faqDepositSnippet); ?> afhankelijk van boottype) krijg je terug bij onbeschadigde retour."
            }
        },
                        {
                            "@type": "Question",
                            "name": "Mag ik met de boot naar Giethoorn varen?",
                            "acceptedAnswer": {
                                "@type": "Answer",
                                "text": "Ja, vanuit onze locatie in Wanneperveen kun je via de Weerribben naar Giethoorn varen. De tocht duurt ongeveer 1,5 tot 2 uur enkele reis. Houd er rekening mee dat je in Giethoorn langzaam moet varen (max 6 km/u) en dat het druk kan zijn in het hoogseizoen. Voor een rustigere ervaring kun je ook Belt-schutsloot bezoeken, een vergelijkbaar maar minder toeristisch dorp in de buurt."
                            }
                        },
        {
            "@type": "Question",
            "name": "Hoe betaal ik?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Je kunt betalen met contant geld, pinpas of online via iDEAL bij het reserveren. De borg betaal je ter plaatse (contant of pin) en krijg je terug bij het inleveren van de boot."
            }
        },
        {
            "@type": "Question",
            "name": "Wat gebeurt er bij slecht weer?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Bij extreme weersomstandigheden (storm, onweer) kunnen wij besluiten de boten niet uit te laten varen voor jouw veiligheid. In dat geval kun je je reservering kosteloos verzetten naar een andere datum. Bij lichte regen kun je gewoon varen."
            }
        },
        {
            "@type": "Question",
            "name": "Mag ik mijn hond / huisdier meenemen aan boord?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Huisdieren zijn alleen toegestaan op onze electrosloepen, kano’s, de zeilpunter en de electroboot. Op alle andere boten zijn huisdieren niet toegestaan."
            }
        },
        {
            "@type": "Question",
            "name": "Wat is een fluisterboot?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Een fluisterboot is een elektrische sloep (electrosloep) die fluisterstil vaart dankzij de elektromotor. Bij Nijenhuis Botenverhuur kun je fluisterbootjes huren om naar Giethoorn te varen. Ze zijn ideaal voor gezinnen en groepen die comfortabel en stil door de Weerribben willen varen."
            }
        },
        {
            "@type": "Question",
            "name": "Kan ik vissen vanuit een gehuurde boot?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Ja, de Weerribben biedt uitstekende vismogelijkheden. Hoewel wij geen gespecialiseerde visboten of karperbootjes met tent verhuren, zijn onze electrosloepen en kano's uitstekend geschikt voor een dag vissen. De stille fluisterboot-motor stoort de vissen niet. Met een kano bereik je de rustigste visplekken."
            }
        },
        {
            "@type": "Question",
            "name": "Kan ik een bootje huren voor een paar uur?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Ja, voor alle boten is ook uurverhuur mogelijk. Uurverhuur kan alleen direct ter plaatse bij onze bootverhuur worden geboekt, niet online of telefonisch. Kom langs voor beschikbaarheid en directe boeking van bootjes verhuur per uur."
            }
        },
        {
            "@type": "Question",
            "name": "Bieden jullie SUP boards aan bij Giethoorn?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Ja, wij verhuren SUP boards. Je kunt stand-up paddelen door de Weerribben naar Giethoorn. De tocht duurt ongeveer 1,5 tot 2 uur enkele reis. SUP in Giethoorn is een actieve en unieke manier om het gebied te verkennen."
            }
        },
        {
            "@type": "Question",
            "name": "Kan ik een luxe sloep huren voor Giethoorn?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Ja, bij Nijenhuis Botenverhuur kun je luxe sloepen (electrosloepen/fluisterboten) huren om naar Giethoorn te varen. Onze sloepen zijn geschikt voor 8 tot 12 personen, comfortabel en fluisterstil. Sloepverhuur Giethoorn - reserveren wordt aanbevolen."
            }
        },
        {
            "@type": "Question",
            "name": "Kan ik een vakantieboot huren?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Ja, op vakantie in de Weerribben? Combineer uw verblijf met het huren van een boot. Wij verhuren boten van 1 april tot 31 oktober. Combineer met ons vakantiehuis of onze camping voor de perfecte vakantie boot huren ervaring."
            }
        },
        {
            "@type": "Question",
            "name": "Bent u op zoek naar bootverhuur vanuit de Randstad, zoals Alphen aan den Rijn?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Bent u op zoek naar bootverhuur vanuit de Randstad, zoals Alphen aan den Rijn? Overweeg een dagje naar de Weerribben bij Giethoorn voor een unieke natuurervaring. Vanuit Wanneperveen vaar je door Nationaal Park Weerribben-Wieden naar het Venetië van het Noorden. Ongeveer 1,5 uur rijden vanuit Alphen aan den Rijn."
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
            <div class="container">
                <div class="faq-intro-block">
                    <p data-i18n="faq_intro_expanded">Op deze pagina vind je antwoorden op de meest gestelde vragen over boot huren bij Nijenhuis Botenverhuur in de Weerribben. Onderwerpen die aan bod komen: prijzen per boottype, of je een vaarbewijs nodig hebt, openingstijden en reserveren, wat er bij de huur inbegrepen is, of je naar Giethoorn mag varen, en praktische zaken zoals betaling en huisdieren. Staat je vraag er niet bij? Neem gerust contact met ons op via het telefoonnummer of het contactformulier – we helpen je graag verder.</p>
                </div>
                <script>window.__FAQ_DATA__ = <?php echo json_encode($faqClientData); ?>;</script>
                <div data-i18n="faq_page_html"><?php include __DIR__ . '/../components/faq-page-body-nl.php'; ?></div>

                <div class="faq-contact-cta">
                    <p data-i18n="faq_contact_cta_p">Staat je vraag er niet bij? Neem gerust contact met ons op.</p>
                    <div class="faq-contact-links">
                        <a href="/contact" data-i18n="faq_contact_cta_form">Contactformulier</a>
                        <a href="tel:0522281528">0522 281 528</a>
                    </div>
                </div>
            </div>
        </section>

        <section class="content-section bg-secondary faq-fleet-section">
            <div class="container">
                <div class="section-heading">
                    <h2 data-i18n="faq_fleet_title">Bekijk onze vloot</h2>
                    <p data-i18n="faq_fleet_subtitle">Kies het boottype dat het beste bij jouw groep en wensen past:</p>
                </div>
                <ul class="faq-fleet-grid">
                    <li class="faq-fleet-card">
                        <strong>⚡ Electrosloep 8 personen</strong>
                        <span class="faq-fleet-desc">Fluisterstil, geen vaarbewijs nodig.</span>
                        <a href="/electrosloop-8#booking" data-i18n="faq_fleet_cta">Beschikbaarheid bekijken →</a>
                    </li>
                    <li class="faq-fleet-card">
                        <strong>⚡ Electrosloep 10 personen</strong>
                        <span class="faq-fleet-desc">Ideaal voor grotere groepen.</span>
                        <a href="/electrosloop-10#booking" data-i18n="faq_fleet_cta">Beschikbaarheid bekijken →</a>
                    </li>
                    <li class="faq-fleet-card">
                        <strong>🛶 Canadese Kano 3 personen</strong>
                        <span class="faq-fleet-desc">Avontuurlijk peddelen door de natuur.</span>
                        <a href="/canoe-3#booking" data-i18n="faq_fleet_cta">Beschikbaarheid bekijken →</a>
                    </li>
                    <li class="faq-fleet-card">
                        <strong>⛵ Zeilpunter 3/4 personen</strong>
                        <span class="faq-fleet-desc">Authentiek zeilen door de Weerribben.</span>
                        <a href="/sailpunter-3-4#booking" data-i18n="faq_fleet_cta">Beschikbaarheid bekijken →</a>
                    </li>
                    <li class="faq-fleet-card">
                        <strong>⛵ Zeilboot 4/5 personen</strong>
                        <span class="faq-fleet-desc">Voor een klassieke zeilbeleving.</span>
                        <a href="/sailboat-4-5#booking" data-i18n="faq_fleet_cta">Beschikbaarheid bekijken →</a>
                    </li>
                </ul>
            </div>
        </section>
    </main>

    <?php include __DIR__ . '/../components/footer.php'; ?>
</body>
</html>
