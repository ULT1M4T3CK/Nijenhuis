<?php
/**
 * Giethoorn Page - Nijenhuis Botenverhuur
 * Comprehensive guide about Giethoorn, optimized for SEO and AI search
 */
require_once __DIR__ . '/../components/config.php';
$basePath = getBasePath();
$pageTitle = 'Giethoorn';
$pageTitleFull = 'Boot huren bij Giethoorn zonder drukte | Nijenhuis Botenverhuur';
$pageDescription = 'Boot huren bij Giethoorn zonder de drukte ✔️ Vertrek rustig vanuit Wanneperveen ✔️ Gratis parkeren ✔️ Sloep, fluisterboot, kano of SUP ✔️ Vanaf €20/dag.';
$pageKeywords = 'giethoorn, bootje huren giethoorn, bootje varen giethoorn, fluisterboot huren giethoorn, fluisterbootje giethoorn, sloepverhuur giethoorn, luxe sloep huren giethoorn, sup giethoorn, bootverhuur giethoorn, giethoorn boot huren, venetië van het noorden';
$headerTitle = 'Boot huren bij Giethoorn — start rustig vanuit Wanneperveen';
$headerTitleI18n = 'giethoorn_title';
$headerDescription = 'Ontdek het prachtige Giethoorn en de omliggende Weerribben per boot';
$headerDescriptionI18n = 'giethoorn_description';

// Breadcrumbs for this page
$breadcrumbs = [
    ['name' => 'Home', 'url' => '/'],
    ['name' => 'Giethoorn', 'url' => '/giethoorn']
];
$additionalStyles = ['/frontend/css/pages/destination-pages.css?v=3', '/frontend/css/pages/boats.css'];
?>
<!DOCTYPE html>
<html lang="nl">
<?php include __DIR__ . '/../components/head.php'; ?>

<body data-page="giethoorn">
    <?php include __DIR__ . '/../components/gtm-body.php'; ?>
<!-- Place Schema.org Structured Data for Giethoorn -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": ["Place", "TouristAttraction", "City"],
    "@id": "https://nijenhuis-botenverhuur.com/giethoorn#place",
    "name": "Giethoorn",
    "alternateName": ["Venetië van het Noorden", "Venice of the North"],
    "description": "Giethoorn is een pittoresk dorp in de Nederlandse provincie Overijssel, bekend om zijn vele grachten, bruggetjes en karakteristieke rietgedekte boerderijen. Het dorp ligt in Nationaal Park Weerribben-Wieden en is een populaire toeristische bestemming.",
    "url": "https://nijenhuis-botenverhuur.com/giethoorn",
    "image": "https://nijenhuis-botenverhuur.com/frontend/Images/Giethoorn/Giethoorn1.png",
    "geo": {
        "@type": "GeoCoordinates",
        "latitude": 52.7397,
        "longitude": 6.0774
    },
    "address": {
        "@type": "PostalAddress",
        "addressLocality": "Giethoorn",
        "addressRegion": "Overijssel",
        "addressCountry": "NL",
        "postalCode": "8355"
    },
    "containedInPlace": {
        "@type": "NaturalFeature",
        "name": "Nationaal Park Weerribben-Wieden"
    },
    "touristType": ["CulturalTourist", "NatureLover", "FamilyTourist"],
    "keywords": "giethoorn, bootje huren, bootje varen, fluisterboot, sloepverhuur, venetië van het noorden, grachten, punter, boot huren, sup giethoorn, weerribben, overijssel, toerisme"
}
</script>
    <?php include __DIR__ . '/../components/topbar.php'; ?>
    <?php include __DIR__ . '/../components/nav.php'; ?>
    <?php include __DIR__ . '/../components/breadcrumb.php'; ?>
    <?php include __DIR__ . '/../components/page-header.php'; ?>

    <main>

        <!-- ============================================================
             1. INTRO: Giethoorn bootverhuur & grachtenervaring
             ============================================================ -->
        <section class="content-section">
            <div class="container">
                <div class="destination-content">
                    <div class="destination-intro">
                        <div class="content-prose">
                            <p>
                                Wil je een <strong>boot huren bij Giethoorn zonder de drukte</strong>? Start dan niet midden in
                                het toeristische centrum, maar rustig vanuit Wanneperveen. Bij Nijenhuis Botenverhuur parkeer je
                                gratis aan de steiger, krijg je persoonlijke uitleg en vaar je via het waterrijke
                                <strong>Nationaal Park Weerribben-Wieden</strong> richting Giethoorn.
                            </p>
                            <p>
                                Giethoorn blijft bijzonder: grachten, bruggetjes en rietgedekte boerderijen maken het dorp uniek.
                                Het verschil zit in je vertrekpunt. Vanuit Wanneperveen vaar je eerst door rustiger water en kun je
                                Giethoorn op je eigen tempo bezoeken, zonder wachtrij bij het instappen.
                            </p>
                        </div>
                    </div>
                </div>

                <h2 class="section-title" style="margin-bottom: 1rem;">Giethoorn in het kort</h2>
                <div class="destination-stats-grid">
                    <div class="facility-card">
                        <div class="facility-icon">🏘️</div>
                        <h3>Woonplaats</h3>
                        <p>±2.500 inwoners</p>
                    </div>
                    <div class="facility-card">
                        <div class="facility-icon">🌉</div>
                        <h3>Bruggetjes</h3>
                        <p>Meer dan 180</p>
                    </div>
                    <div class="facility-card">
                        <div class="facility-icon">🚤</div>
                        <h3>Grachten</h3>
                        <p>4,5 km lang</p>
                    </div>
                    <div class="facility-card">
                        <div class="facility-icon">🌿</div>
                        <h3>Nationaal park</h3>
                        <p>Weerribben-Wieden</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ============================================================
             2. WAAROM GIETHOORN ZO BIJZONDER IS
             ============================================================ -->
        <section class="content-section bg-secondary">
            <div class="container">
                <div class="destination-content">
                    <div class="section-title">
                        <h2>Waarom Giethoorn zo bijzonder is</h2>
                    </div>

                    <div class="destination-card">
                        <div class="destination-split">
                            <div class="content-prose">
                                <p>
                                    De charme van Giethoorn zit in de rustgevende waterwegen, de huisjes met rieten daken en de
                                    schilderachtige houten bruggetjes. Dit plaatje heeft Giethoorn internationaal op de kaart gezet
                                    als een van de mooiste dorpen van Nederland.
                                </p>
                                <p>Bezoekers komen naar Giethoorn om:</p>
                                <ul class="destination-feature-list">
                                    <li>✅ <strong>Unieke grachtensfeer</strong> - geen auto's, alleen boten en voetpaden</li>
                                    <li>✅ <strong>Schilderachtige landschappen</strong> - perfect voor fotografie en ontspanning</li>
                                    <li>✅ <strong>Rijke cultuur &amp; geschiedenis</strong> - van turfstekers tot wereldberoemd dorp</li>
                                </ul>
                                <p>
                                    Giethoorn ligt midden in <strong>Nationaal Park Weerribben-Wieden</strong> - het grootste
                                    laagveenmoeras van Noordwest-Europa. Dat maakt het dorp niet alleen mooi, maar ook ecologisch bijzonder.
                                </p>
                            </div>
                            <div class="destination-split__img-wrap">
                                <?php echo responsiveImage(
                                    'frontend/Images/Giethoorn/Giethoorn1.png',
                                    'Schilderachtig uitzicht op Giethoorn met rietgedekte huisjes en houten bruggetjes',
                                    '(max-width: 768px) 100vw, 50vw'
                                ); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ============================================================
             3. BOOTVERHUUR IN GIETHOORN: OPTIES EN TIPS
             ============================================================ -->
        <section class="content-section">
            <div class="container">
                <div class="destination-content">
                    <div class="section-title">
                        <h2>Bootverhuur in Giethoorn: opties en tips</h2>
                    </div>

                    <div class="destination-card">
                        <div class="destination-split">
                            <div class="destination-split__img-wrap">
                                <?php echo responsiveImage(
                                    'frontend/Images/Giethoorn/giethoorn4.jpeg',
                                    'Verschillende bootjes beschikbaar voor verhuur richting Giethoorn',
                                    '(max-width: 768px) 100vw, 50vw'
                                ); ?>
                            </div>
                            <div class="content-prose">
                                <p>
                                    Wil je Giethoorn beleven zoals een local? <strong>Nijenhuis Botenverhuur</strong> is dé plek
                                    voor bootverhuur bij Giethoorn. Wij zitten net buiten Giethoorn in Wanneperveen - gratis parkeren, geen
                                    toeristische drukte, geen massatoerisme. Je vaart door de rustige Weerribben naar het dorp
                                    en ervaart de grachten op jouw tempo.
                                </p>
                                <p>
                                    Of je nu kiest voor een stille <a href="/electrosloop-8#booking"><strong>fluisterboot</strong></a>, een avontuurlijke <a href="/canoe-3#booking"><strong>kano</strong></a>
                                    of een authentieke <a href="/sailpunter-3-4#booking"><strong>zeilpunter</strong></a> - elk boottype biedt een eigen beleving van de
                                    prachtige grachten. Varen geeft je toegang tot plekjes die te voet onbereikbaar zijn.
                                </p>
                                <p>Tips voor een vlekkeloze vaartocht:</p>
                                <ul class="destination-feature-list">
                                    <li>✅ <strong>Kies Nijenhuis</strong> - net buiten Giethoorn, ervaar het als een local zonder toeristische drukte</li>
                                    <li>✅ <strong>Boek van tevoren</strong> - zeker in het hoogseizoen is online reserveren aan te raden</li>
                                    <li>✅ <strong>Kies het juiste boottype</strong> - fluisterboot voor comfort, kano of SUP voor avontuur</li>
                                    <li>✅ <strong><a href="/wanneperveen" style="color: inherit;">Vertrek vanuit Wanneperveen</a></strong> - ideaal startpunt voor een tocht door de Weerribben naar Giethoorn</li>
                                    <li>✅ <strong>Neem voldoende tijd</strong> - een dagje is ideaal om zowel Giethoorn als de natuur te verkennen</li>
                                </ul>
                                <p><a href="/#boten">Bekijk ons volledig aanbod →</a></p>
                                <p><a href="/blog/giethoorn-drukte-vermijden-rustig-varen">Lees ook: Giethoorn te druk? Zo vaar je rustig door de Weerribben →</a></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ============================================================
             4. BOOT & BOOTJE HUREN VOOR GIETHOORN
             ============================================================ -->
        <section class="content-section bg-secondary content-section--wide-cards">
            <div class="container">
                <div class="destination-content">
                    <div class="section-title">
                        <h2>Boot &amp; bootje huren voor Giethoorn</h2>
                        <p>Bootje huren voor een dagje Giethoorn? Bij Nijenhuis Botenverhuur in Wanneperveen kunt u verschillende boten huren om naar Giethoorn te varen. Bootverhuur Giethoorn was nog nooit zo makkelijk.</p>
                    </div>

                    <div class="facilities-grid">
                        <div class="facility-card">
                            <div class="facility-icon">⚡</div>
                            <h3>Fluisterboten (electrosloepen)</h3>
                            <p>
                                Fluisterboot huren voor Giethoorn? Onze fluisterboten zijn fluisterstil en comfortabel - ideaal
                                voor het hele gezin. Huur een luxe sloep of fluisterbootje voor 8 of 10 personen.
                            </p>
                            <a href="/electrosloop-8#booking">Bekijk electrosloep 8 pers →</a><br>
                            <a href="/electrosloop-10#booking">Bekijk electrosloep 10 pers →</a>
                        </div>
                        <div class="facility-card">
                            <div class="facility-icon">⛵</div>
                            <h3>Zeilpunters &amp; zeilboten</h3>
                            <p>
                                Wil je een zeilboot huren om naar Giethoorn te varen? Onze traditionele zeilpunters brengen
                                je sfeervol door de smalle slootjes. Boot huren Giethoorn op zijn authentiekst.
                            </p>
                            <a href="/sailpunter-3-4#booking">Bekijk zeilpunter 3/4 pers →</a><br>
                            <a href="/sailboat-4-5#booking">Bekijk zeilboot 4/5 pers →</a>
                        </div>
                        <div class="facility-card">
                            <div class="facility-icon">🛶</div>
                            <h3>Kano's &amp; kajaks</h3>
                            <p>
                                Voor wie actief wil bootje varen naar Giethoorn: huur een kano of kajak en peddel door de
                                natuurrijke vaarroutes. Giethoorn boot huren was nog nooit zo avontuurlijk.
                            </p>
                            <a href="/canoe-3#booking">Bekijk Canadese kano 3 pers →</a>
                        </div>
                        <div class="facility-card">
                            <div class="facility-icon">🏄</div>
                            <h3>SUP naar Giethoorn</h3>
                            <p>
                                Ook voor SUP in Giethoorn ben je bij ons aan het juiste adres. Peddel met een SUP board
                                door de Weerribben naar het Venetië van het Noorden - bootje varen en sport in één.
                            </p>
                            <a href="/botenverhuur">Bekijk SUP boards →</a>
                        </div>
                        <div class="facility-card">
                            <div class="facility-icon">🛥️</div>
                            <h3>Sloepverhuur Giethoorn</h3>
                            <p>
                                Onze sloepverhuur biedt luxe sloepen om naar Giethoorn te varen. Ideaal voor een ontspannen
                                dagje op het water - sloep huren in Giethoorn kan vanaf Wanneperveen.
                            </p>
                            <a href="/electrosloop-10#booking">Bekijk luxe electrosloep →</a>
                        </div>
                        <div class="facility-card">
                            <div class="facility-icon">🚤</div>
                            <h3>Bootjes verhuur</h3>
                            <p>
                                Bij Nijenhuis verhuren we bootjes voor een dagje Giethoorn. Kies uit sloepen, kano's en
                                zeilboten - onze botenverhuur biedt voor elk wat wils.
                            </p>
                            <a href="/botenverhuur">Bekijk alle bootjes →</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ============================================================
             4A. SLOEP HUREN IN GIETHOORN
             ============================================================ -->
        <section class="content-section">
            <div class="container">
                <div class="destination-content">
                    <div class="section-title">
                        <h2>Sloep huren in Giethoorn</h2>
                    </div>

                    <div class="destination-card">
                        <div class="content-prose">
                            <p>
                                Een sloep huren in Giethoorn is de perfecte keuze voor grotere gezelschappen. Onze luxe electrosloepen
                                bieden ruimte voor 8 tot 12 personen, met comfortabele zitplaatsen en een tafel aan boord — ideaal voor
                                een ontspannen dagje varen met familie of vrienden.
                            </p>
                            <p>
                                Bij Nijenhuis kun je kiezen uit de <a href="/electrosloop-8#booking"><strong>Electrosloop 8 personen</strong></a>
                                of de ruimere <a href="/electrosloop-10#booking"><strong>Electrosloop 10 personen</strong></a>. Beide sloepen
                                zijn elektrisch aangedreven, fluisterstil en eenvoudig te besturen — geen vaarbewijs nodig. Sloep huren
                                Giethoorn betekent genieten van de grachten zonder stress.
                            </p>
                            <p>
                                Ga je met een groep van 8, 10 of 12 personen? Lees onze
                                <a href="/blog/sloep-huren-groep-8-10-12-personen">gids voor sloep huren met een groep</a> of ons artikel over
                                <a href="/blog/motorboot-huren-giethoorn-weerribben">motorboot huren bij Giethoorn</a> voor tips
                                over routes en planning. Sloep huren bij Giethoorn is al mogelijk vanaf €75 per dag.
                            </p>
                            <a href="/booking" class="btn">Sloep reserveren →</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ============================================================
             4B. GOEDKOOP BOOTJE HUREN BIJ GIETHOORN
             ============================================================ -->
        <section class="content-section bg-secondary">
            <div class="container">
                <div class="destination-content">
                    <div class="section-title">
                        <h2>Goedkoop bootje huren bij Giethoorn</h2>
                    </div>

                    <div class="destination-card">
                        <div class="content-prose">
                            <p>
                                Goedkoop een bootje huren bij Giethoorn? Dat kan! Onze kano's zijn al beschikbaar vanaf €20 per dag
                                en onze kleine elektrische boten zijn eveneens betaalbaar. Je hebt geen vaarbewijs nodig, dus je kunt
                                direct het water op.
                            </p>
                            <p>
                                Een bootje huren in Giethoorn hoeft niet duur te zijn. Vertrek vanuit Wanneperveen en bespaar op
                                parkeerkosten én bootprijzen vergeleken met het centrum. Bekijk ons
                                <a href="/botenverhuur">volledige aanbod en prijsoverzicht</a> om de boot te vinden die bij je budget past.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ============================================================
             4C. GIETHOORN ZONDER DRUKTE
             ============================================================ -->
        <section class="content-section">
            <div class="container">
                <div class="destination-content">
                    <div class="section-title">
                        <h2>Giethoorn zonder drukte: waarom starten in Wanneperveen werkt</h2>
                    </div>

                    <div class="destination-card">
                        <div class="content-prose">
                            <p>
                                In het hoogseizoen kan het centrum van Giethoorn druk zijn met dagjesmensen, rondvaartboten en
                                volle parkeerplaatsen. Door in Wanneperveen te starten, kies je voor een rustiger begin van je
                                vaardag. Je stapt direct bij onze steiger aan boord, vaart eerst door de Weerribben-Wieden en
                                bepaalt daarna zelf of je de grachten van Giethoorn in vaart of juist een stillere route kiest.
                            </p>
                            <ul class="destination-feature-list">
                                <li>✅ <strong>Gratis parkeren</strong> bij de verhuurlocatie in Wanneperveen</li>
                                <li>✅ <strong>Geen wachtrij in het centrum</strong> voor je bootinstructie</li>
                                <li>✅ <strong>Meer routekeuze</strong>: Giethoorn, Belt-schutsloot, Beulakerwijde of de Weerribben</li>
                                <li>✅ <strong>Lokale ervaring</strong> met persoonlijke tips van een familiebedrijf</li>
                            </ul>
                            <p>
                                Wil je vooral natuur en rust? Bekijk dan ook onze pagina over
                                <a href="/weerribben">bootje huren in de Weerribben-Wieden</a>.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA 1 -->
        <section class="cta-section" style="background: var(--secondary-color); color: white; padding: 4rem 0;">
            <div class="container">
                <div class="cta-content">
                    <p class="content-prose" style="margin-bottom: 2rem; opacity: 0.95;">
                        Onze locatie in <a href="/wanneperveen" style="color: white; text-decoration: underline; font-weight: 500;">Wanneperveen</a> ligt op ongeveer 10 kilometer van Giethoorn - perfect voor een mooie
                        vaartocht door de Weerribben naar het Venetië van het Noorden.
                    </p>
                    <div class="cta-buttons">
                        <a href="/#boten" class="btn-cta-primary">Bekijk alle boten</a>
                        <a href="/booking" class="btn-cta-outline">Direct boeken</a>
                    </div>
                </div>
            </div>
        </section>

        <!-- ============================================================
             5. PRAKTISCHE INFORMATIE: RESERVEREN, REGELS & VEILIGHEID
             ============================================================ -->
        <section class="content-section">
            <div class="container">
                <div class="destination-content">
                    <div class="section-title">
                        <h2>Praktische informatie: reserveren, regels &amp; veiligheid</h2>
                    </div>

                    <div class="destination-card">
                        <div class="destination-split">
                            <div class="content-prose">
                                <h3>Reserveren</h3>
                                <p>
                                    Een boot reserveren bij Nijenhuis Botenverhuur is eenvoudig. Wil je een boot huren in
                                    Giethoorn? Je kunt online boeken, zodat je zeker bent van je plek - ook op drukke dagen
                                    in het hoogseizoen. Online reserveren helpt drukte te vermijden en garandeert beschikbaarheid.
                                </p>
                                <a href="/booking" class="btn" style="margin-bottom: 1.5rem;">Nu reserveren →</a>

                                <h3>Regels &amp; veiligheid op het water</h3>
                                <p>Veiligheid staat voorop tijdens het varen op de grachten van Giethoorn:</p>
                                <ul class="destination-feature-list">
                                    <li>🦺 <strong>Draag altijd een reddingsvest</strong> - beschikbaar bij elke verhuur</li>
                                    <li>🗺️ <strong>Volg de aangegeven vaarroutes</strong> - zeker in en rond Giethoorn</li>
                                    <li>🤝 <strong>Wees respectvol</strong> - naar andere boten, bewoners én de natuur</li>
                                    <li>🚤 <strong>Max. 6 km/u in Giethoorn</strong> - de snelheidslimiet wordt streng gehandhaafd</li>
                                </ul>
                                <p>
                                    Door deze richtlijnen te volgen kun je zorgeloos genieten van je vaartocht.
                                    Veiligheid en voorbereiding zorgen voor een prachtige ervaring.
                                </p>
                            </div>
                            <div class="destination-split__img-wrap">
                                <img src="https://images.pexels.com/photos/34756836/pexels-photo-34756836.jpeg?auto=compress&cs=tinysrgb&w=1920"
                                     alt="Rustige vaarroute door Nationaal Park Weerribben-Wieden"
                                     loading="lazy">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ============================================================
             6. DE ULTIEME GRACHTENERVARING: ROUTES, ETEN & FOTO'S
             ============================================================ -->
        <section class="content-section bg-secondary">
            <div class="container">
                <div class="destination-content">
                    <div class="section-title">
                        <h2>De ultieme grachtenervaring: routes, eten &amp; foto's</h2>
                    </div>

                    <div class="destination-card">
                        <h3>📍 Vaarroute vanuit Wanneperveen naar Giethoorn</h3>
                        <div class="destination-split">
                            <div class="content-prose">
                                <p>
                                    Een boottocht in Giethoorn biedt meer dan alleen varen. Vanaf onze locatie in Wanneperveen
                                    (Veneweg 199) is het ongeveer <strong>10 kilometer varen</strong> naar het centrum van
                                    Giethoorn, door het prachtige <strong>Nationaal Park Weerribben-Wieden</strong>.
                                </p>
                                <p>
                                    Onderweg kom je langs rietvelden, moerassen en karakteristieke boerderijen. Na aankomst
                                    in Giethoorn kun je genieten van lokale gerechten bij een van de restaurants aan het water -
                                    het perfecte afsluiter van een dag op het water.
                                </p>
                                <p>Voor fotoliefhebbers is Giethoorn een paradijs:</p>
                                <ul class="destination-feature-list">
                                    <li>📸 <strong>Bezoek de pittoreske bruggetjes</strong> - elke brug een uniek plaatje</li>
                                    <li>🌿 <strong>Ontdek verborgen hoeken</strong> - vanuit het water zie je meer dan te voet</li>
                                    <li>🍽️ <strong>Proef lokale lekkernijen</strong> - restaurants en terrasjes langs het water</li>
                                </ul>
                                <div class="route-details">
                                    <h4>Route details</h4>
                                    <ul>
                                        <li>✅ <strong>Afstand:</strong> 10 km (enkele reis)</li>
                                        <li>✅ <strong>Duur:</strong> 1,5–2 uur (heen en terug)</li>
                                        <li>✅ <strong>Moeilijkheidsgraad:</strong> Gemakkelijk</li>
                                        <li>✅ <strong>Max. vaarsnelheid:</strong> 6 km/u in Giethoorn</li>
                                    </ul>
                                </div>
                                <p><a href="/vaarkaart">Bekijk onze interactieve vaarkaart voor gedetailleerde routes →</a></p>
                            </div>
                            <div class="destination-split__img-wrap">
                                <?php echo responsiveImage(
                                    'frontend/Images/Giethoorn/giethoorn3.jpeg',
                                    'Vaarroute door de Weerribben naar Giethoorn met natuur en waterwegen',
                                    '(max-width: 768px) 100vw, 50vw'
                                ); ?>
                            </div>
                        </div>
                    </div>

                    <!-- Bezienswaardigheden -->
                    <div class="section-title" style="margin-top: 2rem;">
                        <h2>Bezienswaardigheden en activiteiten in Giethoorn</h2>
                    </div>

                    <div class="facilities-grid facilities-grid--4cols">
                        <div class="facility-card">
                            <?php echo responsiveImage(
                                'frontend/Images/Giethoorn/giethoorn2.png',
                                'Boot varen door de grachten van Giethoorn',
                                '(max-width: 768px) 100vw, (max-width: 1024px) 50vw, 25vw'
                            ); ?>
                            <h3>🚣 Varen door Giethoorn</h3>
                            <p>
                                De beste manier om Giethoorn te ontdekken is vanaf het water.
                                Huur een <a href="/electrosloop-8#booking">electrosloep</a>, <a href="/canoe-3#booking">kano</a> of <a href="/sailpunter-3-4#booking">zeilpunter</a> bij Nijenhuis en vaar door de smalle grachten
                                langs de prachtige rietgedekte huizen.
                            </p>
                        </div>
                        <div class="facility-card">
                            <div class="facility-icon">🏛️</div>
                            <h3>Museum 't Olde Maat Uus</h3>
                            <p>
                                Neem een kijkje in het leven van Giethoorn uit de 19e en vroege 20e eeuw in dit
                                authentieke boerenwoningmuseum.
                            </p>
                        </div>
                        <div class="facility-card">
                            <?php echo responsiveImage(
                                'frontend/Images/Giethoorn/giethoorn5.jpeg',
                                'Nationaal Park Weerribben-Wieden natuurgebied',
                                '(max-width: 768px) 100vw, (max-width: 1024px) 50vw, 25vw'
                            ); ?>
                            <h3>🌿 Nationaal park Weerribben-Wieden</h3>
                            <p>
                                Verken het grootste laagveenmoeras van Noordwest-Europa met zijn vele vaarroutes en
                                vogelrijke gebieden. <a href="/vaarkaart">Bekijk de vaarkaart</a>.
                            </p>
                        </div>
                        <div class="facility-card">
                            <div class="facility-icon">🛍️</div>
                            <h3>Wandelen &amp; winkelen</h3>
                            <p>
                                In het centrum van Giethoorn vind je gezellige winkeltjes, restaurants en cafés
                                met uitzicht op het water.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ============================================================
             7. BELT-SCHUTSLOOT ALTERNATIEF
             ============================================================ -->
        <section class="content-section comparison-section">
            <div class="container">
                <div class="destination-content">
                    <div class="comparison-highlight">
                        <span>💎</span>
                        <h2>Ontdek Belt-schutsloot: het geheime alternatief</h2>
                    </div>

                    <div class="content-prose">
                        <p>
                            Op zoek naar een <strong>rustigere en minder toeristische</strong> ervaring dan Giethoorn?
                            Ontdek <strong>Belt-schutsloot</strong>, een charmant dorpje op slechts enkele kilometers van
                            Giethoorn. Belt-schutsloot heeft dezelfde idyllische sfeer met grachten en bruggetjes,
                            maar zonder de drukte.
                        </p>
                    </div>

                    <div class="comparison-box">
                        <h3>Waarom Belt-schutsloot?</h3>
                        <ul>
                            <li>✅ <strong>Minder toeristisch:</strong> Rustiger en authentieker dan Giethoorn</li>
                            <li>✅ <strong>Dezelfde charme:</strong> Mooie grachten, bruggetjes en rietgedekte huizen</li>
                            <li>✅ <strong>Kortere afstand:</strong> Sneller bereikbaar vanaf Wanneperveen</li>
                            <li>✅ <strong>Unieke sfeer:</strong> Ontdek een verborgen parel in de Weerribben</li>
                        </ul>
                    </div>

                    <a href="/belt-schutsloot" class="btn-accent">Lees meer over Belt-schutsloot →</a>
                </div>
            </div>
        </section>

        <!-- ============================================================
             8. VEELGESTELDE VRAGEN
             ============================================================ -->
        <section class="content-section faq-section">
            <div class="container" style="max-width: 800px;">
                <h2 style="text-align: center; margin-bottom: 2rem;">Veelgestelde vragen over bootverhuur Giethoorn</h2>

                <script type="application/ld+json">
                {
                    "@context": "https://schema.org",
                    "@type": "FAQPage",
                    "mainEntity": [
                        {
                            "@type": "Question",
                            "name": "Wat zijn de verhuuropties in Giethoorn?",
                            "acceptedAnswer": {
                                "@type": "Answer",
                                "text": "Bij Nijenhuis Botenverhuur kun je kiezen uit fluisterboten (electrosloepen), traditionele zeilpunters, kano's, kajaks en SUP boards. Elk boottype biedt een eigen beleving van de grachten van Giethoorn. Bekijk ons volledige aanbod op de botenverhuur pagina."
                            }
                        },
                        {
                            "@type": "Question",
                            "name": "Wanneer is de beste tijd voor een boottocht naar Giethoorn?",
                            "acceptedAnswer": {
                                "@type": "Answer",
                                "text": "Het voorjaar en de zomer zijn populair vanwege het milde weer en de bloeiende natuur. Giethoorn kan in het hoogseizoen (juli en augustus) druk zijn. Voor een rustigere ervaring kun je 's ochtends vroeg vertrekken of Belt-schutsloot bezoeken, een vergelijkbaar maar minder toeristisch dorp."
                            }
                        },
                        {
                            "@type": "Question",
                            "name": "Welke veiligheidsmaatregelen worden genomen bij bootverhuur?",
                            "acceptedAnswer": {
                                "@type": "Answer",
                                "text": "Bij Nijenhuis Botenverhuur ontvang je bij elke huur een reddingsvest en veiligheidsinstructies. In Giethoorn geldt een maximumsnelheid van 6 km/u. We adviseren altijd om de aangegeven vaarroutes te volgen en respectvol te zijn naar andere boten en bewoners."
                            }
                        },
                        {
                            "@type": "Question",
                            "name": "Hoe kan ik naar Giethoorn varen vanuit Wanneperveen?",
                            "acceptedAnswer": {
                                "@type": "Answer",
                                "text": "Vanaf Nijenhuis Botenverhuur in Wanneperveen is het ongeveer 10 kilometer varen naar Giethoorn. De hele route (heen en terug) duurt ongeveer 1,5 tot 2 uur en gaat door het prachtige Nationaal Park Weerribben-Wieden."
                            }
                        },
                        {
                            "@type": "Question",
                            "name": "Waarom wordt Giethoorn het Venetië van het Noorden genoemd?",
                            "acceptedAnswer": {
                                "@type": "Answer",
                                "text": "Giethoorn wordt het Venetië van het Noorden genoemd omdat het dorp net als Venetië vele grachten, meer dan 180 bruggetjes en huizen die direct aan het water liggen heeft. Boten zijn de belangrijkste manier om je door het dorp te verplaatsen."
                            }
                        },
                        {
                            "@type": "Question",
                            "name": "Is Giethoorn druk in het hoogseizoen?",
                            "acceptedAnswer": {
                                "@type": "Answer",
                                "text": "Ja, Giethoorn kan vooral in het hoogseizoen (juli en augustus) en in het weekend druk zijn. Voor een rustigere ervaring kun je 's ochtends vroeg naar Giethoorn varen, of Belt-schutsloot bezoeken, een vergelijkbaar maar minder toeristisch dorp in de buurt."
                            }
                        },
                        {
                            "@type": "Question",
                            "name": "Kan ik een fluisterbootje huren om naar Giethoorn te varen?",
                            "acceptedAnswer": {
                                "@type": "Answer",
                                "text": "Ja, bij Nijenhuis Botenverhuur kun je fluisterbootjes (electrosloepen) huren om naar Giethoorn te varen. Onze fluisterboten zijn elektrische sloepen die fluisterstil varen en zijn geschikt voor 8 tot 12 personen."
                            }
                        },
                        {
                            "@type": "Question",
                            "name": "Kan ik SUP-pen naar Giethoorn?",
                            "acceptedAnswer": {
                                "@type": "Answer",
                                "text": "Ja, je kunt een SUP board huren en stand-up paddelen naar Giethoorn. De route gaat door het prachtige Nationaal Park Weerribben-Wieden. SUP in Giethoorn is een actieve en unieke manier om het gebied te verkennen."
                            }
                        }
                    ]
                }
                </script>

                <div class="faq-accordion">
                    <details class="faq-item">
                        <summary>
                            <span class="faq-question">Wat zijn de verhuuropties in Giethoorn?</span>
                            <span class="faq-icon"></span>
                        </summary>
                        <div class="faq-answer">
                            <p>Bij Nijenhuis Botenverhuur kun je kiezen uit fluisterboten (electrosloepen), traditionele zeilpunters, kano's, kajaks en SUP boards. Elk boottype biedt een eigen beleving van de grachten van Giethoorn. Kies bijvoorbeeld een <a href="/electrosloop-8#booking">electrosloep voor 8 personen</a>, een <a href="/sailpunter-3-4#booking">zeilpunter</a> of een <a href="/canoe-3#booking">Canadese kano</a>.</p>
                        </div>
                    </details>

                    <details class="faq-item">
                        <summary>
                            <span class="faq-question">Wanneer is de beste tijd voor een boottocht naar Giethoorn?</span>
                            <span class="faq-icon"></span>
                        </summary>
                        <div class="faq-answer">
                            <p>Het voorjaar en de zomer zijn populair vanwege het milde weer en de bloeiende natuur. Giethoorn kan in het hoogseizoen (juli en augustus) druk zijn. Voor een rustigere ervaring kun je 's ochtends vroeg vertrekken of <a href="/belt-schutsloot">Belt-schutsloot</a> bezoeken, een vergelijkbaar maar minder toeristisch dorp.</p>
                        </div>
                    </details>

                    <details class="faq-item">
                        <summary>
                            <span class="faq-question">Welke veiligheidsmaatregelen worden genomen bij bootverhuur?</span>
                            <span class="faq-icon"></span>
                        </summary>
                        <div class="faq-answer">
                            <p>Bij Nijenhuis Botenverhuur ontvang je bij elke huur een reddingsvest en veiligheidsinstructies. In Giethoorn geldt een maximumsnelheid van 6 km/u. We adviseren altijd om de aangegeven vaarroutes te volgen en respectvol te zijn naar andere boten en bewoners.</p>
                        </div>
                    </details>

                    <details class="faq-item">
                        <summary>
                            <span class="faq-question">Hoe kan ik naar Giethoorn varen vanuit Wanneperveen?</span>
                            <span class="faq-icon"></span>
                        </summary>
                        <div class="faq-answer">
                            <p>Vanaf Nijenhuis Botenverhuur in Wanneperveen is het ongeveer 10 kilometer varen naar Giethoorn. De hele route (heen en terug) duurt ongeveer 1,5 tot 2 uur en gaat door het prachtige Nationaal Park Weerribben-Wieden. Je kunt verschillende boten huren, zoals electrosloepen, zeilpunters of kano's.</p>
                        </div>
                    </details>

                    <details class="faq-item">
                        <summary>
                            <span class="faq-question">Waarom wordt Giethoorn het Venetië van het Noorden genoemd?</span>
                            <span class="faq-icon"></span>
                        </summary>
                        <div class="faq-answer">
                            <p>Giethoorn wordt het Venetië van het Noorden genoemd omdat het dorp net als Venetië vele grachten, meer dan 180 bruggetjes en huizen die direct aan het water liggen heeft. Boten zijn de belangrijkste manier om je door het dorp te verplaatsen.</p>
                        </div>
                    </details>

                    <details class="faq-item">
                        <summary>
                            <span class="faq-question">Is Giethoorn druk in het hoogseizoen?</span>
                            <span class="faq-icon"></span>
                        </summary>
                        <div class="faq-answer">
                            <p>Ja, Giethoorn kan vooral in het hoogseizoen (juli en augustus) en in het weekend druk zijn. Voor een rustigere ervaring kun je 's ochtends vroeg naar Giethoorn varen, of <a href="/belt-schutsloot">Belt-schutsloot</a> bezoeken, een vergelijkbaar maar minder toeristisch dorp in de buurt.</p>
                        </div>
                    </details>

                    <details class="faq-item">
                        <summary>
                            <span class="faq-question">Kan ik een fluisterbootje huren om naar Giethoorn te varen?</span>
                            <span class="faq-icon"></span>
                        </summary>
                        <div class="faq-answer">
                            <p>Ja, bij Nijenhuis Botenverhuur kun je fluisterbootjes (electrosloepen) huren om naar Giethoorn te varen. Onze fluisterboten zijn elektrische sloepen die fluisterstil varen. Kies de <a href="/electrosloop-8#booking">electrosloep 8 personen</a> of de <a href="/electrosloop-10#booking">electrosloep 10 personen</a>.</p>
                        </div>
                    </details>

                    <details class="faq-item">
                        <summary>
                            <span class="faq-question">Kan ik SUP-pen naar Giethoorn?</span>
                            <span class="faq-icon"></span>
                        </summary>
                        <div class="faq-answer">
                            <p>Ja, je kunt een SUP board huren en stand-up paddelen naar Giethoorn. De route gaat door het prachtige Nationaal Park Weerribben-Wieden. SUP in Giethoorn is een actieve en unieke manier om het gebied te verkennen.</p>
                        </div>
                    </details>
                </div>
            </div>
        </section>

        <!-- ============================================================
             9. FINAL CTA
             ============================================================ -->
        <section class="cta-section" style="background: var(--secondary-color); color: white; padding: 4rem 0;">
            <div class="container">
                <div class="cta-content">
                    <h2 style="color: white; margin-bottom: 1rem;">Klaar om Giethoorn te ontdekken?</h2>
                    <p class="content-prose" style="margin-bottom: 2rem; opacity: 0.95;">
                        Reserveer nu je boot en vaar naar het betoverende Giethoorn. Of ontdek het rustigere Belt-schutsloot
                        voor een unieke en authentieke ervaring in de Weerribben.
                    </p>
                    <div class="cta-buttons">
                        <a href="/booking" class="btn-cta-primary">Direct boeken</a>
                        <a href="/#boten" class="btn-cta-outline">Bekijk boten</a>
                    </div>
                </div>
            </div>
        </section>

    </main>

    <?php include __DIR__ . '/../components/footer.php'; ?>
</body>
</html>
