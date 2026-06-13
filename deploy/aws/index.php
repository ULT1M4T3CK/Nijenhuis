<?php
/**
 * Home Page - Nijenhuis Botenverhuur
 */
require_once __DIR__ . '/../components/config.php';
$basePath = getBasePath();
$pageTitle = 'Home';
$pageDescription = 'Botenverhuur in het prachtige natuurgebied Weerribben bij Giethoorn. Elektrische boten, kano\'s, camping en jachthaven diensten.';
$includeBoatData = true;

// SEO Fix: Set canonical URL to base URL (without query parameters) to prevent duplicate content
// The boat parameter is only used client-side by JavaScript for UX, but all URLs should point to the base URL for SEO
// Always use HTTPS for canonical URL (site is now HTTPS-only)
$canonicalUrl = 'https://' . $_SERVER['HTTP_HOST'] . '/';
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Botenverhuur Weerribben | Camping & Jachthaven Giethoorn</title>
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="<?php echo $pageDescription; ?>">
    <meta name="keywords" content="botenverhuur, Giethoorn, Wanneperveen, Weerribben, elektrische boten, camping, jachthaven, kano, kajak">
    <meta name="author" content="Nijenhuis Boat Rental">
    
    <!-- Canonical URL - Points to base URL to prevent duplicate content issues -->
    <link rel="canonical" href="<?php echo htmlspecialchars($canonicalUrl); ?>">
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="<?php echo SITE_NAME; ?> - Giethoorn & Wanneperveen">
    <meta property="og:description" content="Ervaar de schoonheid van het natuurgebied Weerribben met onze botenverhuur.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo htmlspecialchars($canonicalUrl); ?>">
    <meta property="og:image" content="https://nijenhuis-botenverhuur.com<?php echo assetPath('frontend/Images/banner-img.jpg'); ?>">
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="<?php echo assetPath('frontend/Images/logo-white.svg'); ?>">
    <link rel="apple-touch-icon" href="<?php echo assetPath('frontend/Images/logo-white.svg'); ?>">
    
    <!-- Preload Critical Resources -->
    <link rel="preload" href="<?php echo assetPath('frontend/Images/banner-img.jpg'); ?>" as="image" fetchpriority="high">
    <link rel="preload" href="<?php echo assetPath('frontend/css/styles.css'); ?>" as="style">
    <link rel="stylesheet" href="<?php echo assetPath('frontend/css/styles.css'); ?>">
    <link rel="stylesheet" href="<?php echo assetPath('frontend/css/pages/home.css'); ?>">
    <link rel="stylesheet" href="<?php echo assetPath('js/booking-system.css'); ?>">
    <link rel="stylesheet" href="<?php echo assetPath('frontend/css/pages/boats.css'); ?>">

    <!-- Schema.org Structured Data - LocalBusiness -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": ["LocalBusiness", "TouristAttraction"],
        "@id": "https://nijenhuis-botenverhuur.com/#organization",
        "name": "<?php echo SITE_NAME; ?>",
        "alternateName": "Nijenhuis Bootverhuur Wanneperveen",
        "description": "Bootjes huren in Nationaal Park Weerribben-Wieden. Electrosloepen, zeilboten, kano's, kajaks en SUP boards verhuur nabij Giethoorn. Inclusief seizoenscamping en jachthaven.",
        "url": "https://nijenhuis-botenverhuur.com",
        "logo": "https://nijenhuis-botenverhuur.com/frontend/Images/logo-white.svg",
        "image": "https://nijenhuis-botenverhuur.com/frontend/Images/banner-img.jpg",
        "telephone": "<?php echo SITE_PHONE_LINK; ?>",
        "address": {
            "@type": "PostalAddress",
            "streetAddress": "<?php echo SITE_ADDRESS; ?>",
            "addressLocality": "Wanneperveen",
            "postalCode": "7946 LP",
            "addressRegion": "Overijssel",
            "addressCountry": "NL"
        },
        "geo": {
            "@type": "GeoCoordinates",
            "latitude": 52.697269,
            "longitude": 6.077958
        },
        "openingHoursSpecification": {
            "@type": "OpeningHoursSpecification",
            "dayOfWeek": ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"],
            "opens": "09:00",
            "closes": "18:00",
            "validFrom": "<?php echo date('Y'); ?>-04-01",
            "validThrough": "<?php echo date('Y'); ?>-10-31"
        },
        "priceRange": "€€",
        "paymentAccepted": ["Cash", "Debit Card", "Credit Card"],
        "currenciesAccepted": "EUR",
        "areaServed": {
            "@type": "GeoCircle",
            "geoMidpoint": {
                "@type": "GeoCoordinates",
                "latitude": 52.697269,
                "longitude": 6.077958
            },
            "geoRadius": "25000"
        },
        "hasOfferCatalog": {
            "@type": "OfferCatalog",
            "name": "Bootjes en Watervaartuigen",
            "itemListElement": [
                {
                    "@type": "Offer",
                    "itemOffered": {
                        "@type": "Service",
                        "name": "Electrosloep Verhuur",
                        "description": "Elektrische sloepen voor 5 tot 12 personen"
                    }
                },
                {
                    "@type": "Offer",
                    "itemOffered": {
                        "@type": "Service",
                        "name": "Zeilboot Verhuur",
                        "description": "Zeilboten en zeilpunters voor 3-5 personen"
                    }
                },
                {
                    "@type": "Offer",
                    "itemOffered": {
                        "@type": "Service",
                        "name": "Kano en Kajak Verhuur",
                        "description": "Canadese kano's en kajaks voor 1-3 personen"
                    }
                },
                {
                    "@type": "Offer",
                    "itemOffered": {
                        "@type": "Service",
                        "name": "SUP Board Verhuur",
                        "description": "Stand-up paddleboards voor 1 persoon"
                    }
                }
            ]
        },
        "containedInPlace": {
            "@type": "NaturalFeature",
            "name": "Nationaal Park Weerribben-Wieden",
            "description": "Het grootste aaneengesloten laagveenmoeras van Noordwest-Europa"
        },
        "knowsAbout": ["botenverhuur", "electrosloep", "Giethoorn", "Weerribben-Wieden", "watersport"],
        "slogan": "Huur een boot en ontdek de Weerribben"
    }
    </script>
    
    <!-- JavaScript -->
    <script>
        // Inject PHP Configuration into JS
        window.SiteConfig = {
            seasonStart: { 
                month: <?php echo SEASON_START_MONTH; ?>, 
                day: <?php echo SEASON_START_DAY; ?> 
            },
            seasonEnd: { 
                month: <?php echo SEASON_END_MONTH; ?>, 
                day: <?php echo SEASON_END_DAY; ?> 
            },
            serverPort: <?php echo isDevelopment() ? 8000 : 80; ?>
        };
    </script>
    <!-- Critical scripts only - other scripts loaded in footer with defer -->
    <script src="<?php echo assetPath('frontend/src/js/core/security.js'); ?>"></script>
    <script src="<?php echo assetPath('frontend/src/js/core/shared.js'); ?>"></script>

    <!-- Luigi's Box -->
    <script src="https://scripts.luigisbox.tech/LBX-1031658.js" defer></script>
</head>
<body>
    <?php include __DIR__ . '/../components/topbar.php'; ?>
    <?php include __DIR__ . '/../components/nav.php'; ?>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-layout">
                <!-- Booking Form -->
                <div class="booking-form-modern" id="booking">
                    <div class="form-header">
                        <p class="form-subtitle" data-i18n="hero_book_h2">Direct boeken</p>
                        <p data-i18n="hero_book_p">Reserveer eenvoudig je boot voor een dag op het water</p>
                    </div>
                    <form id="bookingForm" autocomplete="off">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="boatType" data-i18n="hero_book_boat_type">Boot type</label>
                                <span class="input-icon" aria-hidden="true">⛵</span>
                                <select id="boatType" name="boatType" required aria-required="true">
                                    <option value="" data-i18n="hero_book_boat_type_select">Selecteer een boot</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="dateRange" data-i18n="hero_book_date">Datum</label>
                                <span class="input-icon" aria-hidden="true">📅</span>
                                <input type="text" id="dateRange" name="dateRange" readonly required aria-required="true" placeholder="Selecteer datums">
                                <input type="hidden" id="date" name="date">
                                <input type="hidden" id="rentalEndDate" name="rentalEndDate">
                                <div id="calendarContainer" class="calendar-container" style="display: none;"></div>
                                <div id="priceDisplay" style="display: none; margin-top: 1rem; padding: 1rem; background: #f8fafc; border-radius: 0.75rem; text-align: center;">
                                    <div style="font-size: 0.9rem; color: var(--text-secondary); margin-bottom: 0.5rem;">Totaalprijs</div>
                                    <div id="priceAmount" style="font-size: 1.8rem; font-weight: 700; color: var(--primary-color);">€0.00</div>
                                </div>
                            </div>
                        </div>
                        <div class="form-row" id="engineOptionRow" style="display: none;">
                            <div class="form-group">
                                <label for="engineOption">Motor optie</label>
                                <span class="input-icon" aria-hidden="true">⚙️</span>
                                <select id="engineOption" name="engineOption">
                                    <option value="without">Zonder motor (€70)</option>
                                    <option value="with">Met motor (€85)</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row" id="quantityRow" style="display: none;">
                            <div class="form-group">
                                <label for="boatQuantity">Aantal boten</label>
                                <span class="input-icon" aria-hidden="true">🚤</span>
                                <select id="boatQuantity" name="boatQuantity">
                                    <option value="1">1</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn" data-i18n="hero_book_btn">Boek nu</button>
                            <button type="button" id="addToCartBtn" class="btn" data-i18n="btn_add_to_cart" style="text-align:center;">🛒 Toevoegen aan winkelwagen</button>
                        </div>
                    </form>
                    

                </div>
                
                <div class="hero-content">
                    <h1 data-i18n="hero_h1">Boot Huren bij Giethoorn & Weerribben</h1>
                    <p data-i18n="hero_h1_p">Huur electrosloepen, zeilboten, kano's en kajaks in Nationaal Park Weerribben-Wieden. Direct aan het water bij Wanneperveen, de perfecte uitvalsbasis voor families en vrienden.</p>
                    <div style="margin-top: 1.5rem; display: flex; gap: 1rem; flex-wrap: wrap; justify-content: center;">
                        <a href="/giethoorn" style="color: white; text-decoration: underline; font-weight: 500; opacity: 0.95;">
                            Ontdek Giethoorn →
                        </a>
                        <span style="color: white; opacity: 0.7;">|</span>
                        <a href="/belt-schutsloot" style="color: white; text-decoration: underline; font-weight: 500; opacity: 0.95;">
                            Ontdek Belt-schutsloot →
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <main>
        <!-- Introduction Section -->
        <section class="intro-section">
            <div class="container">
                <div class="intro-grid">
                    <div class="intro-content">
                        <h2 data-i18n="intro_h2">Even helemaal weg uit de dagelijkse routine</h2>
                        <p data-i18n="intro_h2_p">In de drukke wereld van vandaag heeft iedereen een moment nodig om los te koppelen. Laat files, stress en de dagelijkse sleur achter je.</p>
                        <p data-i18n="intro_h2_p2">Onze boten bieden de perfecte manier om het adembenemende natuurgebied Weerribben te verkennen.</p>
                    </div>
                    <div class="intro-features">
                        <h3 data-i18n="intro_h3">Waarom kiezen voor Nijenhuis?</h3>
                        <ul class="features-list">
                            <li data-i18n="intro_h3_li1">📍 Gelegen in het hart van het natuurgebied Weerribben</li>
                            <li data-i18n="intro_h3_li2">🚤 Breed assortiment boten voor alle voorkeuren</li>
                            <li data-i18n="intro_h3_li3">🌿 Milieuvriendelijke elektrische boten beschikbaar</li>
                            <li data-i18n="intro_h3_li4">👨‍👩‍👧‍👦 Perfect voor families en groepen</li>
                            <li data-i18n="intro_h3_li5">💰 Concurrentiële prijzen voor alle budgetten</li>
                            <li data-i18n="intro_h3_li6">📞 Persoonlijke service en ondersteuning</li>
                        </ul>
                        <div class="cta-box">
                            <p data-i18n="intro_cta_p"><strong>Voor meer informatie, bel <?php echo SITE_PHONE; ?></strong></p>
                            <p class="payment-note" data-i18n="intro_cta_p2">Contant en pin betalingen geaccepteerd</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Boat Fleet Section -->
        <section class="boat-fleet-section" style="padding: 4rem 0; background: #f8fafc;">
            <div class="container">
                <div class="section-title">
                    <h2 data-i18n="fleet_h2">Onze Boten</h2>
                    <p data-i18n="fleet_p">Kies uit ons ruime aanbod van elektrische sloepen, zeilboten en kano's</p>
                </div>
                
                <!-- Note about hourly rental and direct booking -->
                <div style="max-width: 800px; margin: 0 auto 2rem; padding: 1rem 1.5rem; background: #fff3cd; border-left: 4px solid #ffc107; border-radius: 8px; text-align: center;">
                    <p style="margin: 0; color: #856404; font-size: 0.95rem; line-height: 1.6;" data-i18n="fleet_hourly_note">
                        ℹ️ Let op: Voor alle boten is het ook mogelijk om per uur te huren in plaats van per dag. 
                        Uurverhuur kan alleen direct ter plaatse bij de bootverhuur worden geboekt, niet online of telefonisch. 
                        Kom langs bij onze verhuurlocatie voor beschikbaarheid en directe boeking.
                    </p>
                </div>
                
                <!-- Boats Grid - Populated by JS -->
                <div id="boatFleetGrid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;">
                    <!-- Loading state -->
                    <div style="grid-column: 1/-1; text-align: center; padding: 2rem;">
                        <p>Boten laden...</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Services Section -->
        <section class="services-section">
            <div class="container">
                <h2 data-i18n="services_h2">Onze diensten</h2>
                <div class="services-grid">
                    <div class="service-card">
                        <div class="service-icon">🚤</div>
                        <h3 data-i18n="services_h3_1">Botenverhuur</h3>
                        <p data-i18n="services_p_1">Elektrische boten, kano's, kajaks en SUP boards voor alle leeftijden.</p>
                        <div style="text-align:center;"><a href="/botenverhuur" class="btn btn-outline" data-i18n="services_btn_1">Bekijk Botenverhuur</a></div>
                    </div>
                    <div class="service-card">
                        <div class="service-icon">🏠</div>
                        <h3 data-i18n="services_h3_2">Vakantiehuis</h3>
                        <p data-i18n="services_p_2">Comfortabele vakantie accommodatie perfect voor families en groepen.</p>
                        <div style="text-align:center;"><a href="/vakantiehuis" class="btn btn-outline" data-i18n="services_btn_2">Bekijk Vakantiehuis</a></div>
                    </div>
                    <div class="service-card">
                        <div class="service-icon">🏕️</div>
                        <h3 data-i18n="services_h3_3">Camping</h3>
                        <p data-i18n="services_p_3">Prachtige kampeerplaatsen in het natuurgebied met moderne faciliteiten.</p>
                        <div style="text-align:center;"><a href="/camping" class="btn btn-outline" data-i18n="services_btn_3">Bekijk Camping</a></div>
                    </div>

                </div>
            </div>
        </section>

        <!-- Discover Destinations Section -->
        <section class="content-section" style="background: #fff; padding: 4rem 0;">
            <div class="container">
                <div class="section-title" style="text-align: center; margin-bottom: 3rem;">
                    <h2 style="font-size: 2rem; margin-bottom: 1rem; color: var(--primary-color, #003366);">Ontdek de Mooiste Bestemmingen</h2>
                    <p style="font-size: 1.1rem; color: var(--text-secondary, #555);">Vaar naar Giethoorn of ontdek het verborgen parel Belt-schutsloot</p>
                </div>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; max-width: 1000px; margin: 0 auto;">
                    <div style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); padding: 2.5rem; border-radius: 16px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); text-align: center;">
                        <div style="font-size: 3rem; margin-bottom: 1rem;">🏘️</div>
                        <h3 style="font-size: 1.5rem; margin-bottom: 1rem; color: var(--primary-color, #003366);">Giethoorn</h3>
                        <p style="line-height: 1.8; color: #666; margin-bottom: 1.5rem;">
                            Ontdek het beroemde "Venetië van het Noorden" met zijn idyllische grachten, meer dan 180 bruggetjes 
                            en karakteristieke rietgedekte boerderijen. Perfect voor een onvergetelijke vaartocht.
                        </p>
                        <a href="/giethoorn" 
                           style="display: inline-block; background: var(--primary-color, #003366); color: white; padding: 0.75rem 1.5rem; border-radius: 8px; text-decoration: none; font-weight: 600; transition: transform 0.2s;">
                            Ontdek Giethoorn →
                        </a>
                    </div>
                    
                    <div style="background: linear-gradient(135deg, #fff5e6 0%, #ffe6cc 100%); padding: 2.5rem; border-radius: 16px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); text-align: center; border: 2px solid #ffa500;">
                        <div style="font-size: 3rem; margin-bottom: 1rem;">💎</div>
                        <h3 style="font-size: 1.5rem; margin-bottom: 1rem; color: var(--primary-color, #003366);">Belt-schutsloot</h3>
                        <p style="line-height: 1.8; color: #666; margin-bottom: 1.5rem;">
                            Ontdek dit verborgen parel: dezelfde idyllische charme als Giethoorn, maar rustiger en minder toeristisch. 
                            Perfect voor wie op zoek is naar authenticiteit en rust.
                        </p>
                        <a href="/belt-schutsloot" 
                           style="display: inline-block; background: #ffa500; color: white; padding: 0.75rem 1.5rem; border-radius: 8px; text-decoration: none; font-weight: 600; transition: transform 0.2s;">
                            Ontdek Belt-schutsloot →
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Entity Definition Block - AI Optimized -->
        <section class="about-section" style="background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); padding: 4rem 0;">
            <div class="container">
                <div class="section-title">
                    <h2 data-i18n="home_about_title">Over Nijenhuis Botenverhuur</h2>
                    <p data-i18n="home_about_tagline">Al meer dan 50 jaar jouw vertrouwde partner voor watersport in de Weerribben</p>
                </div>
                
                <div class="about-content" style="max-width: 1000px; margin: 0 auto;">
                    <div style="text-align: center; margin-bottom: 3rem; font-size: 1.2rem; line-height: 1.8; color: var(--text-secondary);">
                        <p>
                            <strong>Nijenhuis Botenverhuur</strong> is een familiebedrijf voor bootjes verhuur, seizoenscamping en jachthaven, 
                            gelegen aan de <strong>Veneweg 199 in Wanneperveen</strong>, direct aan 
                            <strong>Nationaal Park Weerribben-Wieden</strong> – het grootste laagveenmoeras van Noordwest-Europa.
                        </p>
                    </div>
                    
                    <div class="facts-grid" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 2rem;">
                        <div class="fact-card" style="background: white; padding: 2rem; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); transition: transform 0.3s ease; display: flex; flex-direction: column; align-items: center; text-align: center;">
                            <div style="width: 60px; height: 60px; background: rgba(59, 130, 246, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; margin-bottom: 1rem;">📍</div>
                            <h3 style="margin: 0 0 0.5rem; color: var(--primary-color); font-size: 1.25rem;" data-i18n="about_location_title">Locatie</h3>
                            <p style="margin: 0; color: var(--text-secondary);" data-i18n="about_location_desc">
                                Wanneperveen, Overijssel<br>
                                <span class="fact-sub">
                                    10 km van <a href="/giethoorn" style="color: var(--primary-color, #003366); text-decoration: underline; font-weight: 500;">Giethoorn</a>
                                </span>
                            </p>
                        </div>
                        
                        <div class="fact-card" style="background: white; padding: 2rem; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); transition: transform 0.3s ease; display: flex; flex-direction: column; align-items: center; text-align: center;">
                            <div style="width: 60px; height: 60px; background: rgba(16, 185, 129, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; margin-bottom: 1rem;">📅</div>
                            <h3 style="margin: 0 0 0.5rem; color: var(--primary-color); font-size: 1.25rem;" data-i18n="about_season_title">Seizoen</h3>
                            <p style="margin: 0; color: var(--text-secondary);" data-i18n="about_season_desc">1 april – 31 oktober<br><span class="fact-sub">Dagelijks 09:00-18:00</span></p>
                        </div>
                        
                        <div class="fact-card" style="background: white; padding: 2rem; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); transition: transform 0.3s ease; display: flex; flex-direction: column; align-items: center; text-align: center;">
                            <div style="width: 60px; height: 60px; background: rgba(245, 158, 11, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; margin-bottom: 1rem;">🚤</div>
                            <h3 style="margin: 0 0 0.5rem; color: var(--primary-color); font-size: 1.25rem;" data-i18n="about_fleet_title">Boten</h3>
                            <p style="margin: 0; color: var(--text-secondary);" data-i18n="about_fleet_desc">25+ vaartuigen<br><span class="fact-sub">1 tot 12 personen</span></p>
                        </div>
                        
                        <div class="fact-card" style="background: white; padding: 2rem; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); transition: transform 0.3s ease; display: flex; flex-direction: column; align-items: center; text-align: center;">
                            <div style="width: 60px; height: 60px; background: rgba(99, 102, 241, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; margin-bottom: 1rem;">💳</div>
                            <h3 style="margin: 0 0 0.5rem; color: var(--primary-color); font-size: 1.25rem;" data-i18n="about_prices_title">Prijzen</h3>
                            <p style="margin: 0; color: var(--text-secondary);" data-i18n="about_prices_desc">Vanaf €20/dag<br><span class="fact-sub">Geen vaarbewijs nodig</span></p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Interactive Boat Finder (Moved to botenverhuur.php) -->

        <!-- Testimonials Section -->
        <section class="testimonials-section" style="display: none; background: var(--secondary-color); padding: 4rem 0; color: white;">
            <div class="container">
                <div class="section-title" style="color: white;">
                    <h2 style="color: white;">Wat onze gasten zeggen</h2>
                    <p style="color: rgba(255,255,255,0.8);">Ervaringen van bezoekers aan Nijenhuis Botenverhuur</p>
                </div>
                
                <div class="testimonials-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem; margin-top: 2rem;">
                    
                    <div class="testimonial-card" style="background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); padding: 1.5rem; border-radius: 16px; border: 1px solid rgba(255,255,255,0.2);">
                        <div style="display: flex; gap: 0.25rem; margin-bottom: 1rem;">
                            <span style="color: #fbbf24;">★</span>
                            <span style="color: #fbbf24;">★</span>
                            <span style="color: #fbbf24;">★</span>
                            <span style="color: #fbbf24;">★</span>
                            <span style="color: #fbbf24;">★</span>
                        </div>
                        <p style="font-style: italic; line-height: 1.7; margin-bottom: 1rem;">"Fantastische dag gehad met het hele gezin! De electrosloep was makkelijk te besturen en we konden heerlijk picknicken op het water. Aanrader!"</p>
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <div style="width: 40px; height: 40px; background: var(--primary-color); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold;">F</div>
                            <div>
                                <strong>Familie de Vries</strong>
                                <p style="margin: 0; font-size: 0.85rem; opacity: 0.8;">Amsterdam</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="testimonial-card" style="background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); padding: 1.5rem; border-radius: 16px; border: 1px solid rgba(255,255,255,0.2);">
                        <div style="display: flex; gap: 0.25rem; margin-bottom: 1rem;">
                            <span style="color: #fbbf24;">★</span>
                            <span style="color: #fbbf24;">★</span>
                            <span style="color: #fbbf24;">★</span>
                            <span style="color: #fbbf24;">★</span>
                            <span style="color: #fbbf24;">★</span>
                        </div>
                        <p style="font-style: italic; line-height: 1.7; margin-bottom: 1rem;">"Al jaren komen we hier met de caravan. De rust, de natuur en de vriendelijke service maken het elke keer weer speciaal. Echt een verborgen parel!"</p>
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <div style="width: 40px; height: 40px; background: #10b981; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold;">J</div>
                            <div>
                                <strong>Jan & Marieke</strong>
                                <p style="margin: 0; font-size: 0.85rem; opacity: 0.8;">Seizoengasten camping</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="testimonial-card" style="background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); padding: 1.5rem; border-radius: 16px; border: 1px solid rgba(255,255,255,0.2);">
                        <div style="display: flex; gap: 0.25rem; margin-bottom: 1rem;">
                            <span style="color: #fbbf24;">★</span>
                            <span style="color: #fbbf24;">★</span>
                            <span style="color: #fbbf24;">★</span>
                            <span style="color: #fbbf24;">★</span>
                            <span style="color: #fbbf24;">★</span>
                        </div>
                        <p style="font-style: italic; line-height: 1.7; margin-bottom: 1rem;">"Met de kajak door de kleine slootjes, vogelgeluiden overal... Dit is het echte Nederland! Mooier dan Giethoorn zelf, veel rustiger hier."</p>
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <div style="width: 40px; height: 40px; background: #f59e0b; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold;">S</div>
                            <div>
                                <strong>Stefan</strong>
                                <p style="margin: 0; font-size: 0.85rem; opacity: 0.8;">Duitsland</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Map Section -->
        <section class="map-section">
            <div class="container">
                <h2 data-i18n="map_h2">Vind ons</h2>
                <div class="map-container">
                    <iframe 
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d77373.91668916645!2d6.077958504433576!3d52.69726901355547!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47c871953a3891e5%3A0x1a70802adc308067!2sVeneweg+199%2C+7946+LP+Wanneperveen!5e0!3m2!1sen!2snl!4v1552921192864" 
                        width="100%" 
                        height="400" 
                        style="border:0; border-radius: var(--border-radius);" 
                        allowfullscreen="" 
                        loading="lazy" 
                        referrerpolicy="no-referrer-when-downgrade"
                        title="Locatie Nijenhuis Botenverhuur - Veneweg 199, Wanneperveen">
                    </iframe>
                </div>
            </div>
        </section>
    </main>

    <?php include __DIR__ . '/../components/footer.php'; ?>


    <!-- Boat Info Modal -->
    <div id="boatInfoModal" class="boat-info-modal">
        <div class="boat-info-content">
            <button class="boat-info-close" id="boatInfoClose" aria-label="Sluiten">&times;</button>
            <div id="boatInfoBody"></div>
        </div>
    </div>

    <!-- Availability Calendar Modal -->
    <div id="availabilityCalendarModal" class="availability-calendar-modal">
        <div class="calendar-modal-content">
            <button class="calendar-modal-close" id="calendarModalClose" aria-label="Sluiten">&times;</button>
            <div class="calendar-header">
                <h2 id="calendarBoatName">Boot Beschikbaarheid</h2>
                <p class="calendar-legend">
                    <span class="legend-item"><span class="legend-dot available"></span> Beschikbaar</span>
                    <span class="legend-item"><span class="legend-dot unavailable"></span> Niet beschikbaar</span>
                    <span class="legend-item"><span class="legend-dot selected"></span> Geselecteerd</span>
                </p>
            </div>
            
            <div class="calendar-navigation">
                <button class="calendar-nav-btn" id="prevMonth">❮</button>
                <h3 id="currentMonthYear"></h3>
                <button class="calendar-nav-btn" id="nextMonth">❯</button>
            </div>
            
            <div class="calendar-grid" id="calendarGrid">
                <!-- Calendar will be generated here -->
            </div>
            
            <div class="calendar-selection-info" id="selectionInfo" style="display:none; margin-top: 15px; padding: 10px; background: #e8f5e9; border-radius: 8px; text-align: center;">
                <p style="margin: 0; font-weight: 600;">Geselecteerde periode:</p>
                <p id="selectionRangeText" style="margin: 5px 0;">-</p>
                <p id="selectionPriceText" style="margin: 5px 0; font-size: 1.1em; color: var(--primary-color);">Totaal: €0</p>
            </div>
            <div id="selectionError" style="display:none; margin-top: 10px; color: #c62828; text-align: center; font-size: 0.9em;">
                De geselecteerde periode bevat niet-beschikbare dagen.
            </div>

            <div id="boatOptions" class="calendar-options" style="margin-top: 10px; text-align: center;"></div>

            <div class="calendar-actions">
                <button class="btn btn-primary disabled" id="addToCartBtn" onclick="addCurrentBoatToCart()">🛒 Toevoegen aan winkelwagen</button>
            </div>
        </div>
    </div>

    <!-- New Modular Scripts -->
    <script src="<?php echo assetPath('frontend/src/js/hooks/useBoatData.js'); ?>"></script>
    <script src="<?php echo assetPath('frontend/src/js/hooks/useBookingAvailability.js'); ?>"></script>
    <script src="<?php echo assetPath('frontend/src/js/pages/home.js'); ?>"></script>
</body>
</html>

