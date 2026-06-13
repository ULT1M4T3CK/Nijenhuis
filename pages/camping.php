<?php
/**
 * Camping Page - Nijenhuis Botenverhuur
 * Seasonal camping in the Weerribben nature reserve
 */

// Include configuration
require_once __DIR__ . '/../components/config.php';

// Set base path for this directory
$basePath = '..';

// Page-specific configuration (SEO optimized)
$pageTitle = 'Camping Weerribben bij Giethoorn';
$pageDescription = 'Seizoenscamping in Nationaal Park Weerribben-Wieden bij Giethoorn. Eigen aanlegplaats, water, elektriciteit en sanitair. Caravan mag jaarrond staan.';
$pageKeywords = 'camping weerribben, seizoenscamping giethoorn, camping wanneperveen, kamperen overijssel, camping aan het water';

// Header configuration
$headerTitle = 'Seizoenscamping';
$headerTitleI18n = 'camping_title';
$headerDescription = 'Kom helemaal tot rust tijdens het kamperen midden in het prachtige natuurgebied de Weerribben.';
$headerDescriptionI18n = 'camping_description';

// Breadcrumbs for this page
$breadcrumbs = [
    ['name' => 'Home', 'url' => '/'],
    ['name' => 'Camping', 'url' => '/camping']
];
?>
<!DOCTYPE html>
<html lang="nl">
<?php include __DIR__ . '/../components/head.php'; ?>
<body data-page="camping">
    <?php include __DIR__ . '/../components/topbar.php'; ?>
    <?php include __DIR__ . '/../components/nav.php'; ?>
    <?php include __DIR__ . '/../components/breadcrumb.php'; ?>
    <?php include __DIR__ . '/../components/page-header.php'; ?>

    <!-- Campground Schema.org Structured Data -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Campground",
        "@id": "https://nijenhuis-botenverhuur.com/pages/camping.php",
        "name": "Camping Nijenhuis - Seizoenscamping Weerribben",
        "alternateName": "Nijenhuis Seizoenscamping",
        "description": "Seizoenscamping in Nationaal Park Weerribben-Wieden bij Giethoorn. Eigen aanlegplaats, water, elektriciteit en sanitair. Caravan mag jaarrond blijven staan.",
        "url": "https://nijenhuis-botenverhuur.com/pages/camping.php",
        "telephone": "+31522281528",
        "address": {
            "@type": "PostalAddress",
            "streetAddress": "Veneweg 199",
            "addressLocality": "Wanneperveen",
            "postalCode": "7946 LP",
            "addressRegion": "Overijssel",
            "addressCountry": "NL"
        },
        "geo": {
            "@type": "GeoCoordinates",
            "latitude": 52.6972,
            "longitude": 6.0780
        },
        "openingHoursSpecification": {
            "@type": "OpeningHoursSpecification",
            "dayOfWeek": ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"],
            "opens": "09:00",
            "closes": "18:00",
            "validFrom": "<?php echo date('Y'); ?>-04-01",
            "validThrough": "<?php echo date('Y'); ?>-10-31"
        },
        "amenityFeature": [
            {"@type": "LocationFeatureSpecification", "name": "Eigen aanlegplaats", "value": true},
            {"@type": "LocationFeatureSpecification", "name": "Elektriciteit", "value": true},
            {"@type": "LocationFeatureSpecification", "name": "Wateraansluiting", "value": true},
            {"@type": "LocationFeatureSpecification", "name": "Sanitairgebouw", "value": true},
            {"@type": "LocationFeatureSpecification", "name": "Jaarrond staanplaats", "value": true}
        ],
        "containedInPlace": {
            "@type": "NaturalFeature",
            "name": "Nationaal Park Weerribben-Wieden"
        },
        "petsAllowed": true,
        "checkinTime": "14:00",
        "checkoutTime": "11:00",
        "priceRange": "€€"
    }
    </script>

    <!-- Main Content -->
    <main>
        <!-- Camping Overview -->
        <section class="content-section">
            <div class="container">
                <div class="section-title">
                    <h2 data-i18n="camping_overview_title">Onze camping</h2>
                    <p data-i18n="camping_overview_description">Een rustige en sfeervolle camping midden in de natuur</p>
                </div>

                <p data-i18n="camping_bluf_summary" style="font-size: 1.15rem; line-height: 1.7; color: var(--text-secondary); max-width: 800px; margin: 0 auto 2rem;">Nijenhuis Botenverhuur biedt seizoenscamping in Nationaal Park Weerribben-Wieden bij Giethoorn. Elke staanplaats heeft een eigen aanlegplaats, water, elektriciteit en sanitair. Caravan mag jaarrond staan. Seizoen: april tot oktober.</p>

                <div class="camping-info-grid">
                    <div class="camping-image">
                        <?php echo responsiveImage(
                            'frontend/Images/banner-img.jpg',
                            'Seizoenscamping Nijenhuis aan het water in Nationaal Park Weerribben-Wieden bij Giethoorn',
                            '(max-width: 768px) 100vw, 50vw',
                            ['data-i18n-alt' => 'alt_camping_banner']
                        ); ?>
                    </div>
                    <div class="camping-details">
                        <h3 data-i18n="camping_overview_seasonal_title">Al meer dan 50 jaar een familiebegrip</h3>
                        <p data-i18n="camping_overview_seasonal_description">
                            Al ruim een halve eeuw is Camping Nijenhuis een verborgen parel in het hart van de Weerribben. Wat meer dan vijftig jaar geleden begon als een passie voor gastvrijheid en natuur, is uitgegroeid tot een unieke familiecamping waar generaties gasten zich thuis voelen. Nog steeds in familiehanden koesteren wij de persoonlijke sfeer en de rust die onze camping zo bijzonder maken.
                            <br><br>
                            Onze seizoenscamping is kleinschalig opgezet, waardoor je geniet van maximale privacy en ruimte. Het is de perfecte plek om te ontsnappen aan de dagelijkse drukte. Uniek aan onze camping is dat elke staanplaats beschikt over een eigen aanlegplaats, zodat je direct vanaf je caravan het water op kunt om de prachtige waterwegen van Giethoorn en de Weerribben te verkennen.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <section class="content-section bg-secondary">
            <div class="container">
                <div class="section-title">
                    <h2 data-i18n="camping_area_title">Omgeving & recreatie</h2>
                </div>
                <div class="content-prose">
                    <p data-i18n="camping_area_p1">Camping Nijenhuis ligt midden in Nationaal Park Weerribben-Wieden, een van de mooiste natuurgebieden van Nederland. Vanaf je staanplaats vaar je direct het water op – geen gedoe met trailers of slepen. De waterwegen verbinden je met Giethoorn, Wanneperveen, Belt-Schutsloot en talloze rustige plekjes waar je alleen de vogels hoort.</p>
                    <p data-i18n="camping_area_p2">Naast varen kun je fietsen, wandelen, vissen en zwemmen. Er zijn uitgestippelde routes voor elke afstand. In de omgeving vind je restaurants, musea en bootverhuur. Veel gasten combineren hun verblijf met een <a href="/electrosloop-8#booking">electrosloep (8 pers)</a>, <a href="/electrosloop-10#booking">electrosloep (10 pers)</a> of <a href="/canoe-3#booking">Canadese kano</a> van Nijenhuis Botenverhuur – bekijk ook onze <a href="/sailpunter-3-4#booking">zeilpunter</a> en <a href="/sailboat-4-5#booking">zeilboot</a> voor een authentieke vaarbeleving.</p>
                </div>
            </div>
        </section>

        <section class="content-section">
            <div class="container">
                <div class="section-title">
                    <h2 data-i18n="camping_tips_title">Praktische informatie seizoenscamping</h2>
                </div>
                <div class="content-prose">
                    <p data-i18n="camping_tips_p1">De camping is open van 1 april tot 31 oktober. Caravans mogen het hele jaar op de plaats blijven staan, waardoor je in het voor- en naseizoen zonder stress kunt komen en gaan. Elke staanplaats heeft water, elektriciteit (met eigen meter), rioolaansluiting en een eigen aanlegplaats. Sanitair met douches en toiletten is aanwezig, evenals een wasmachine en droger.</p>
                    <p data-i18n="camping_tips_p2">Vanwege de kleinschaligheid en de gewilde locatie raden we aan tijdig te reserveren. Bel ons voor beschikbaarheid en prijzen. Honden zijn welkom, mits aangelijnd op de camping. De sfeer is rustig en geschikt voor gezinnen en natuurliefhebbers die genieten van eenvoud en direct contact met het water.</p>
                </div>
            </div>
        </section>

        <!-- Facilities -->
        <section class="content-section">
            <div class="container">
                <div class="section-title">
                    <h2 data-i18n="facilities_title">Faciliteiten</h2>
                    <p data-i18n="facilities_description">Alle voorzieningen voor seizoensplaatsen</p>
                </div>

                <div class="facilities-grid">
                    <div class="facility-card">
                        <div class="facility-icon">🚿</div>
                        <h3 data-i18n="facilities_sanitary_title">Sanitair</h3>
                        <p data-i18n="facilities_sanitary_description">Douches en toiletten beschikbaar voor alle gasten</p>
                    </div>
                    <div class="facility-card">
                        <div class="facility-icon">⚡</div>
                        <h3 data-i18n="facilities_electricity_title">Elektriciteit</h3>
                        <p data-i18n="facilities_electricity_description">Elektriciteitsmeter op elke plaats voor eigen verbruik</p>
                    </div>
                    <div class="facility-card">
                        <div class="facility-icon">🚰</div>
                        <h3 data-i18n="facilities_water_title">Water</h3>
                        <p data-i18n="facilities_water_description">Wateraansluiting beschikbaar op elke plaats</p>
                    </div>
                    <div class="facility-card">
                        <div class="facility-icon">📡</div>
                        <h3 data-i18n="facilities_antenna_title">Centrale antenne</h3>
                        <p data-i18n="facilities_antenna_description">Centrale antenne voor TV-ontvangst</p>
                    </div>
                    <div class="facility-card">
                        <div class="facility-icon">🚢</div>
                        <h3 data-i18n="facilities_mooring_title">Eigen aanlegplaats</h3>
                        <p data-i18n="facilities_mooring_description">Elke plaats heeft een eigen aanlegplaats</p>
                    </div>
                    <div class="facility-card">
                        <div class="facility-icon">🏠</div>
                        <h3 data-i18n="facilities_sewerage_title">Riool</h3>
                        <p data-i18n="facilities_sewerage_description">Rioolafvoer beschikbaar op alle plaatsen</p>
                    </div>
                </div>
                
                <div class="cta-section" style="text-align: center; margin-top: 40px; padding: 20px; background-color: var(--primary-color); color: white; border-radius: var(--radius-lg); box-shadow: var(--shadow-md);">
                    <p style="margin-bottom: 15px;"><strong data-i18n="camping_overview_cta_strong" style="font-size: 1.2rem; color: white;">Interesse in een seizoensplaats?</strong></p>
                    <p style="margin-bottom: 20px;" data-i18n="camping_overview_cta_text">Neem contact met ons op voor de mogelijkheden en beschikbaarheid.</p>
                    <a href="tel:<?php echo SITE_PHONE_LINK; ?>" class="btn" style="background-color: white; color: var(--primary-color); border: none; font-weight: 700;" data-i18n="camping_overview_cta_button">Bel direct: <?php echo SITE_PHONE; ?></a>
                </div>
            </div>
        </section>
    </main>

    <?php include __DIR__ . '/../components/footer.php'; ?>

    <script>
        // Page-specific: Season status functionality
        function updateSeasonStatus() {
            const now = new Date();
            const currentMonth = now.getMonth() + 1;
            const currentDay = now.getDate();
            
            const seasonStart = { month: 4, day: 1 };
            const seasonEnd = { month: 10, day: 31 };
            
            let isInSeason = false;
            
            if (currentMonth > seasonStart.month && currentMonth < seasonEnd.month) {
                isInSeason = true;
            } else if (currentMonth === seasonStart.month && currentDay >= seasonStart.day) {
                isInSeason = true;
            } else if (currentMonth === seasonEnd.month && currentDay <= seasonEnd.day) {
                isInSeason = true;
            }
            
            const statusElement = document.querySelector('.season-status-text');
            if (statusElement) {
                    statusElement.textContent = isInSeason
                        ? window.getTranslation('season_status_open')
                        : window.getTranslation('season_status_closed_until');
                statusElement.style.color = isInSeason ? '#4caf50' : '#f44336';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            updateSeasonStatus();
        });
    </script>
</body>
</html>

