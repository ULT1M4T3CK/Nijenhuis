<?php
/**
 * Wanneperveen Page - Nijenhuis Botenverhuur
 * SEO landing page: top reasons to rent boats in Wanneperveen
 * Optimized for SEO and AI search
 * Prices loaded from boats.json (single source of truth)
 */
require_once __DIR__ . '/../components/config.php';
require_once __DIR__ . '/../components/faq_price_helper.php';
$basePath = getBasePath();

$faqBoats = faq_load_boats();
$faqElectrosloopPrice = faq_get_lowest_electrosloop_price($faqBoats);
$faqCanoePrice = faq_get_lowest_canoe_price($faqBoats);
$pageTitle = 'Bootverhuur Nijenhuis Wanneperveen: waarom bij ons';
$pageDescription = 'Geniet van rustig varen met Bootverhuur Nijenhuis Wanneperveen. Huur een boot in het vredige Wanneperveen en ontdek prachtige vaarwegen zonder drukte.';
$pageKeywords = 'bootverhuur nijenhuis wanneperveen, wanneperveen boot huren, fluisterboot wanneperveen, sloep huren nabij giethoorn, varen weerribben, gratis parkeren bootverhuur, mooiste vaarroutes wanneperveen, boot huren zonder vaarbewijs, electrosloep huren overijssel';
$headerTitle = 'Wanneperveen';
$headerTitleI18n = 'wanneperveen_title';
$headerDescription = 'Ontdek de mooiste vaarwegen van de Weerribben vanuit Wanneperveen';
$headerDescriptionI18n = 'wanneperveen_description';

$breadcrumbs = [
    ['name' => 'Home', 'url' => '/'],
    ['name' => 'Wanneperveen', 'url' => '/wanneperveen']
];
$additionalStyles = ['/frontend/css/pages/destination-pages.css?v=2', '/frontend/css/pages/boats.css'];
?>
<!DOCTYPE html>
<html lang="nl">
<?php include __DIR__ . '/../components/head.php'; ?>

<body data-page="wanneperveen">
    <?php include __DIR__ . '/../components/gtm-body.php'; ?>
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": ["Place", "TouristAttraction"],
    "@id": "https://nijenhuis-botenverhuur.com/wanneperveen#place",
    "name": "Wanneperveen",
    "description": "Wanneperveen is een rustig dorp in Overijssel, gelegen aan Nationaal Park Weerribben-Wieden. Het biedt directe toegang tot prachtige vaarwegen zonder de drukte van Giethoorn.",
    "url": "https://nijenhuis-botenverhuur.com/wanneperveen",
    "image": "https://nijenhuis-botenverhuur.com/frontend/Images/belterwijde.jpg",
    "geo": {
        "@type": "GeoCoordinates",
        "latitude": 52.7080,
        "longitude": 6.0850
    },
    "address": {
        "@type": "PostalAddress",
        "addressLocality": "Wanneperveen",
        "addressRegion": "Overijssel",
        "addressCountry": "NL",
        "postalCode": "7946"
    },
    "containedInPlace": {
        "@type": "NaturalFeature",
        "name": "Nationaal Park Weerribben-Wieden"
    },
    "touristType": ["NatureLover", "FamilyTourist", "CulturalTourist"],
    "keywords": "bootverhuur nijenhuis wanneperveen, wanneperveen boot huren, fluisterboot, sloep huren nabij giethoorn, varen weerribben, gratis parkeren bootverhuur"
}
</script>
    <?php include __DIR__ . '/../components/topbar.php'; ?>
    <?php include __DIR__ . '/../components/nav.php'; ?>
    <?php include __DIR__ . '/../components/breadcrumb.php'; ?>
    <?php include __DIR__ . '/../components/page-header.php'; ?>

    <main>

        <!-- ============================================================
             1. INTRO: Waarom Wanneperveen?
             ============================================================ -->
        <section class="content-section">
            <div class="container">
                <div class="destination-content">
                    <div class="destination-intro">
                        <div class="content-prose">
                            <p>
                                Verlang je naar de schoonheid van de Nederlandse vaarwegen zonder de drukke grachten van Giethoorn?
                                Dan is <strong>Wanneperveen</strong> de geheime achterdeur waar je naar op zoek was.
                                <strong>Bootverhuur Nijenhuis Wanneperveen</strong> biedt directe toegang tot rustig varen in
                                <strong>Nationaal Park Weerribben-Wieden</strong>. Zoals de locals weten: als je in
                                Wanneperveen een boot huurt, geniet je van hetzelfde prachtige landschap - maar met
                                aanzienlijk meer ruimte om van de natuur te genieten.
                            </p>
                        </div>
                    </div>
                </div>

                <h2 class="section-title" style="margin-bottom: 1rem;">Wanneperveen in het kort</h2>
                <div class="destination-stats-grid">
                    <div class="facility-card">
                        <div class="facility-icon">🌿</div>
                        <h3>Nationaal park</h3>
                        <p>Weerribben-Wieden</p>
                    </div>
                    <div class="facility-card">
                        <div class="facility-icon">🚤</div>
                        <h3>Vaarbewijs</h3>
                        <p>Niet nodig</p>
                    </div>
                    <div class="facility-card">
                        <div class="facility-icon">🅿️</div>
                        <h3>Parkeren</h3>
                        <p>Gratis aan de steiger</p>
                    </div>
                    <div class="facility-card">
                        <div class="facility-icon">📍</div>
                        <h3>Afstand Giethoorn</h3>
                        <p>±10 km varen</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ============================================================
             2. GEEN VAARBEWIJS NODIG
             ============================================================ -->
        <section class="content-section bg-secondary">
            <div class="container">
                <div class="destination-content">
                    <div class="section-title">
                        <h2>Geen vaarbewijs nodig: in een paar minuten een fluisterboot onder de knie</h2>
                    </div>

                    <div class="destination-card">
                        <div class="destination-split">
                            <div class="content-prose">
                                <p>
                                    Op een boot stappen kan best spannend zijn als je nog nooit het roer hebt vastgehouden,
                                    maar voor het verkennen van Wanneperveen heb je <strong>geen vaarbewijs of ervaring</strong>
                                    nodig. Onze vloot draait om de <strong>fluisterboot</strong> - een fluisterstille
                                    elektrische sloep die zonder uitlaatgassen of lawaai door het water glijdt. Dit bootje
                                    beweegt op een ontspannen tempo, zodat je de geluiden van de natuur hoort in plaats van
                                    het gebrul van een motor. Veilig en legaal voor iedereen boven de 18.
                                </p>
                                <p>
                                    Onze <strong>electrosloepen</strong> zijn eenvoudig te besturen: met een stuurwiel en
                                    een zachte gashendel vaar je intuïtief in de richting waar je heen wilt - geen gedoe met
                                    een helmstok waarbij "links duwen" de boot naar rechts stuurt. De leercurve blijft klein,
                                    ook voor eerste keer huurders.
                                </p>
                            </div>
                            <div class="destination-split__img-wrap">
                                <?php echo responsiveImage(
                                    'frontend/Images/Boats/electrosloep-8/electrosloop-8.jpg',
                                    'Electrosloep met stuurwiel voor eenvoudig varen in Wanneperveen',
                                    '(max-width: 768px) 100vw, 50vw'
                                ); ?>
                            </div>
                        </div>
                    </div>

                    <div class="destination-card">
                        <div class="destination-split">
                            <div class="destination-split__img-wrap">
                                <?php echo responsiveImage(
                                    'frontend/Images/Wanneperveen/fluisterboot-wanneperveen.jpg',
                                    'Fluisterboot op de vaarwegen van Nationaal Park Weerribben-Wieden',
                                    '(max-width: 768px) 100vw, 50vw'
                                ); ?>
                            </div>
                            <div class="content-prose">
                                <h3>Hands-on uitleg voor vertrek</h3>
                                <p>
                                    We zorgen ervoor dat je je nooit verloren voelt op het water. Voor vertrek krijg je een
                                    <strong>persoonlijke demonstratie</strong> van een medewerker. Die loopt met je door alle
                                    basishandelingen: van het bedienen van de zachte gashendel tot veilig achteruitvaren.
                                    Zo verlaat je de steiger met een zelfverzekerd gevoel in plaats van vraagtekens.
                                </p>
                                <p>
                                    Met de bediening onder de knie ben je klaar om de mooiste routes van de regio te ontdekken.
                                </p>
                                <a href="/electrosloop-8#booking" class="btn-read-more" style="display:inline-block;">Bekijk electrosloep 8 pers →</a>
                                &nbsp;&nbsp;<a href="/electrosloop-10#booking" class="btn-read-more" style="display:inline-block;">Bekijk electrosloep 10 pers →</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ============================================================
             3. DE MOOISTE ROUTES VANUIT WANNEPERVEEN
             ============================================================ -->
        <section class="content-section">
            <div class="container">
                <div class="destination-content">
                    <div class="section-title">
                        <h2>Van verborgen kreekjes tot open meren: de mooiste vaarroutes vanaf Wanneperveen</h2>
                    </div>

                    <div class="destination-card">
                        <div class="destination-split">
                            <div class="content-prose">
                                <h3>De Beulakerwijde en omgeving</h3>
                                <p>
                                    Zodra je de haven verlaat, ontvouwt het landschap zich in een mix van intieme vaarwegen en
                                    wijdse uitzichten. Veel gasten vinden de route over de <strong>Beulakerwijde</strong> - het
                                    grootste meer in de regio - een van de <strong>mooiste vaarroutes vanaf Wanneperveen</strong>.
                                    Het open water biedt een heerlijk gevoel van vrijheid op zonnige dagen, terwijl de smallere,
                                    met riet omzoomde kanalen beschutting geven en ongelooflijke kansen om lokale dieren te
                                    spotten: reigers, ijsvogels en aalscholvers, recht vanuit je boot.
                                </p>
                            </div>
                            <div class="destination-split__img-wrap">
                                <?php echo responsiveImage(
                                    'frontend/Images/Wanneperveen/beulakerwijde-view.jpg',
                                    'Uitzicht over de Beulakerwijde, het grootste meer in de Weerribben',
                                    '(max-width: 768px) 100vw, 50vw'
                                ); ?>
                            </div>
                        </div>
                    </div>

                    <div class="destination-card">
                        <div class="destination-split">
                            <div class="destination-split__img-wrap">
                                <?php echo responsiveImage(
                                    'frontend/Images/Wanneperveen/giethoorn-route-wanneperveen.jpg',
                                    'Varen door de grachten van Giethoorn vanuit Wanneperveen',
                                    '(max-width: 768px) 100vw, 50vw'
                                ); ?>
                            </div>
                            <div class="content-prose">
                                <h3>Vaar richting Giethoorn - op jouw tempo</h3>
                                <p>
                                    Navigeren door deze wateren is verrassend eenvoudig, zelfs als je richting het beroemde
                                    "Venetië van het Noorden" vaart. Als je bij ons een <strong>sloep huurt nabij Giethoorn</strong>,
                                    heb je het unieke voordeel dat je het drukke dorpscentrum kunt bezoeken én je daarna weer
                                    kunt terugtrekken in de stilte.
                                </p>
                                <p>
                                    Je kunt <strong>zelf varen door de grachten van Giethoorn</strong> door de duidelijke
                                    kleurgecodeerde palen te volgen die als verkeersborden op het water fungeren. Zo ontdek je
                                    vol vertrouwen de grachten zonder te verdwalen.
                                </p>
                                <a href="/giethoorn" class="btn-read-more" style="display:inline-block;">Ontdek Giethoorn →</a>
                            </div>
                        </div>
                    </div>

                    <div class="destination-card">
                        <div class="content-prose">
                            <h3>Met het hele gezin - inclusief de hond</h3>
                            <p>
                                Het hele gezin meenemen vraagt alleen wat extra planning qua ruimte en comfort. Onze stabiele
                                boten zijn ideaal voor kinderen, en we delen graag <strong>tips voor honden mee op de boot</strong>:
                                neem bijvoorbeeld een handdoek mee waarop ze kunnen liggen om uitglijden te voorkomen.
                                Met voldoende ruimte voor een picknickmand en veilige zitplaatsen kun je ontspannen in de
                                wetenschap dat je passagiers veilig zijn - terwijl je uitkijkt naar een zorgeloze terugtocht.
                            </p>
                            <ul class="destination-feature-list">
                                <li>✅ <strong>Stabiele boten</strong> - veilig voor kinderen en huisdieren</li>
                                <li>✅ <strong>Ruimte voor picknick</strong> - neem je eigen eten en drinken mee</li>
                                <li>✅ <strong>Hond aan boord</strong> - op <a href="/canoe-3#booking">kano's</a>, <a href="/sailpunter-3-4#booking">zeilpunter</a> en electroboot welkom</li>
                                <li>✅ <strong>Reddingsvesten</strong> - beschikbaar voor alle maten</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section class="cta-section" style="background: var(--secondary-color); color: white; padding: 4rem 0;">
            <div class="container">
                <div class="cta-content">
                    <p class="content-prose" style="margin-bottom: 2rem; opacity: 0.95;">
                        Onze locatie aan de Veneweg 199 in Wanneperveen is het perfecte startpunt voor een ontspannen
                        dag op het water - of je nu richting Giethoorn vaart of de rust van de Weerribben opzoekt.
                    </p>
                    <div class="cta-buttons">
                        <a href="/electrosloop-8#booking" class="btn-cta-primary">Bekijk electrosloep 8 pers</a>
                        <a href="/booking" class="btn-cta-outline">Direct boeken</a>
                    </div>
                </div>
            </div>
        </section>

        <!-- ============================================================
             4. STRESSVRIJ VERTREKKEN
             ============================================================ -->
        <section class="content-section bg-secondary">
            <div class="container">
                <div class="destination-content">
                    <div class="section-title">
                        <h2>Stressvrij vertrekken: gratis parkeren en eenvoudig boeken</h2>
                    </div>

                    <div class="destination-card">
                        <div class="destination-split">
                            <div class="content-prose">
                                <p>
                                    Je avontuur starten bij Nijenhuis betekent dat je de parkeerstressen van Giethoorn overslaat.
                                    Met <strong>gratis parkeren bij de bootverhuur</strong> recht aan de steiger bespaar je tijd
                                    voor wat er écht toe doet: ontspannen op het water.
                                </p>
                                <p>
                                    Check online <strong>wat een dagje varen in de Weerribben kost</strong> en reserveer je plek
                                    voor de <strong>beste reistijd voor varen in Overijssel</strong>. Boeken is eenvoudig via
                                    onze website - je ontvangt direct een bevestiging.
                                </p>
                                <a href="/booking" class="btn-read-more" style="display:inline-block;">Nu reserveren →</a>
                            </div>
                            <div class="destination-split__img-wrap">
                                <?php echo responsiveImage(
                                    'frontend/Images/Wanneperveen/wanneperveen-nature.jpg',
                                    'Rustige vaarroute door Nationaal Park Weerribben-Wieden bij Wanneperveen',
                                    '(max-width: 768px) 100vw, 50vw'
                                ); ?>
                            </div>
                        </div>
                    </div>

                    <div class="destination-card">
                        <div class="content-prose">
                            <h3>Checklist voor je vertrek</h3>
                            <ul class="destination-feature-list">
                                <li>✅ <strong>Digitale boekingsbevestiging</strong> - check je e-mail voor vertrek</li>
                                <li>✅ <strong>Zonnebrandcrème en zonnebril</strong> - op het water brand je sneller</li>
                                <li>✅ <strong>Picknickmand en water</strong> - geniet van een lunch op het water</li>
                                <li>✅ <strong>Handdoek voor de hond</strong> - als je viervoetige vriend mee gaat</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ============================================================
             5. ONTDEK OOK
             ============================================================ -->
        <section class="content-section comparison-section">
            <div class="container">
                <div class="destination-content">
                    <div class="comparison-highlight">
                        <span>🗺️</span>
                        <h2>Ontdek ook onze andere bestemmingen</h2>
                    </div>

                    <div class="content-prose">
                        <p>
                            Vanuit Wanneperveen heb je toegang tot de mooiste plekken in de Weerribben.
                            Bezoek het wereldberoemde <strong>Giethoorn</strong> of ontdek het rustiger
                            <strong>Belt-schutsloot</strong> - of combineer beide in één onvergetelijke dag op het water.
                        </p>
                    </div>

                    <div class="comparison-box">
                        <h3>Populaire bestemmingen</h3>
                        <ul>
                            <li>✅ <strong><a href="/giethoorn">Giethoorn</a>:</strong> ±10 km - het Venetië van het Noorden, bereikbaar in 1,5-2 uur</li>
                            <li>✅ <strong><a href="/belt-schutsloot">Belt-schutsloot</a>:</strong> ±6-8 km - verborgen parel, rustiger dan Giethoorn</li>
                            <li>✅ <strong><a href="/vaarkaart">Weerribben natuurroute</a>:</strong> ±15 km - door het hart van het Nationaal Park</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <!-- ============================================================
             6. VEELGESTELDE VRAGEN
             ============================================================ -->
        <section class="content-section faq-section">
            <div class="container" style="max-width: 800px;">
                <h2 style="text-align: center; margin-bottom: 2rem;">Veelgestelde vragen over bootverhuur in Wanneperveen</h2>

                <script type="application/ld+json">
                {
                    "@context": "https://schema.org",
                    "@type": "FAQPage",
                    "mainEntity": [
                        {
                            "@type": "Question",
                            "name": "Heb ik een vaarbewijs nodig om in Wanneperveen te varen?",
                            "acceptedAnswer": {
                                "@type": "Answer",
                                "text": "Nee, je hebt geen vaarbewijs nodig. Onze fluisterboten en electrosloepen zijn eenvoudig te besturen en mogen zonder vaarbewijs worden gevaren. Je krijgt voor vertrek een persoonlijke instructie zodat je je op je gemak voelt."
                            }
                        },
                        {
                            "@type": "Question",
                            "name": "Kan ik vanuit Wanneperveen naar Giethoorn varen?",
                            "acceptedAnswer": {
                                "@type": "Answer",
                                "text": "Ja, Giethoorn ligt op ongeveer 10 kilometer varen vanaf onze locatie in Wanneperveen. De route gaat door het prachtige Nationaal Park Weerribben-Wieden en duurt ongeveer 1,5 tot 2 uur enkele reis."
                            }
                        },
                        {
                            "@type": "Question",
                            "name": "Is er gratis parkeren bij de bootverhuur?",
                            "acceptedAnswer": {
                                "@type": "Answer",
                                "text": "Ja, bij Nijenhuis Botenverhuur in Wanneperveen is parkeren helemaal gratis. Je parkeert direct bij de steiger, zodat je meteen het water op kunt."
                            }
                        },
                        {
                            "@type": "Question",
                            "name": "Mag ik mijn hond meenemen op de boot?",
                            "acceptedAnswer": {
                                "@type": "Answer",
                                "text": "Huisdieren zijn alleen toegestaan op onze electrosloepen, kano's, de zeilpunter en de electroboot. Op alle andere boten zijn huisdieren niet toegestaan. Neem een handdoek mee waarop je hond kan liggen om uitglijden te voorkomen."
                            }
                        },
                        {
                            "@type": "Question",
                            "name": "Wat kost een dagje varen in de Weerribben?",
                            "acceptedAnswer": {
                                "@type": "Answer",
                                "text": "De prijzen variëren per boottype. Electrosloepen zijn er vanaf €<?php echo (int)($faqElectrosloopPrice ?? 0); ?> per dag en kano's vanaf €<?php echo (int)($faqCanoePrice ?? 0); ?> per dag. Bekijk onze botenverhuur pagina voor alle actuele prijzen en beschikbaarheid."
                            }
                        },
                        {
                            "@type": "Question",
                            "name": "Wat is de beste reistijd voor varen in Overijssel?",
                            "acceptedAnswer": {
                                "@type": "Answer",
                                "text": "Het vaarseizoen loopt van 1 april tot 31 oktober. De mooiste periode is van mei tot en met september, wanneer het weer het meest stabiel is. Voor een rustigere ervaring zijn doordeweekse dagen en het voor- of naseizoen ideaal."
                            }
                        }
                    ]
                }
                </script>

                <div class="faq-accordion">
                    <details class="faq-item">
                        <summary>
                            <span class="faq-question">Heb ik een vaarbewijs nodig om in Wanneperveen te varen?</span>
                            <span class="faq-icon"></span>
                        </summary>
                        <div class="faq-answer">
                            <p>Nee, je hebt geen vaarbewijs nodig. Onze fluisterboten en electrosloepen zijn eenvoudig te besturen en mogen zonder vaarbewijs worden gevaren. Je krijgt voor vertrek een persoonlijke instructie zodat je je op je gemak voelt.</p>
                        </div>
                    </details>

                    <details class="faq-item">
                        <summary>
                            <span class="faq-question">Kan ik vanuit Wanneperveen naar Giethoorn varen?</span>
                            <span class="faq-icon"></span>
                        </summary>
                        <div class="faq-answer">
                            <p>Ja, Giethoorn ligt op ongeveer 10 kilometer varen vanaf onze locatie in Wanneperveen. De route gaat door het prachtige Nationaal Park Weerribben-Wieden en duurt ongeveer 1,5 tot 2 uur enkele reis. Je volgt kleurgecodeerde palen die als verkeersborden op het water dienen.</p>
                        </div>
                    </details>

                    <details class="faq-item">
                        <summary>
                            <span class="faq-question">Is er gratis parkeren bij de bootverhuur?</span>
                            <span class="faq-icon"></span>
                        </summary>
                        <div class="faq-answer">
                            <p>Ja, bij Nijenhuis Botenverhuur in Wanneperveen is parkeren helemaal gratis. Je parkeert direct bij de steiger, zodat je meteen het water op kunt.</p>
                        </div>
                    </details>

                    <details class="faq-item">
                        <summary>
                            <span class="faq-question">Mag ik mijn hond meenemen op de boot?</span>
                            <span class="faq-icon"></span>
                        </summary>
                        <div class="faq-answer">
                            <p>Huisdieren zijn alleen toegestaan op onze electrosloepen, kano's, de zeilpunter en de electroboot. Op alle andere boten zijn huisdieren niet toegestaan. Neem een handdoek mee waarop je hond kan liggen om uitglijden te voorkomen.</p>
                        </div>
                    </details>

                    <details class="faq-item">
                        <summary>
                            <span class="faq-question">Wat kost een dagje varen in de Weerribben?</span>
                            <span class="faq-icon"></span>
                        </summary>
                        <div class="faq-answer">
                            <p>De prijzen variëren per boottype. <a href="/electrosloop-8#booking">Electrosloepen</a> zijn er vanaf €<?php echo (int)($faqElectrosloopPrice ?? 0); ?> per dag en <a href="/canoe-3#booking">kano's</a> vanaf €<?php echo (int)($faqCanoePrice ?? 0); ?> per dag. Bekijk ook de <a href="/sailpunter-3-4#booking">zeilpunter</a> of <a href="/sailboat-4-5#booking">zeilboot</a> voor een authentieke vaarbeleving.</p>
                        </div>
                    </details>

                    <details class="faq-item">
                        <summary>
                            <span class="faq-question">Wat is de beste reistijd voor varen in Overijssel?</span>
                            <span class="faq-icon"></span>
                        </summary>
                        <div class="faq-answer">
                            <p>Het vaarseizoen loopt van 1 april tot 31 oktober. De mooiste periode is van mei tot en met september, wanneer het weer het meest stabiel is. Voor een rustigere ervaring zijn doordeweekse dagen en het voor- of naseizoen ideaal.</p>
                        </div>
                    </details>
                </div>
            </div>
        </section>

        <!-- ============================================================
             7. FINAL CTA
             ============================================================ -->
        <section class="cta-section" style="background: var(--secondary-color); color: white; padding: 4rem 0;">
            <div class="container">
                <div class="cta-content">
                    <h2 style="color: white; margin-bottom: 1rem;">Klaar om Wanneperveen te ontdekken?</h2>
                    <p class="content-prose" style="margin-bottom: 2rem; opacity: 0.95;">
                        Reserveer nu je boot en geniet van rustig varen door de Weerribben. Gratis parkeren,
                        geen vaarbewijs nodig en persoonlijke instructie voor vertrek.
                    </p>
                    <div class="cta-buttons">
                        <a href="/booking" class="btn-cta-primary">Direct boeken</a>
                        <a href="/electrosloop-10#booking" class="btn-cta-outline">Bekijk electrosloep 10 pers</a>
                    </div>
                </div>
            </div>
        </section>

    </main>

    <?php include __DIR__ . '/../components/footer.php'; ?>
</body>
</html>
