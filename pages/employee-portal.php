<?php
/**
 * Employee Portal - Medewerker Portaal
 * Replicates index.php look and feel for manual bookings without payment.
 */
require_once __DIR__ . '/../components/config.php';
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$basePath = getBasePath();
$pageTitle = 'Medewerker Portaal';

// Auth Check - Check for both employee and admin authentication
$isLoggedIn = (isset($_SESSION['employee_authenticated']) && $_SESSION['employee_authenticated'] === true) ||
              (isset($_SESSION['admin_authenticated']) && $_SESSION['admin_authenticated'] === true);

$userName = $_SESSION['employee_user'] ?? $_SESSION['admin_user'] ?? 'Medewerker';
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title><?php echo $pageTitle; ?> | <?php echo SITE_NAME; ?></title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="<?php echo assetPath('frontend/Images/logo-white.svg'); ?>">
    
    <!-- Styles (Same as index.php) -->
    <link rel="preload" href="<?php echo assetPath('frontend/Images/banner-img.jpg'); ?>" as="image">
    <link rel="stylesheet" href="<?php echo assetPath('frontend/css/styles.css'); ?>">
    <link rel="stylesheet" href="<?php echo assetPath('js/booking-system.css'); ?>">
    <link rel="stylesheet" href="<?php echo assetPath('frontend/css/pages/employee-portal.css'); ?>">
    <link rel="stylesheet" href="<?php echo assetPath('frontend/css/pages/home.css'); ?>">
    
    <script>
        const adminSession = { csrfToken: '' };

        async function refreshAdminSession() {
            try {
                const endpoint = window.location.origin + '/admin/booking-handler.php?action=session';
                const response = await fetch(endpoint, {
                    method: 'GET',
                    credentials: 'include'
                });
                if (!response.ok) return false;
                
                // Read response as text first
                const responseText = await response.text();
                if (!responseText || responseText.trim() === '') {
                    return false;
                }
                
                // Check content type
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    console.error('Non-JSON response in session validation:', responseText);
                    return false;
                }
                
                let result;
                try {
                    result = JSON.parse(responseText);
                } catch (parseError) {
                    console.error('JSON parse error in session validation. Response text:', responseText);
                    return false;
                }
                
                if (result.success && result.authenticated) {
                    adminSession.csrfToken = result.csrfToken || '';
                    return true;
                }
            } catch (error) {
                console.error('Session validation error:', error);
            }
            return false;
        }

        async function getCsrfToken() {
            if (!adminSession.csrfToken) {
                await refreshAdminSession();
            }
            return adminSession.csrfToken || '';
        }
        window.SiteConfig = {
            seasonStart: { month: <?php echo SEASON_START_MONTH; ?>, day: <?php echo SEASON_START_DAY; ?> },
            seasonEnd: { month: <?php echo SEASON_END_MONTH; ?>, day: <?php echo SEASON_END_DAY; ?> },
            serverPort: <?php echo isDevelopment() ? 8000 : 80; ?>
        };
    </script>
    <script src="<?php echo assetPath('frontend/src/js/core/security.js'); ?>"></script>
    <script src="<?php echo assetPath('js/boat-data-service.js'); ?>"></script>
    <script src="<?php echo assetPath('frontend/src/js/core/shared.js'); ?>"></script>
    <script src="<?php echo assetPath('frontend/src/js/core/translation.js'); ?>"></script>
    <!-- Use same hooks as index.php -->
    <script src="<?php echo assetPath('frontend/src/js/hooks/useBoatData.js'); ?>"></script>
    <script src="<?php echo assetPath('frontend/src/js/hooks/useBookingAvailability.js'); ?>"></script>
    <!-- Include home.js for calendar picker functionality -->
    <script src="<?php echo assetPath('frontend/src/js/pages/home.js'); ?>"></script>
</head>
<body>
    <?php if (!$isLoggedIn): ?>
        <div class="modal portal-login-modal active">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>Medewerker login</h2>
                </div>
                <div class="modal-body">
                    <div id="loginError" style="display:none; color: #dc3545; text-align: center; margin-bottom: var(--spacing-lg); padding: var(--spacing-md); background: #f8d7da; border: 1px solid #f5c6cb; border-radius: var(--radius-md); font-weight: 500;"></div>
                    <form id="loginForm" onsubmit="handleLogin(event)" style="display: flex; flex-direction: column; gap: var(--spacing-lg); max-width: 400px; margin: 0 auto;">
                        <div class="form-group">
                            <label for="username" style="text-align: center;">Gebruikersnaam</label>
                            <input type="text" id="username" name="username" class="form-input" required>
                        </div>
                        <div class="form-group">
                            <label for="password" style="text-align: center;">Wachtwoord</label>
                            <input type="password" id="password" name="password" class="form-input" required>
                        </div>
                        <div class="form-actions" style="align-items: center;">
                            <button type="submit" class="btn btn-primary" id="loginBtn" style="width: 100%; max-width: 300px;">Inloggen</button>
                            <div style="text-align: center; margin-top: var(--spacing-md);">
                                <a href="/" style="color: var(--text-secondary); text-decoration: none; font-size: 0.9rem;">&larr; Terug naar website</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Background content (blurred optionally) -->
        <div style="filter: blur(5px); pointer-events: none;">
             <?php include __DIR__ . '/../components/topbar.php'; ?>
             <?php include __DIR__ . '/../components/nav.php'; ?>
             <section class="hero"><div class="container"><h1>Medewerker portaal</h1></div></section>
        </div>
    <?php else: ?>
    
        <div class="admin-bar">
            <div>
                <strong>Medewerker Portaal</strong> | Ingelogd als: <?php echo htmlspecialchars($userName); ?>
            </div>
            <button onclick="handleLogout()" class="btn btn-sm" style="background-color: #dc3545; color: white; border: none; padding: 8px 20px; border-radius: 5px; cursor: pointer;">Uitloggen</button>
        </div>

        <?php include __DIR__ . '/../components/topbar.php'; ?>
        <?php include __DIR__ . '/../components/nav.php'; ?>

        <!-- Hero Section -->
        <section class="hero">
            <div class="container">
                <div class="hero-layout">
                    <!-- Booking Form -->
                    <div class="booking-form-modern" id="booking">
                        <div class="manual-booking-badge" style="display: none;">⚠️ Wijziging: Handmatige invoer</div>
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
                                <button type="submit" class="btn">📞 Handmatig via telefoon</button>
                                <button type="button" id="receptieBtn" class="btn" style="text-align:center;">🏢 Handmatig aan de receptie</button>
                            </div>
                        </form>
                        

                    </div>
                    
                    <div class="hero-content">
                        <h1 data-i18n="hero_h1">Boot huren bij Giethoorn & Weerribben</h1>
                        <p data-i18n="hero_h1_p">Huur electrosloepen, zeilboten, kano's en kajaks in Nationaal Park Weerribben-Wieden. Direct aan het water bij Wanneperveen, de perfecte uitvalsbasis voor families en vrienden.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Main Content (Exact copy from index.php) -->
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
                        <h2 data-i18n="fleet_h2">Onze boten</h2>
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
                                <strong>Nijenhuis Botenverhuur</strong> is een familiebedrijf voor bootjes verhuur, seizoenscamping, 
                                gelegen aan de <strong>Veneweg 199 in Wanneperveen</strong>, direct aan 
                                <strong>Nationaal Park Weerribben-Wieden</strong> – het grootste laagveenmoeras van Noordwest-Europa.
                            </p>
                        </div>
                        
                        <div class="facts-grid" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 2rem;">
                            <div class="fact-card" style="background: white; padding: 2rem; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); transition: transform 0.3s ease; display: flex; flex-direction: column; align-items: center; text-align: center;">
                                <div style="width: 60px; height: 60px; background: rgba(59, 130, 246, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; margin-bottom: 1rem;">📍</div>
                                <h3 style="margin: 0 0 0.5rem; color: var(--primary-color); font-size: 1.25rem;" data-i18n="about_location_title">Locatie</h3>
                                <p style="margin: 0; color: var(--text-secondary);" data-i18n="about_location_desc">Wanneperveen, Overijssel<br><span class="fact-sub">10 km van Giethoorn</span></p>
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

        <!-- Booking Modal (Reused for Manual Entry details) -->
        <div id="bookingModal" class="booking-modal">
            <div class="booking-modal-content">
                <div class="booking-modal-header">
                    <h2 id="bookingModalTitle">Klantgegevens (Handmatig)</h2>
                    <button class="booking-modal-close" id="closeBookingModal">&times;</button>
                </div>
                <div class="booking-modal-body">
                    <div id="availabilityCheck" class="availability-check hidden">
                        <!-- Used for loading states -->
                    </div>
                    
                    <form id="manualDetailsForm" class="booking-details-form">
                        <div class="manual-booking-badge" style="width:100%; text-align:center;">Geen betaling vereist - Voeg klant toe</div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="customerName">Naam *</label>
                                <input type="text" id="customerName" name="customerName" required>
                            </div>
                            <div class="form-group">
                                <label for="customerEmail">E-mail *</label>
                                <input type="email" id="customerEmail" name="customerEmail" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="customerPhone">Telefoon *</label>
                                <input type="tel" id="customerPhone" name="customerPhone" required>
                            </div>
                            <div class="form-group">
                                <label for="arrivalTime">Aankomsttijd *</label>
                                <select id="arrivalTime" name="arrivalTime" required>
                                    <option value="">-- Selecteer tijd --</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="cityOfOrigin">Woonplaats *</label>
                                <input type="text" id="cityOfOrigin" name="cityOfOrigin" required placeholder="Bijv. Amsterdam">
                            </div>
                            <div class="form-group">
                                <label for="modalBoatQuantity">Aantal boten *</label>
                                <select id="modalBoatQuantity" name="boatQuantity" required>
                                    <option value="1">1</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group" style="grid-column: 1 / -1;">
                                <label for="customerNotes">Opmerkingen</label>
                                <textarea id="customerNotes" name="customerNotes" rows="3"></textarea>
                            </div>
                        </div>
                        
                        <div class="booking-summary">
                            <h3>Reservering overzicht</h3>
                            <div class="summary-item">
                                <span class="summary-label">Datum:</span>
                                <span id="summaryDate"></span>
                            </div>
                             <div class="summary-item">
                                <span class="summary-label">Boot:</span>
                                <span id="summaryBoat"></span>
                            </div>
                            <div class="summary-item">
                                <span class="summary-label">Aantal boten:</span>
                                <span id="summaryQuantityModal">1</span>
                            </div>
                            <div class="summary-item">
                                <span class="summary-label">Aantal dagen:</span>
                                <span id="summaryDaysModal">1</span>
                            </div>
                            <div class="summary-item">
                                <span class="summary-label">Totaal prijs:</span>
                                <span id="summaryPriceModal" style="color: var(--primary-color); font-weight: 700; font-size: 1.2rem;">€0.00</span>
                            </div>
                            <!-- Deposit Note - Added dynamically if needed -->
                            <div id="depositNoteContainerModal" style="grid-column: 1 / -1; margin-top: 1rem;"></div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn" style="background: #28a745;">Reservering Bevestigen</button>
                            <button type="button" class="btn btn-outline" id="cancelBooking">Annuleren</button>
                        </div>
                    </form>
                    
                    <div id="bookingSuccess" class="booking-success hidden">
                        <div class="success-icon">✅</div>
                        <h3>Handmatige reservering geslaagd!</h3>
                        <p>De reservering is toegevoegd aan het systeem.</p>
                        <p id="emailWarning" class="email-warning hidden" style="background:#fff3cd;color:#856404;padding:10px;border-radius:6px;margin-top:10px;">
                            Let op: De bevestigingsmail kon niet worden verzonden. Controleer of de Microsoft Graph e-mail configuratie (.env) correct is ingesteld.
                        </p>
                        <div class="booking-id">
                            <strong>Reservering ID:</strong> <span id="bookingId"></span>
                        </div>
                        <button class="btn" onclick="location.reload()">Nieuwe Boeking</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Receptie Modal (Simplified - only boat + time) -->
        <div id="receptieModal" class="booking-modal">
            <div class="booking-modal-content">
                <div class="booking-modal-header">
                    <h2>Receptie Reservering</h2>
                    <button class="booking-modal-close" id="closeReceptieModal">&times;</button>
                </div>
                <div class="booking-modal-body">
                    <form id="receptieForm" class="booking-details-form">
                        <div class="manual-booking-badge" style="width:100%; text-align:center;">Snelle boeking aan de receptie</div>
                        <div class="booking-summary">
                            <h3>Reservering overzicht</h3>
                            <div class="summary-item">
                                <span class="summary-label">Datum:</span>
                                <span id="receptieSummaryDate"></span>
                            </div>
                            <div class="summary-item">
                                <span class="summary-label">Boot:</span>
                                <span id="receptieSummaryBoat"></span>
                            </div>
                            <div class="summary-item">
                                <span class="summary-label">Aantal boten:</span>
                                <span id="receptieSummaryQuantity">1</span>
                            </div>
                            <div class="summary-item">
                                <span class="summary-label">Totaal prijs:</span>
                                <span id="receptieSummaryPrice" style="color: var(--primary-color); font-weight: 700; font-size: 1.2rem;">€0.00</span>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group" style="grid-column: 1 / -1;">
                                <label for="receptieArrivalTime">Aankomsttijd *</label>
                                <select id="receptieArrivalTime" name="arrivalTime" required>
                                    <option value="">-- Selecteer tijd --</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn" style="background: #28a745;">Reservering Bevestigen</button>
                            <button type="button" class="btn btn-outline" id="cancelReceptie">Annuleren</button>
                        </div>
                    </form>

                    <div id="receptieSuccess" class="booking-success hidden">
                        <div class="success-icon">✅</div>
                        <h3>Receptie reservering geslaagd!</h3>
                        <p>De reservering is toegevoegd aan het systeem.</p>
                        <div class="booking-id">
                            <strong>Reservering ID:</strong> <span id="receptieBookingId"></span>
                        </div>
                        <button class="btn" onclick="location.reload()">Nieuwe Boeking</button>
                    </div>
                </div>
            </div>
        </div>

    <?php endif; ?>

    <script>
        // AUTHENTICATION LOGIC
        async function handleLogin(e) {
            e.preventDefault();
            const btn = document.getElementById('loginBtn');
            const errorDiv = document.getElementById('loginError');
            
            btn.disabled = true;
            btn.textContent = 'Bezig met inloggen...';
            errorDiv.style.display = 'none';

            try {
                const username = document.getElementById('username').value.trim();
                const password = document.getElementById('password').value;
                
                if (!username || !password) {
                    errorDiv.textContent = 'Voer zowel gebruikersnaam als wachtwoord in';
                    errorDiv.style.display = 'block';
                    btn.disabled = false;
                    btn.textContent = 'Inloggen';
                    return;
                }

                // Use PHP handler for both local and production to avoid CORS and port issues
                const endpoint = window.location.origin + '/admin/booking-handler.php';
                
                console.log('Attempting login to:', endpoint);
                
                // For PHP handler we need action
                const response = await fetch(endpoint, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    credentials: 'include', // Required for session cookies
                    body: JSON.stringify({
                        action: 'employeeLogin',
                        username: username,
                        password: password
                    })
                });

                console.log('Response status:', response.status);
                console.log('Response headers:', Object.fromEntries(response.headers.entries()));

                // Check if response is ok and is JSON
                if (!response.ok) {
                    const errorText = await response.text();
                    console.error('HTTP error response:', errorText);
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                // Read response as text first to avoid parsing errors
                const responseText = await response.text();
                
                if (!responseText || responseText.trim() === '') {
                    throw new Error('Empty response from server');
                }
                
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    console.error('Non-JSON response:', responseText);
                    errorDiv.textContent = 'Server fout: Ongeldige response';
                    errorDiv.style.display = 'block';
                    btn.disabled = false;
                    btn.textContent = 'Inloggen';
                    return;
                }

                let data;
                try {
                    data = JSON.parse(responseText);
                } catch (parseError) {
                    console.error('JSON parse error. Response text:', responseText);
                    throw new Error('Invalid JSON response from server');
                }
                console.log('Login response:', data);
                
                if (data.success) {
                    console.log('Login successful, reloading page...');
                    location.reload();
                } else {
                    errorDiv.textContent = data.message || 'Inloggen mislukt';
                    errorDiv.style.display = 'block';
                    btn.disabled = false;
                    btn.textContent = 'Inloggen';
                }
            } catch (err) {
                console.error('Login error:', err);
                errorDiv.textContent = 'Er is een fout opgetreden: ' + (err.message || 'Onbekende fout');
                errorDiv.style.display = 'block';
                btn.disabled = false;
                btn.textContent = 'Inloggen';
            }
        }

        async function handleLogout() {
             try {
                const endpoint = window.location.origin + '/admin/booking-handler.php';
                
                await fetch(endpoint, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    credentials: 'include',
                    body: JSON.stringify({ action: 'logout' })
                });
                
                // Clear any local storage related to employee session
                localStorage.removeItem('employeeAuthenticated');
                localStorage.removeItem('employeeUser');
                
                // Redirect to login page
                window.location.href = window.location.origin + '/pages/employee-portal.php';
             } catch (e) { 
                console.error('Logout error:', e);
                window.location.href = window.location.origin + '/pages/employee-portal.php';
             }
        }

    <?php if ($isLoggedIn): ?>
        // EMPLOYEE PORTAL BOOKING LOGIC
        // Override home.js form submission to use manual booking modal instead of checkout
        
        let currentManualBooking = null;
        let currentReceptieBooking = null;
        
        // Wait for home.js to initialize, then override form submission
        document.addEventListener('DOMContentLoaded', () => {
            // Override the booking form submission after home.js initializes
            // Use a longer timeout to ensure home.js has fully initialized
            setTimeout(() => {
                const bookingForm = document.getElementById('bookingForm');
                if (bookingForm) {
                    // Don't clone the form - just override the submit handler
                    // This preserves all event listeners including calendar
                    const originalSubmit = bookingForm.onsubmit;
                    bookingForm.onsubmit = null;
                    
                    // Remove any existing submit listeners by replacing with a new one
                    // But preserve the form element itself to keep calendar listeners
                    bookingForm.addEventListener('submit', async function(e) {
                        e.preventDefault();
                        e.stopImmediatePropagation(); // Prevent other handlers
                        
                        const boatType = document.getElementById('boatType')?.value;
                        const dateInput = document.getElementById('date');
                        const endDateInput = document.getElementById('rentalEndDate');
                        const quantityInput = document.getElementById('boatQuantity');
                        const date = dateInput?.value;
                        const endDate = endDateInput?.value || date;
                        const quantity = quantityInput ? parseInt(quantityInput.value) || 1 : 1;
                        
                        if (!boatType || !date) {
                            alert('Vul alle verplichte velden in.');
                            return;
                        }
                        
                        currentManualBooking = {
                            date: date,
                            endDate: endDate,
                            boatType: boatType,
                            quantity: quantity
                        };
                        
                        showManualModal();
                    }, true); // Use capture phase to run before other handlers
                }
                
                // Show "Add to Cart" button for employee portal (cart adds items for batch manual booking)
                const receptieBtn = document.getElementById('receptieBtn');
                if (receptieBtn) {
                    receptieBtn.addEventListener('click', function() {
                        const boatType = document.getElementById('boatType')?.value;
                        const dateInput = document.getElementById('date');
                        const endDateInput = document.getElementById('rentalEndDate');
                        const quantityInput = document.getElementById('boatQuantity');
                        const date = dateInput?.value;
                        const endDate = endDateInput?.value || date;
                        const quantity = quantityInput ? parseInt(quantityInput.value) || 1 : 1;

                        if (!boatType || !date) {
                            alert('Selecteer eerst een boot en datum.');
                            return;
                        }

                        currentReceptieBooking = {
                            date: date,
                            endDate: endDate,
                            boatType: boatType,
                            quantity: quantity
                        };

                        showReceptieModal();
                    });
                }
                
                // Subscribe to booking updates for boat fleet rendering
                if (window.useBookingAvailability) {
                    const bookingService = window.useBookingAvailability();
                    if (bookingService && bookingService.subscribe) {
                        bookingService.subscribe((bookings) => {
                            // Update boat fleet when bookings change
                            if (window.HomePage && typeof window.HomePage.renderBoatFleetSection === 'function') {
                                const dateVal = document.getElementById('date')?.value;
                                window.HomePage.renderBoatFleetSection(dateVal);
                            }
                        });
                    }
                }
            }, 1000); // Increased timeout to ensure home.js is fully loaded
        });
        
        // Override cart checkout to create manual bookings instead of redirecting to payment page
        document.addEventListener('DOMContentLoaded', () => {
            // Wait for cart.js to load and set up globals
            setTimeout(() => {
                // Replace the "Afrekenen" button behavior with manual booking flow
                window.validateAndCheckout = async function() {
                    const cart = window.CartManager;
                    if (!cart || cart.isEmpty()) {
                        alert('De winkelwagen is leeg.');
                        return;
                    }
                    
                    const items = cart.getItems();
                    
                    // Store cart items for manual booking and open the manual booking modal
                    // We'll collect customer details once and apply to all items
                    window._employeeCartCheckout = true;
                    window._employeeCartItems = items.map(item => ({
                        boatId: item.boatId,
                        boatName: item.boatName,
                        startDate: item.startDate,
                        endDate: item.endDate,
                        days: item.days,
                        quantity: item.quantity || 1,
                        useMotor: item.useMotor || false,
                        price: item.price,
                        pricePerBoat: item.pricePerBoat || item.price
                    }));
                    
                    // Close cart sidebar
                    if (window.toggleCartSidebar) {
                        window.toggleCartSidebar();
                    }
                    
                    // Build a summary of all cart items
                    let summaryHtml = '';
                    let totalPrice = 0;
                    window._employeeCartItems.forEach((item, idx) => {
                        const qty = item.quantity || 1;
                        const itemPrice = item.price || 0;
                        totalPrice += itemPrice;
                        const startStr = new Date(item.startDate).toLocaleDateString('nl-NL');
                        const endStr = new Date(item.endDate).toLocaleDateString('nl-NL');
                        const dateStr = item.startDate === item.endDate ? startStr : `${startStr} t/m ${endStr}`;
                        const motorLabel = (item.useMotor && item.boatId === 'sailboat-4-5') ? ' <small style="color:#666;">(+ motor)</small>' : '';
                        summaryHtml += `<div style="padding: 8px 0; border-bottom: 1px solid #eee;">
                            <strong>${item.boatName}</strong>${motorLabel}${qty > 1 ? ` (${qty}x)` : ''}<br>
                            <span style="color: var(--text-secondary); font-size: 0.9em;">${dateStr} - ${item.days} dag${item.days > 1 ? 'en' : ''}</span>
                            <span style="float: right; font-weight: 600;">€${itemPrice.toFixed(2)}</span>
                        </div>`;
                    });
                    summaryHtml += `<div style="padding: 10px 0; font-weight: 700; font-size: 1.1em;">
                        Totaal: <span style="float: right; color: var(--primary-color);">€${totalPrice.toFixed(2)}</span>
                    </div>`;
                    
                    // Show the manual booking modal with cart items summary
                    const modal = document.getElementById('bookingModal');
                    if (!modal) return;
                    
                    resetConfirmBookingButton();
                    modal.classList.add('active');
                    document.body.style.overflow = 'hidden';
                    
                    // Update modal title
                    const modalTitle = document.getElementById('bookingModalTitle');
                    if (modalTitle) modalTitle.textContent = 'Klantgegevens voor winkelwagen';
                    
                    // Populate arrival time options
                    populateArrivalTimeOptions();
                    
                    // Hide quantity field in modal (cart already has quantities)
                    const modalQuantityRow = document.getElementById('modalBoatQuantity');
                    if (modalQuantityRow && modalQuantityRow.closest('.form-group')) {
                        modalQuantityRow.closest('.form-group').style.display = 'none';
                    }
                    
                    // Replace the booking summary with cart items summary
                    const summarySection = document.querySelector('#manualDetailsForm .booking-summary');
                    if (summarySection) {
                        summarySection.innerHTML = `<h3>Winkelwagen overzicht</h3>${summaryHtml}`;
                    }
                    
                    // Show the form, hide success
                    const form = document.getElementById('manualDetailsForm');
                    const success = document.getElementById('bookingSuccess');
                    if (form) form.classList.remove('hidden');
                    if (success) success.classList.add('hidden');
                };
                
                // Also update the checkout button text in the cart sidebar
                const checkoutBtn = document.querySelector('.cart-checkout-btn');
                if (checkoutBtn) {
                    checkoutBtn.textContent = 'Handmatig boeken';
                    checkoutBtn.setAttribute('data-i18n', '');
                }
            }, 1500); // After cart.js has loaded
        });
        
        // Modal handlers
        document.addEventListener('DOMContentLoaded', () => {
            const closeBtn = document.getElementById('closeBookingModal');
            const cancelBtn = document.getElementById('cancelBooking');
            if (closeBtn) closeBtn.addEventListener('click', closeManualModal);
            if (cancelBtn) cancelBtn.addEventListener('click', closeManualModal);
            
            // Final submit
            const detailsForm = document.getElementById('manualDetailsForm');
            if (detailsForm) {
                detailsForm.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    const btn = e.target.querySelector('button[type="submit"]');
                    btn.disabled = true;
                    btn.textContent = 'Verwerken...';
                    
                    // Shared customer details
                    const customerDetails = {
                        customerName: document.getElementById('customerName').value,
                        customerEmail: document.getElementById('customerEmail').value,
                        customerPhone: document.getElementById('customerPhone').value,
                        arrivalTime: document.getElementById('arrivalTime').value,
                        cityOfOrigin: document.getElementById('cityOfOrigin').value,
                        notes: document.getElementById('customerNotes').value,
                        status: 'manual'
                    };
                    
                    try {
                        const endpoint = window.location.origin + '/admin/booking-handler.php';
                        const csrf = await getCsrfToken();
                        
                        // Check if this is a cart checkout (multiple items) or single booking
                        if (window._employeeCartCheckout && window._employeeCartItems && window._employeeCartItems.length > 0) {
                            // Cart checkout: create a manual booking for each cart item
                            const items = window._employeeCartItems;
                            let createdCount = 0;
                            let lastBookingId = '';
                            let errors = [];
                            let anyEmailFailed = false;
                            
                            for (const item of items) {
                                const bookingData = {
                                    date: item.startDate,
                                    endDate: item.endDate || item.startDate,
                                    boatType: item.boatId,
                                    quantity: item.quantity || 1,
                                    engineOption: item.useMotor ? 'with' : 'without',
                                    ...customerDetails,
                                    csrfToken: csrf,
                                    forceOverride: true
                                };
                                
                                try {
                                    const response = await fetch(endpoint, {
                                        method: 'POST',
                                        headers: { 'Content-Type': 'application/json' },
                                        credentials: 'include',
                                        body: JSON.stringify({
                                            action: 'createManualBooking',
                                            ...bookingData
                                        })
                                    });
                                    
                                    if (!response.ok) {
                                        errors.push(`${item.boatName}: HTTP ${response.status}`);
                                        continue;
                                    }
                                    
                                    const text = await response.text();
                                    const result = JSON.parse(text);
                                    
                                    if (result.success) {
                                        createdCount += (result.bookingsCreated || 1);
                                        lastBookingId = result.booking?.id || result.bookingId || lastBookingId;
                                        if (result.confirmationEmailSent === false) anyEmailFailed = true;
                                    } else {
                                        errors.push(`${item.boatName}: ${result.message || 'Mislukt'}`);
                                    }
                                } catch (itemErr) {
                                    errors.push(`${item.boatName}: ${itemErr.message}`);
                                }
                            }
                            
                            if (createdCount > 0) {
                                // Clear cart after successful bookings
                                if (window.CartManager) {
                                    window.CartManager.clear();
                                }
                                window._employeeCartCheckout = false;
                                window._employeeCartItems = null;
                                
                                document.getElementById('manualDetailsForm').classList.add('hidden');
                                document.getElementById('bookingSuccess').classList.remove('hidden');
                                const successMsg = document.querySelector('#bookingSuccess h3');
                                if (successMsg) {
                                    successMsg.textContent = `${createdCount} Reservering${createdCount > 1 ? 'en' : ''} Aangemaakt!`;
                                }
                                const successDesc = document.querySelector('#bookingSuccess p');
                                if (successDesc) {
                                    successDesc.textContent = errors.length > 0 
                                        ? `${createdCount} reservering(en) succesvol. ${errors.length} mislukt.`
                                        : `Alle ${createdCount} reservering(en) zijn toegevoegd aan het systeem.`;
                                }
                                document.getElementById('bookingId').textContent = lastBookingId;
                                const emailWarn = document.getElementById('emailWarning');
                                if (emailWarn) emailWarn.classList.toggle('hidden', !anyEmailFailed);
                            } else {
                                alert('Geen reserveringen aangemaakt. Fouten: ' + errors.join('; '));
                                btn.disabled = false;
                                btn.textContent = 'Reservering Bevestigen';
                            }
                        } else {
                            // Single booking (from "Boek nu" button)
                            const bookingData = {
                                date: currentManualBooking.date,
                                endDate: currentManualBooking.endDate || currentManualBooking.date,
                                boatType: currentManualBooking.boatType,
                                quantity: parseInt(document.getElementById('modalBoatQuantity')?.value || document.getElementById('boatQuantity')?.value || '1') || 1,
                                ...customerDetails,
                                engineOption: document.getElementById('engineOption')?.value || 'without'
                            };
                            
                            // Employee portal: always allow override so staff can complete bookings
                            const payload = {
                                action: 'createManualBooking',
                                ...bookingData,
                                csrfToken: csrf,
                                forceOverride: true
                            };
                            
                            const response = await fetch(endpoint, {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json' },
                                credentials: 'include',
                                body: JSON.stringify(payload)
                            });
                            
                            if (!response.ok) {
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }
                            
                            const text = await response.text();
                            if (!text || text.trim() === '') {
                                throw new Error('Empty response from server');
                            }
                            
                            let result;
                            try {
                                result = JSON.parse(text);
                            } catch (parseError) {
                                console.error('JSON parse error. Response text:', text);
                                throw new Error('Invalid JSON response from server');
                            }
                            
                            if (result.success) {
                                document.getElementById('manualDetailsForm').classList.add('hidden');
                                document.getElementById('bookingSuccess').classList.remove('hidden');
                                const bookingId = result.booking?.id || result.bookingId || 'Unknown';
                                document.getElementById('bookingId').textContent = bookingId;
                                const emailWarn = document.getElementById('emailWarning');
                                if (emailWarn) emailWarn.classList.toggle('hidden', result.confirmationEmailSent !== false);
                            } else {
                                alert('Fout: ' + (result.message || 'Unknown error'));
                                btn.disabled = false;
                                btn.textContent = 'Reservering Bevestigen';
                            }
                        }
                    } catch(err) {
                        alert('Server fout: ' + (err.message || 'Onbekende fout'));
                        console.error('Booking creation error:', err);
                        btn.disabled = false;
                        btn.textContent = 'Reservering Bevestigen';
                    }
                });
            }
        });

        // Populate arrival time options (09:00 to 18:00 in 15-minute intervals)
        function populateArrivalTimeOptions() {
            const arrivalTimeSelect = document.getElementById('arrivalTime');
            if (!arrivalTimeSelect) return;

            // Clear existing options except the first placeholder
            arrivalTimeSelect.innerHTML = '<option value="">-- Selecteer tijd --</option>';

            // Generate time slots from 09:00 to 18:00 in 15-minute intervals
            const startHour = 9;
            const endHour = 18;
            const intervals = [0, 15, 30, 45]; // Quarterly steps

            for (let hour = startHour; hour <= endHour; hour++) {
                for (const minute of intervals) {
                    // Skip 18:15, 18:30, 18:45 (only allow up to 18:00)
                    if (hour === endHour && minute > 0) {
                        break;
                    }

                    const hourStr = hour.toString().padStart(2, '0');
                    const minuteStr = minute.toString().padStart(2, '0');
                    const timeValue = `${hourStr}:${minuteStr}`;
                    const timeDisplay = `${hourStr}:${minuteStr}`;

                    const option = document.createElement('option');
                    option.value = timeValue;
                    option.textContent = timeDisplay;
                    arrivalTimeSelect.appendChild(option);
                }
            }
        }

        // Populate quantity dropdown based on boat availability (for modal)
        async function populateQuantityDropdown() {
            const quantitySelect = document.getElementById('modalBoatQuantity');
            if (!quantitySelect || !currentManualBooking) return;

            const boatId = currentManualBooking.boatType;
            const startDate = currentManualBooking.date;
            const endDate = currentManualBooking.endDate || currentManualBooking.date;

            if (!boatId || !startDate) {
                quantitySelect.innerHTML = '<option value="1">1</option>';
                quantitySelect.value = '1';
                return;
            }

            try {
                // Get boat info to find total count
                let boats = [];
                if (window.BoatDataService) {
                    try {
                        boats = await window.BoatDataService.getAllBoats();
                    } catch (e) {
                        const stored = localStorage.getItem('nijenhuis_boats');
                        if (stored) boats = JSON.parse(stored);
                    }
                } else {
                    const stored = localStorage.getItem('nijenhuis_boats');
                    if (stored) boats = JSON.parse(stored);
                }

                const boat = boats.find(b => b.id === boatId);
                const fallbackMax = boat ? (boat.total ?? 10) : 10;

                // Check availability
                const endpoint = window.location.origin + '/admin/booking-handler.php';
                const response = await fetch(`${endpoint}?action=checkAvailability&boatType=${encodeURIComponent(boatId)}&date=${encodeURIComponent(startDate)}&endDate=${encodeURIComponent(endDate)}`, {
                    method: 'GET',
                    credentials: 'include',
                    headers: { 'Accept': 'application/json' }
                });

                let maxQuantity = fallbackMax;
                if (response.ok) {
                    const text = await response.text();
                    if (text && text.trim()) {
                        try {
                            const data = JSON.parse(text);
                            if (data.success && data.data && data.data.availableCount !== undefined) {
                                maxQuantity = data.data.availableCount;
                            }
                        } catch (parseError) {
                            console.warn('Failed to parse availability response:', parseError);
                        }
                    }
                }

                // Populate dropdown
                quantitySelect.innerHTML = '';
                if (maxQuantity <= 0) {
                    quantitySelect.innerHTML = '<option value="0">Niet beschikbaar</option>';
                    quantitySelect.disabled = true;
                    return;
                }

                quantitySelect.disabled = false;
                for (let i = 1; i <= maxQuantity; i++) {
                    const option = document.createElement('option');
                    option.value = i;
                    option.textContent = i;
                    quantitySelect.appendChild(option);
                }

                // Set default to 1
                quantitySelect.value = '1';
            } catch (error) {
                console.error('Error populating quantity dropdown:', error);
                // Fallback to default
                quantitySelect.innerHTML = '<option value="1">1</option>';
                quantitySelect.value = '1';
            }
        }

        // Reset the confirm booking button so it is enabled and shows correct text
        function resetConfirmBookingButton() {
            const form = document.getElementById('manualDetailsForm');
            if (form) {
                const btn = form.querySelector('button[type="submit"]');
                if (btn) {
                    btn.disabled = false;
                    btn.textContent = 'Reservering Bevestigen';
                }
            }
        }

        // Modal functions
        async function showManualModal() {
            const modal = document.getElementById('bookingModal');
            if (!modal) return;
            
            resetConfirmBookingButton();
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
            
            // Populate arrival time options
            populateArrivalTimeOptions();
            
            // Populate quantity dropdown in modal (but use value from main form if available)
            await populateQuantityDropdown();
            
            // Sync quantity from main form to modal if available
            const mainFormQuantity = document.getElementById('boatQuantity');
            const modalQuantity = document.getElementById('modalBoatQuantity');
            if (mainFormQuantity && modalQuantity && currentManualBooking.quantity) {
                // Set the modal quantity to match the form quantity
                if (modalQuantity.querySelector(`option[value="${currentManualBooking.quantity}"]`)) {
                    modalQuantity.value = currentManualBooking.quantity.toString();
                } else {
                    // If the selected quantity isn't available in modal, use the main form value
                    modalQuantity.value = mainFormQuantity.value || '1';
                }
            } else if (mainFormQuantity && modalQuantity) {
                // Sync from main form even if not in currentManualBooking
                modalQuantity.value = mainFormQuantity.value || '1';
            }
            
            // Populate Summary
            const startDate = new Date(currentManualBooking.date);
            const summaryDateEl = document.getElementById('summaryDate');
            if (summaryDateEl) {
                summaryDateEl.textContent = startDate.toLocaleDateString('nl-NL');
            }
            
            // Get boat name
            const boatSelect = document.getElementById('boatType');
            const summaryBoatEl = document.getElementById('summaryBoat');
            if (boatSelect && summaryBoatEl) {
                const boatName = boatSelect.options[boatSelect.selectedIndex].text;
                summaryBoatEl.textContent = boatName;
            }

            // Function to update deposit note
            const updateDepositNote = () => {
                const quantitySelect = document.getElementById('modalBoatQuantity');
                const depositContainer = document.getElementById('depositNoteContainerModal');
                if (!quantitySelect || !depositContainer || !currentManualBooking) return;

                const quantity = parseInt(quantitySelect.value) || 1;
                const boatId = currentManualBooking.boatType;

                // Get boat info to check for deposit
                if (window.CartManager) {
                    const boat = window.CartManager.getBoatById(boatId);
                    if (boat && boat.deposit) {
                        const totalDeposit = Number(boat.deposit) * quantity;
                        const depositMsg = `Let op: Er wordt een borg van €${totalDeposit.toFixed(2)} gevraagd bij afhaling.`;
                        depositContainer.innerHTML = `
                            <div class="deposit-note">
                                <span>ℹ️</span>
                                <span>${depositMsg}</span>
                            </div>
                        `;
                        depositContainer.style.display = 'block';
                    } else {
                        depositContainer.style.display = 'none';
                        depositContainer.innerHTML = '';
                    }
                } else {
                    depositContainer.style.display = 'none';
                    depositContainer.innerHTML = '';
                }
            };

            // Function to update price in modal
            const updateModalPrice = () => {
                const quantitySelect = document.getElementById('modalBoatQuantity');
                const summaryPriceEl = document.getElementById('summaryPriceModal');
                if (!quantitySelect || !summaryPriceEl || !currentManualBooking) return;

                const quantity = parseInt(quantitySelect.value) || 1;
                const boatId = currentManualBooking.boatType;
                const startDateStr = currentManualBooking.date;
                const endDateStr = currentManualBooking.endDate || currentManualBooking.date;
                
                // Calculate days
                let days = 1;
                if (startDateStr && endDateStr) {
                    const start = new Date(startDateStr);
                    const end = new Date(endDateStr);
                    days = Math.ceil(Math.abs(end - start) / (1000 * 60 * 60 * 24)) + 1;
                }

                const engineSelect = document.getElementById('engineOption');
                const useMotor = boatId === 'sailboat-4-5' && engineSelect && engineSelect.value === 'with';

                // Calculate price using CartManager
                if (window.CartManager) {
                    const pricePerBoat = window.CartManager.calculatePrice(boatId, days, useMotor);
                    const totalPrice = pricePerBoat * quantity;
                    
                    let priceText = `€${totalPrice.toFixed(2)}`;
                    if (days > 1) {
                        priceText += ` voor ${days} dagen`;
                    } else {
                        priceText += ` voor 1 dag`;
                    }
                    if (quantity > 1) {
                        priceText += ` (${quantity}x)`;
                    }
                    
                    summaryPriceEl.textContent = priceText;
                } else {
                    // Fallback if CartManager not available
                    summaryPriceEl.textContent = '€0.00';
                }
            };

            // Update quantity in summary (use modal quantity)
            const quantitySelect = document.getElementById('modalBoatQuantity');
            const summaryQuantityEl = document.getElementById('summaryQuantityModal');
            if (quantitySelect && summaryQuantityEl) {
                summaryQuantityEl.textContent = quantitySelect.value || '1';
                // Update when quantity changes
                quantitySelect.addEventListener('change', () => {
                    summaryQuantityEl.textContent = quantitySelect.value;
                    // Also update currentManualBooking
                    currentManualBooking.quantity = parseInt(quantitySelect.value) || 1;
                    // Update price when quantity changes
                    updateModalPrice();
                    // Update deposit note when quantity changes
                    updateDepositNote();
                });
            }

            // Initial price calculation - always recalculate to ensure accuracy with current quantity
            const summaryPriceEl = document.getElementById('summaryPriceModal');
            if (summaryPriceEl) {
                // Always recalculate to ensure it matches the quantity in the modal
                updateModalPrice();
            }

            // Initial deposit note calculation
            updateDepositNote();
            
            // Calc days
            const startDateStr = currentManualBooking.date;
            const endDateStr = currentManualBooking.endDate || currentManualBooking.date;
            let days = 1;
            if (startDateStr && endDateStr) {
                const start = new Date(startDateStr);
                const end = new Date(endDateStr);
                days = Math.ceil(Math.abs(end - start) / (1000 * 60 * 60 * 24)) + 1;
            }
            const summaryDaysEl = document.getElementById('summaryDaysModal');
            if (summaryDaysEl) {
                summaryDaysEl.textContent = days;
            }
        }

        function closeManualModal() {
            const modal = document.getElementById('bookingModal');
            if (modal) {
                modal.classList.remove('active');
            }
            document.body.style.overflow = '';
            
            const form = document.getElementById('manualDetailsForm');
            const success = document.getElementById('bookingSuccess');
            if (form) form.classList.remove('hidden');
            if (success) success.classList.add('hidden');
            if (form) form.reset();
            resetConfirmBookingButton();
            
            // Reset cart checkout state
            window._employeeCartCheckout = false;
            window._employeeCartItems = null;
            
            // Reset modal title
            const modalTitle = document.getElementById('bookingModalTitle');
            if (modalTitle) modalTitle.textContent = 'Klantgegevens (Handmatig)';
            
            // Restore quantity field visibility
            const modalQuantityRow = document.getElementById('modalBoatQuantity');
            if (modalQuantityRow && modalQuantityRow.closest('.form-group')) {
                modalQuantityRow.closest('.form-group').style.display = '';
            }
            
            // Restore success section text
            const successMsg = document.querySelector('#bookingSuccess h3');
            if (successMsg) successMsg.textContent = 'Handmatige Reservering Geslaagd!';
            const successDesc = document.querySelector('#bookingSuccess p');
            if (successDesc) successDesc.textContent = 'De reservering is toegevoegd aan het systeem.';
            const emailWarn = document.getElementById('emailWarning');
            if (emailWarn) emailWarn.classList.add('hidden');
        }

        // --- Receptie Modal functions ---
        function showReceptieModal() {
            const modal = document.getElementById('receptieModal');
            if (!modal || !currentReceptieBooking) return;

            modal.classList.add('active');
            document.body.style.overflow = 'hidden';

            // Populate arrival time
            const arrivalSelect = document.getElementById('receptieArrivalTime');
            if (arrivalSelect) {
                arrivalSelect.innerHTML = '<option value="">-- Selecteer tijd --</option>';
                for (let h = 9; h <= 18; h++) {
                    for (const m of [0, 15, 30, 45]) {
                        if (h === 18 && m > 0) break;
                        const hh = h.toString().padStart(2, '0');
                        const mm = m.toString().padStart(2, '0');
                        const opt = document.createElement('option');
                        opt.value = `${hh}:${mm}`;
                        opt.textContent = `${hh}:${mm}`;
                        arrivalSelect.appendChild(opt);
                    }
                }
            }

            // Fill summary
            const dateEl = document.getElementById('receptieSummaryDate');
            if (dateEl) dateEl.textContent = new Date(currentReceptieBooking.date).toLocaleDateString('nl-NL');

            const boatSelect = document.getElementById('boatType');
            const boatEl = document.getElementById('receptieSummaryBoat');
            if (boatSelect && boatEl) {
                boatEl.textContent = boatSelect.options[boatSelect.selectedIndex]?.text || currentReceptieBooking.boatType;
            }

            const qtyEl = document.getElementById('receptieSummaryQuantity');
            if (qtyEl) qtyEl.textContent = currentReceptieBooking.quantity || 1;

            // Calculate price
            const priceEl = document.getElementById('receptieSummaryPrice');
            if (priceEl && window.CartManager) {
                const startD = currentReceptieBooking.date;
                const endD = currentReceptieBooking.endDate || startD;
                let days = 1;
                if (startD && endD) {
                    days = Math.ceil(Math.abs(new Date(endD) - new Date(startD)) / (1000*60*60*24)) + 1;
                }
                const engineSelect = document.getElementById('engineOption');
                const useMotor = currentReceptieBooking.boatType === 'sailboat-4-5' && engineSelect && engineSelect.value === 'with';
                const perBoat = window.CartManager.calculatePrice(currentReceptieBooking.boatType, days, useMotor);
                const total = perBoat * (currentReceptieBooking.quantity || 1);
                priceEl.textContent = `€${total.toFixed(2)}`;
            }

            // Reset form state
            const form = document.getElementById('receptieForm');
            const success = document.getElementById('receptieSuccess');
            if (form) form.classList.remove('hidden');
            if (success) success.classList.add('hidden');
            const btn = form?.querySelector('button[type="submit"]');
            if (btn) { btn.disabled = false; btn.textContent = 'Reservering Bevestigen'; }
        }

        function closeReceptieModal() {
            const modal = document.getElementById('receptieModal');
            if (modal) modal.classList.remove('active');
            document.body.style.overflow = '';
            const form = document.getElementById('receptieForm');
            if (form) { form.classList.remove('hidden'); form.reset(); }
            const success = document.getElementById('receptieSuccess');
            if (success) success.classList.add('hidden');
            currentReceptieBooking = null;
        }

        // Receptie modal event listeners
        document.addEventListener('DOMContentLoaded', () => {
            const closeBtn = document.getElementById('closeReceptieModal');
            const cancelBtn = document.getElementById('cancelReceptie');
            if (closeBtn) closeBtn.addEventListener('click', closeReceptieModal);
            if (cancelBtn) cancelBtn.addEventListener('click', closeReceptieModal);

            const receptieForm = document.getElementById('receptieForm');
            if (receptieForm) {
                receptieForm.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    const btn = e.target.querySelector('button[type="submit"]');
                    btn.disabled = true;
                    btn.textContent = 'Verwerken...';

                    if (!currentReceptieBooking) {
                        alert('Geen boekinggegevens beschikbaar.');
                        btn.disabled = false;
                        btn.textContent = 'Reservering Bevestigen';
                        return;
                    }

                    try {
                        const endpoint = window.location.origin + '/admin/booking-handler.php';
                        const csrf = await getCsrfToken();
                        const engineSelect = document.getElementById('engineOption');

                        const payload = {
                            action: 'createManualBooking',
                            date: currentReceptieBooking.date,
                            endDate: currentReceptieBooking.endDate || currentReceptieBooking.date,
                            boatType: currentReceptieBooking.boatType,
                            quantity: currentReceptieBooking.quantity || 1,
                            arrivalTime: document.getElementById('receptieArrivalTime').value,
                            customerName: 'Receptie',
                            customerEmail: 'receptie@nijenhuis.nl',
                            customerPhone: '-',
                            cityOfOrigin: '',
                            notes: '',
                            source: 'receptie',
                            status: 'manual',
                            engineOption: engineSelect?.value || 'without',
                            csrfToken: csrf,
                            forceOverride: true
                        };

                        const response = await fetch(endpoint, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            credentials: 'include',
                            body: JSON.stringify(payload)
                        });

                        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

                        const text = await response.text();
                        if (!text || text.trim() === '') throw new Error('Empty response from server');

                        const result = JSON.parse(text);

                        if (result.success) {
                            document.getElementById('receptieForm').classList.add('hidden');
                            document.getElementById('receptieSuccess').classList.remove('hidden');
                            document.getElementById('receptieBookingId').textContent = result.booking?.id || result.bookingId || 'Unknown';
                        } else {
                            alert('Fout: ' + (result.message || 'Onbekende fout'));
                            btn.disabled = false;
                            btn.textContent = 'Reservering Bevestigen';
                        }
                    } catch(err) {
                        alert('Server fout: ' + (err.message || 'Onbekende fout'));
                        console.error('Receptie booking error:', err);
                        btn.disabled = false;
                        btn.textContent = 'Reservering Bevestigen';
                    }
                });
            }
        });
        
        function prefillManualBooking(boatId) {
            const boatSelect = document.getElementById('boatType');
            if (!boatSelect) return;
            
            boatSelect.value = boatId;
            
            // Trigger change events (home.js will handle these)
            const changeEvent = new Event('change', { bubbles: true });
            boatSelect.dispatchEvent(changeEvent);
            
            // Scroll to form
            const form = document.querySelector('.booking-form-modern');
            if (form) {
                form.scrollIntoView({behavior: 'smooth', block: 'center'});
            }
            
            // Visual feedback
            boatSelect.style.borderColor = '#10b981';
            setTimeout(() => boatSelect.style.borderColor = '', 1000);
        }
        
    <?php endif; ?>
    </script>
</body>
</html>
