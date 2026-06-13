<?php
/**
 * Vakantiehuis Page - Nijenhuis Botenverhuur
 */
require_once __DIR__ . '/../components/config.php';
$basePath = getBasePath();
$pageTitle = 'Vakantiehuis Belterwiede bij Giethoorn';
$pageDescription = 'Ruim vakantiehuis aan het Belterwiede meer bij Giethoorn. 5 slaapkamers, 2 badkamers, volledig uitgerust. Het hele jaar geopend, direct aan het water.';
$pageKeywords = 'vakantiehuis giethoorn, vakantiewoning weerribben, accommodatie wanneperveen, huis huren belterwiede';
$headerTitle = 'Vakantiehuis';
$headerTitleI18n = 'house_header_h1';
$headerDescription = 'Ervaar een heerlijk verblijf in ons vakantiehuis, midden in het prachtige natuurgebied de Weerribben.';
$headerDescriptionI18n = 'house_header_p1';

// Breadcrumbs for this page
$breadcrumbs = [
    ['name' => 'Home', 'url' => '/'],
    ['name' => 'Vakantiehuis', 'url' => '/vakantiehuis']
];
$additionalStyles = ['/frontend/css/pages/destination-pages.css?v=2', '/frontend/css/pages/boats.css'];
// Slideshow images (raw paths for responsiveImage helper)
$slideshowPaths = [];
for ($i = 1; $i <= 17; $i++) {
    if ($i != 11) {
        $slideshowPaths[] = "frontend/Images/Vakantiehuis/{$i}.jpg";
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<?php include __DIR__ . '/../components/head.php'; ?>
<body data-page="vakantiehuis">
    <?php include __DIR__ . '/../components/topbar.php'; ?>
    <?php include __DIR__ . '/../components/nav.php'; ?>
    <?php include __DIR__ . '/../components/breadcrumb.php'; ?>
    <?php include __DIR__ . '/../components/page-header.php'; ?>

    <!-- VacationRental Schema.org Structured Data -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "VacationRental",
        "@id": "https://nijenhuis-botenverhuur.com/pages/vakantiehuis.php",
        "name": "Vakantiehuis Belterwiede",
        "alternateName": "Vakantiewoning Nijenhuis bij Giethoorn",
        "description": "Ruim vakantiehuis aan het Belterwiede meer bij Giethoorn. 5 slaapkamers, 2 badkamers, volledig uitgerust. Direct aan het water, ideaal voor families.",
        "url": "https://nijenhuis-botenverhuur.com/pages/vakantiehuis.php",
        "image": "https://nijenhuis-botenverhuur.com/frontend/Images/Vakantiehuis/1.jpg",
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
        "numberOfRooms": 7,
        "amenityFeature": [
            {"@type": "LocationFeatureSpecification", "name": "Volledig uitgeruste keuken", "value": true},
            {"@type": "LocationFeatureSpecification", "name": "WiFi", "value": true},
            {"@type": "LocationFeatureSpecification", "name": "Parking", "value": true},
            {"@type": "LocationFeatureSpecification", "name": "Tuin", "value": true},
            {"@type": "LocationFeatureSpecification", "name": "Direct aan het water", "value": true},
            {"@type": "LocationFeatureSpecification", "name": "Eigen aanlegsteiger", "value": true}
        ],
        "containedInPlace": {
            "@type": "NaturalFeature",
            "name": "Nationaal Park Weerribben-Wieden"
        },
        "petsAllowed": true,
        "checkinTime": "15:00",
        "checkoutTime": "10:00"
    }
    </script>

    <main>
        <section class="content-section">
            <div class="container">
                <div class="destination-content">
                    <div class="destination-intro">
                        <div class="section-title" style="margin-bottom: 0.75rem;">
                            <h2 data-i18n="house_overview_h2">Vakantiehuis Belterwiede bij Giethoorn</h2>
                        </div>
                        <div class="content-prose">
                            <p data-i18n="house_overview_p1"><strong>HET HELE JAAR GEOPEND</strong> – Ons vakantiehuis Belterwiede ligt nabij Giethoorn in het hart van de Weerribben.</p>
                        </div>
                    </div>
                </div>

                <div class="house-details">
                    <div class="house-image">
                        <div class="modern-slideshow">
                            <div class="slideshow-main">
                                <?php foreach ($slideshowPaths as $index => $imgPath): ?>
                                <?php
                                $slideAttrs = ['data-i18n-alt' => 'alt_house_interior', 'class' => 'slide' . ($index === 0 ? ' active' : '')];
                                if ($index === 0) {
                                    $slideAttrs['loading'] = 'eager';
                                    $slideAttrs['fetchpriority'] = 'high';
                                } else {
                                    $slideAttrs['loading'] = 'lazy';
                                }
                                echo responsiveImage(
                                    $imgPath,
                                    'Vakantiehuis Belterwiede interieur - vakantiewoning bij Giethoorn',
                                    '(max-width: 768px) 100vw, 60vw',
                                    $slideAttrs
                                );
                                ?>
                                <?php endforeach; ?>
                            </div>
                            <div class="slideshow-thumbnails">
                                <?php foreach ($slideshowPaths as $index => $imgPath): ?>
                                <div class="thumbnail<?php echo $index === 0 ? ' active' : ''; ?>" onclick="currentSlide(<?php echo $index + 1; ?>)">
                                    <img src="/<?php echo htmlspecialchars($imgPath); ?>" alt="Thumbnail <?php echo $index + 1; ?>" loading="lazy" decoding="async">
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <button class="modern-slideshow-btn prev" onclick="changeSlide(-1)">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M15 18l-6-6 6-6"/>
                                </svg>
                            </button>
                            <button class="modern-slideshow-btn next" onclick="changeSlide(1)">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M9 18l6-6-6-6"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="house-info">
                        <h3 data-i18n="house_overview_h3">Perfecte uitvalsbasis in de kop van Overijssel</h3>
                        <p data-i18n="house_overview_p2">Wil je een weekend, midweek, week of een hele vakantie doorbrengen in een prachtig natuur- of watersportgebied? Kom dan naar de Kop van Overijssel, waar je kunt genieten van varen, vissen, zwemmen, fietsen, wandelen en het bezoeken van andere dorpen in de omgeving. Het huis is direct gelegen aan het Belterwijde meer.</p>
                        
                        <h4 data-i18n="house_overview_h4">Indeling van het vakantiehuis</h4>
                        <p data-i18n="house_overview_p3"><strong>Beneden:</strong> <span>Je hebt 1 slaapkamer, een douche, toilet en wasmachine. Je kunt ontspannen in de ruime woonkamer met TV en radio. De kamer heeft een open keuken met diverse huishoudelijke apparaten (oven, magnetron, koelkast). Er is een ruime hal en het huis is volledig voorzien van centrale verwarming.</span></p>
                        
                        <p data-i18n="house_overview_p4"><strong>Boven:</strong> <span>Je hebt vier slaapkamers, waarvan er twee een wastafel hebben. Er is ook een douche en toilet op de tweede verdieping.</span></p>
                        
                        <h4 data-i18n="house_overview_h4">Bijzonderheden</h4>
                        <ul class="icon-list">
                            <li><span class="icon">👶</span> <span data-i18n="house_overview_li1">Kinderbedje, box en kinderstoel kunnen aangevraagd worden in het Waterpark Belterwiede.</span></li>
                            <li><span class="icon">🛏️</span> <span data-i18n="house_overview_li2">Kussens en dekbedden beschikbaar</span></li>
                            <li><span class="icon">🧺</span> <span data-i18n="house_overview_li3">Linnengoed graag zelf meenemen</span></li>
                            <li><span class="icon">🛒</span> <span data-i18n="house_overview_li4">Linnengoed ook te huur bij ons (graag vooraf melden)</span></li>
                            <li><span class="icon">📞</span> <span data-i18n="house_overview_li5">Voor verdere vragen kun je contact met ons opnemen</span></li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <section class="content-section bg-secondary">
            <div class="container">
                <div class="section-title">
                    <h2 data-i18n="house_why_title">Waarom kiezen voor dit vakantiehuis?</h2>
                </div>
                <div class="content-prose house-info">
                    <p data-i18n="house_why_p1">Het vakantiehuis Belterwiede biedt het beste van twee werelden: de rust van het Nationaal Park Weerribben-Wieden en de levendigheid van Giethoorn om de hoek. Omdat het huis direct aan het Belterwijde meer ligt, stap je letterlijk vanuit de tuin je boot of kano in. Ideaal voor gezinnen die willen varen, vissen of zwemmen zonder steeds te moeten in- en uitladen.</p>
                    <p data-i18n="house_why_p2">Het huis is het hele jaar geopend, waardoor je ook in de herfst en winter kunt genieten van wandelingen, fietstochten en de unieke sfeer van de Weerribben. In de zomer is het een perfecte uitvalsbasis voor dagtrips naar Giethoorn, Belt-Schutsloot of andere dorpen in de omgeving. Waterpark Belterwiede verzorgt de reserveringen en het beheer van het vakantiehuis.</p>
                </div>
            </div>
        </section>

        <section class="content-section">
            <div class="container">
                <div class="section-title">
                    <h2 data-i18n="house_surroundings_title">Omgeving & activiteiten</h2>
                </div>
                <div class="content-prose house-info">
                    <p data-i18n="house_surroundings_p1">Vanuit het vakantiehuis Belterwiede heb je direct toegang tot het uitgestrekte waternetwerk van de Weerribben. Varen, kanoën of vissen – het kan allemaal vanaf je eigen aanlegsteiger. Fiets- en wandelroutes lopen door het gebied en verbinden je met pittoreske dorpen als Giethoorn, Wanneperveen en Blokzijl.</p>
                    <p data-i18n="house_surroundings_p2">In de omgeving vind je restaurants, winkels en attracties. Giethoorn ligt op ongeveer 15 minuten rijden en staat bekend om zijn grachten en rietgedekte huizen. Voor gezinnen zijn er speeltuinen en strandjes aan het water. Het vakantiehuis is geschikt voor maximaal twaalf personen en biedt voldoende ruimte voor een ontspannen verblijf.</p>
                </div>
            </div>
        </section>

        <section class="content-section">
            <div class="container">
                <div class="section-title">
                    <h2 data-i18n="house_amenities_h1">Faciliteiten</h2>
                    <p data-i18n="house_amenities_p1">Alles voor een comfortabel verblijf</p>
                </div>

                <div class="facilities-grid">
                    <div class="facility-card">
                        <div class="facility-icon">🛏️</div>
                        <h3 data-i18n="house_amenities_h2">5 Slaapkamers</h3>
                        <p data-i18n="house_amenities_p2">1 slaapkamer beneden, 4 slaapkamers boven (2 met wastafel)</p>
                    </div>
                    <div class="facility-card">
                        <div class="facility-icon">🍳</div>
                        <h3 data-i18n="house_amenities_h3">Open keuken</h3>
                        <p data-i18n="house_amenities_p3">Oven, magnetron, koelkast en alle huishoudelijke apparaten</p>
                    </div>
                    <div class="facility-card">
                        <div class="facility-icon">🛁</div>
                        <h3 data-i18n="house_amenities_h4">2 Badkamers</h3>
                        <p data-i18n="house_amenities_p4">Douche en toilet op beide verdiepingen</p>
                    </div>
                    <div class="facility-card">
                        <div class="facility-icon">📺</div>
                        <h3 data-i18n="house_amenities_h5">Woonkamer</h3>
                        <p data-i18n="house_amenities_p5">Ruime woonkamer met TV en radio</p>
                    </div>
                    <div class="facility-card">
                        <div class="facility-icon">🧺</div>
                        <h3 data-i18n="house_amenities_h6">Wasmachine</h3>
                        <p data-i18n="house_amenities_p6">Wasmachine beschikbaar in het huis</p>
                    </div>
                    <div class="facility-card">
                        <div class="facility-icon">🔥</div>
                        <h3 data-i18n="house_amenities_h7">Centrale verwarming</h3>
                        <p data-i18n="house_amenities_p7">Volledig verwarmd voor comfort het hele jaar door</p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php include __DIR__ . '/../components/footer.php'; ?>

    <script>
        // Page-specific: Modern Slideshow functionality with smooth transitions
        let currentSlideIndex = 0;
        let isTransitioning = false;
        const slides = document.querySelectorAll('.slideshow-main .slide');
        const thumbnails = document.querySelectorAll('.thumbnail');
        const totalSlides = slides.length;

        // Initialize first slide
        if (slides.length > 0) {
            slides[0].classList.add('active');
            if (thumbnails[0]) thumbnails[0].classList.add('active');
        }

        function showSlide(index) {
            if (isTransitioning) return;
            isTransitioning = true;

            // Remove active class from all slides and thumbnails
            slides.forEach(slide => slide.classList.remove('active'));
            thumbnails.forEach(thumb => thumb.classList.remove('active'));

            // Add active class to new slide and thumbnail
            if (slides[index]) {
                slides[index].classList.add('active');
                if (thumbnails[index]) thumbnails[index].classList.add('active');
            }

            // Reset transition lock after animation completes
            setTimeout(() => {
                isTransitioning = false;
            }, 600);
        }

        function changeSlide(direction) {
            if (isTransitioning) return;
            currentSlideIndex += direction;
            if (currentSlideIndex >= totalSlides) currentSlideIndex = 0;
            else if (currentSlideIndex < 0) currentSlideIndex = totalSlides - 1;
            showSlide(currentSlideIndex);
        }

        function currentSlide(index) {
            if (isTransitioning) return;
            currentSlideIndex = index - 1;
            showSlide(currentSlideIndex);
        }

        // Auto-advance slideshow every 5 seconds
        let slideInterval = setInterval(() => {
            if (!isTransitioning) {
                changeSlide(1);
            }
        }, 5000);

        // Pause/resume on hover
        const slideshow = document.querySelector('.modern-slideshow');
        if (slideshow) {
            slideshow.addEventListener('mouseenter', () => {
                clearInterval(slideInterval);
            });
            slideshow.addEventListener('mouseleave', () => {
                slideInterval = setInterval(() => {
                    if (!isTransitioning) {
                        changeSlide(1);
                    }
                }, 5000);
            });
        }

        // Keyboard navigation support
        document.addEventListener('keydown', (e) => {
            if (slideshow && document.activeElement === document.body) {
                if (e.key === 'ArrowLeft') {
                    e.preventDefault();
                    changeSlide(-1);
                } else if (e.key === 'ArrowRight') {
                    e.preventDefault();
                    changeSlide(1);
                }
            }
        });
    </script>
</body>
</html>

