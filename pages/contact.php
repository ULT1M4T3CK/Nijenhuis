<?php
/**
 * Contact Page - Nijenhuis Botenverhuur
 */
require_once __DIR__ . '/../components/config.php';
$basePath = getBasePath();
$pageTitle = 'Contact & Route | Wanneperveen';
$pageDescription = 'Contact opnemen met Nijenhuis Botenverhuur. Adres: Veneweg 199, Wanneperveen. Tel: 0522 281 528. Open april-oktober, dagelijks 9:00-18:00. Gratis parkeren.';
$pageKeywords = 'contact nijenhuis botenverhuur, route wanneperveen, adres botenverhuur giethoorn';
$headerTitle = 'Contact';
$headerTitleI18n = 'contact_title';
$headerDescription = 'Neem contact met ons op voor vragen, reserveringen of meer informatie';
$headerDescriptionI18n = 'contact_p';

// Breadcrumbs for this page
$breadcrumbs = [
    ['name' => 'Home', 'url' => '/'],
    ['name' => 'Contact', 'url' => '/contact']
];
?>
<!DOCTYPE html>
<html lang="nl">
<?php include __DIR__ . '/../components/head.php'; ?>
<body data-page="contact">
    <?php include __DIR__ . '/../components/topbar.php'; ?>
    <?php include __DIR__ . '/../components/nav.php'; ?>
    <?php include __DIR__ . '/../components/breadcrumb.php'; ?>
    <?php include __DIR__ . '/../components/page-header.php'; ?>

    <main>
        <section class="content-section">
            <div class="container">
                <div class="section-title">
                    <h2 data-i18n="contact_h2">Contact &amp; Route</h2>
                    <p data-i18n="contact_h2_p">Neem contact op met Nijenhuis Botenverhuur in Wanneperveen. Bekijk hier onze contactgegevens en routebeschrijving.</p>
                </div>
                <div class="content-prose">
                    <p data-i18n="contact_intro_extra">Nijenhuis Botenverhuur ligt aan de Veneweg 199 in Wanneperveen, aan de rand van Nationaal Park Weerribben-Wieden. Wij zijn gespecialiseerd in bootverhuur – van electrosloepen en zeilboten tot kano's en SUP-boards – en bieden daarnaast seizoenscamping. Voor reserveringen, vragen over prijzen of beschikbaarheid kun je ons bellen of langskomen tijdens openingstijden. Er is gratis parkeergelegenheid bij onze locatie.</p>
                </div>

                <div class="contact-grid">
                    <div class="contact-info-cards">
                        <div class="contact-item">
                            <div class="contact-details">
                                <h4 data-i18n="contact_address_title"><span class="contact-icon">📍</span> Adres</h4>
                                <p data-i18n="contact_address"><?php echo SITE_ADDRESS; ?></p>
                                <p data-i18n="contact_zip"><?php echo SITE_POSTAL; ?></p>
                                <p data-i18n="contact_country"><?php echo SITE_COUNTRY; ?></p>
                            </div>
                        </div>



                        <div class="contact-item">
                            <div class="contact-details">
                                <h4 data-i18n="contact_opening_title"><span class="contact-icon">⏰</span> Openingstijden</h4>
                                <p data-i18n="contact_opening_p">Dagelijks: <?php echo SITE_HOURS; ?></p>
                                <p data-i18n="contact_season_p">Seizoen: <?php echo SITE_SEASON_START; ?> - <?php echo SITE_SEASON_END; ?></p>
                            </div>
                        </div>

                        <div class="contact-item">
                            <div class="contact-details">
                                <h4 data-i18n="contact_business_title"><span class="contact-icon">🏢</span> Bedrijfsgegevens</h4>
                                <p data-i18n="contact_kvk">Kvk: <?php echo SITE_KVK; ?></p>
                                <p data-i18n="contact_btw">Btw nr: <?php echo SITE_BTW; ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="contact-direct-card">
                        <h3 data-i18n="contact_call_title">Direct contact</h3>
                        <p data-i18n="contact_call_p">Voor vragen, reserveringen of meer informatie, bel ons direct:</p>
                        
                        <div class="call-button-container">
                            <a href="tel:<?php echo SITE_PHONE_LINK; ?>" class="call-button">
                                <div class="call-icon">📞</div>
                                <div class="call-text">
                                    <span class="call-number"><?php echo SITE_PHONE; ?></span>
                                    <span class="call-label" data-i18n="contact_call_button">Bel Nu</span>
                                </div>
                            </a>
                        </div>
                        
                        <div class="call-info">
                            <p><strong data-i18n="contact_call_info_p">Beschikbaar: Dagelijks van <?php echo SITE_HOURS; ?></strong></p>
                            <p><strong data-i18n="contact_call_info_p2">Seizoen: <?php echo SITE_SEASON_START; ?> - <?php echo SITE_SEASON_END; ?></strong></p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="content-section bg-secondary">
            <div class="container">
                <div class="section-title">
                    <h2 data-i18n="contact_route_h2">Routebeschrijving</h2>
                </div>
                <div class="content-prose">
                    <p data-i18n="contact_route_p1">Wanneperveen ligt in de Kop van Overijssel, tussen Meppel en Steenwijk. Kom je met de auto? Volg de borden naar Wanneperveen en zoek de Veneweg – wij zitten op nummer 199, direct aan het water. Vanuit Giethoorn is het circa 15 minuten rijden. Er is gratis parkeergelegenheid bij onze locatie. Openbaar vervoer: buslijn 77 stopt in de buurt van Wanneperveen; voor de exacte haltes raadpleeg de dienstregeling.</p>
                    <p data-i18n="contact_route_p2">Tijdens het seizoen (1 april – 31 oktober) zijn wij dagelijks geopend van 09:00 tot 18:00 uur. Voor boten en kano's raden we aan vooraf te reserveren, vooral in het weekend en in de zomermaanden. Bij aankomst kun je direct bij ons terecht voor de sleutel, instructie en routekaart.</p>
                </div>
            </div>
        </section>

        <section class="content-section">
            <div class="container">
                <div class="section-title">
                    <h2 data-i18n="contact_map_title">Waar vind je ons?</h2>
                    <p data-i18n="contact_map_p">Bekijk onze locatie op de kaart</p>
                </div>

                <div class="map-section">
                    <div class="map-container">
                        <iframe 
                            class="map-frame"
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d77373.91668916645!2d6.077958504433576!3d52.69726901355547!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47c871953a3891e5%3A0x1a70802adc308067!2sVeneweg+199%2C+7946+LP+Wanneperveen!5e0!3m2!1sen!2snl!4v1552921192864" 
                            allowfullscreen="" 
                            loading="lazy" 
                            referrerpolicy="no-referrer-when-downgrade"
                            title="Google Maps - Locatie Nijenhuis Botenverhuur, Veneweg 199, Wanneperveen">
                        </iframe>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php include __DIR__ . '/../components/footer.php'; ?>

    <script>
        // Page-specific: Contact form success handling
        (function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('success') === 'true') {
                const t = (key, fallback) => (window.getTranslation ? window.getTranslation(key) : fallback);
                const successMessage = document.createElement('div');
                successMessage.className = 'success-message';
                const content = document.createElement('div');
                content.className = 'success-content';
                const h3 = document.createElement('h3');
                h3.setAttribute('data-i18n', 'contact_success_title');
                h3.textContent = t('contact_success_title', '✅ Bericht succesvol verzonden!');
                const p1 = document.createElement('p');
                p1.setAttribute('data-i18n', 'contact_success_message');
                p1.textContent = t('contact_success_message', 'Bedankt voor je bericht. We nemen zo snel mogelijk contact met je op via het opgegeven e-mailadres.');
                const p2 = document.createElement('p');
                const strong = document.createElement('strong');
                strong.setAttribute('data-i18n', 'contact_success_sent_to');
                strong.textContent = t('contact_success_sent_to', 'Je bericht is verzonden naar: info@nijenhuis-botenverhuur.nl');
                p2.appendChild(strong);
                content.appendChild(h3);
                content.appendChild(p1);
                content.appendChild(p2);
                successMessage.appendChild(content);
                
                const main = document.querySelector('main');
                if (main) main.insertBefore(successMessage, main.firstChild);
                window.history.replaceState({}, document.title, window.location.pathname);
            }
        })();
    </script>
</body>
</html>

