<?php
/**
 * Weerribben-Wieden landing page - Nijenhuis Botenverhuur
 * SEO page for lower-competition Weerribben boat rental intent.
 */
require_once __DIR__ . '/../components/config.php';
$basePath = getBasePath();

$pageTitle = 'Bootje huren Weerribben-Wieden';
$pageTitleFull = 'Bootje huren Weerribben-Wieden | Sloep, fluisterboot & kano huren';
$pageDescription = 'Bootje huren in de Weerribben-Wieden ✔️ Rustig vertrek vanuit Wanneperveen ✔️ Sloep, fluisterboot, kano of SUP ✔️ Geen vaarbewijs nodig ✔️ Vanaf €20/dag.';
$pageKeywords = 'bootje huren weerribben, boot huren weerribben, sloep huren weerribben, fluisterboot huren weerribben, kano huren weerribben, weerribben-wieden varen, varen weerribben, bootje huren weerribben-wieden';
$headerTitle = 'Bootje huren in de Weerribben-Wieden';
$headerTitleI18n = 'weerribben_title';
$headerDescription = 'Vaar door rietlanden, stille sloten en open meren vanuit Wanneperveen';
$headerDescriptionI18n = 'weerribben_description';
$breadcrumbs = [
    ['name' => 'Home', 'url' => '/'],
    ['name' => 'Weerribben-Wieden', 'url' => '/weerribben'],
];
$additionalStyles = ['/frontend/css/pages/destination-pages.css?v=3', '/frontend/css/pages/boats.css'];
?>
<!DOCTYPE html>
<html lang="nl">
<?php include __DIR__ . '/../components/head.php'; ?>

<body data-page="weerribben">
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": ["Place", "NaturalFeature", "TouristAttraction"],
    "@id": "https://nijenhuis-botenverhuur.com/weerribben#place",
    "name": "Nationaal Park Weerribben-Wieden",
    "alternateName": ["Weerribben", "Weerribben-Wieden"],
    "description": "Nationaal Park Weerribben-Wieden is een waterrijk natuurgebied in Overijssel met rietlanden, meren, smalle vaarten en rustige vaarroutes. Nijenhuis Botenverhuur in Wanneperveen is een rustig vertrekpunt voor boottochten door dit gebied.",
    "url": "https://nijenhuis-botenverhuur.com/weerribben",
    "image": "https://nijenhuis-botenverhuur.com/frontend/Images/Wanneperveen/beulakerwijde-view.jpg",
    "geo": {
        "@type": "GeoCoordinates",
        "latitude": 52.697269,
        "longitude": 6.077958
    },
    "containedInPlace": {
        "@type": "AdministrativeArea",
        "name": "Overijssel"
    },
    "touristType": ["NatureLover", "FamilyTourist", "OutdoorEnthusiast"],
    "keywords": "bootje huren weerribben, sloep huren weerribben, fluisterboot huren weerribben, kano huren weerribben, varen weerribben-wieden"
}
</script>
    <?php include __DIR__ . '/../components/topbar.php'; ?>
    <?php include __DIR__ . '/../components/nav.php'; ?>
    <?php include __DIR__ . '/../components/breadcrumb.php'; ?>
    <?php include __DIR__ . '/../components/page-header.php'; ?>

    <main>
        <section class="content-section">
            <div class="container">
                <div class="destination-content">
                    <div class="destination-intro">
                        <div class="content-prose">
                            <p>
                                Wil je een <strong>bootje huren in de Weerribben-Wieden</strong>? Vanuit Nijenhuis Botenverhuur
                                in Wanneperveen vaar je direct het rustige water op. Geen vertrekstress in het centrum van
                                Giethoorn, maar gratis parkeren bij de steiger, persoonlijke uitleg en alle ruimte om het
                                grootste laagveenmoeras van Noordwest-Europa te ontdekken.
                            </p>
                            <p>
                                De Weerribben-Wieden is ideaal voor een dag varen met een fluisterboot, sloep, kano, kajak,
                                zeilpunter of SUP. Je vaart door rietkragen, petgaten, stille slootjes en open meren, met
                                onderweg routes naar Giethoorn, Belt-schutsloot en de Beulakerwijde.
                            </p>
                        </div>
                    </div>

                    <h2 class="section-title" style="margin-bottom: 1rem;">Weerribben-Wieden in het kort</h2>
                    <div class="destination-stats-grid">
                        <div class="facility-card">
                            <div class="facility-icon">🌿</div>
                            <h3>Natuurgebied</h3>
                            <p>Nationaal Park</p>
                        </div>
                        <div class="facility-card">
                            <div class="facility-icon">🚤</div>
                            <h3>Vaarbewijs</h3>
                            <p>Niet nodig</p>
                        </div>
                        <div class="facility-card">
                            <div class="facility-icon">🅿️</div>
                            <h3>Vertrek</h3>
                            <p>Gratis parkeren</p>
                        </div>
                        <div class="facility-card">
                            <div class="facility-icon">📍</div>
                            <h3>Startpunt</h3>
                            <p>Wanneperveen</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="content-section bg-secondary">
            <div class="container">
                <div class="destination-content">
                    <div class="section-title">
                        <h2>Welke boot huur je voor de Weerribben?</h2>
                        <p>Kies het type boot dat past bij je groep, tempo en route.</p>
                    </div>

                    <div class="facilities-grid">
                        <div class="facility-card">
                            <div class="facility-icon">⚡</div>
                            <h3>Fluisterboot of electrosloep</h3>
                            <p>
                                De populairste keuze voor gezinnen en groepen. Stil, comfortabel en eenvoudig te besturen.
                                Ideaal voor een ontspannen route door de Weerribben-Wieden of richting Giethoorn.
                            </p>
                            <a href="/electrosloop-8#booking">Bekijk electrosloep 8 pers →</a><br>
                            <a href="/electrosloop-10#booking">Bekijk electrosloep 10 pers →</a>
                        </div>
                        <div class="facility-card">
                            <div class="facility-icon">🛶</div>
                            <h3>Kano of kajak</h3>
                            <p>
                                Voor wie dicht bij de natuur wil komen. Met een kano of kajak kun je rustig door smalle
                                vaarten peddelen en kom je op plekken waar grotere boten niet komen.
                            </p>
                            <a href="/canoe-3#booking">Bekijk Canadese kano →</a><br>
                            <a href="/kayak-2#booking">Bekijk kajak 2 pers →</a>
                        </div>
                        <div class="facility-card">
                            <div class="facility-icon">⛵</div>
                            <h3>Zeilpunter of zeilboot</h3>
                            <p>
                                Een traditionele manier om het gebied te beleven. De zeilpunter past bij de historie van de
                                Weerribben en is geschikt voor ervaren zeilers die rustig willen varen.
                            </p>
                            <a href="/sailpunter-3-4#booking">Bekijk zeilpunter →</a><br>
                            <a href="/sailboat-4-5#booking">Bekijk zeilboot →</a>
                        </div>
                        <div class="facility-card">
                            <div class="facility-icon">🏄</div>
                            <h3>SUP board</h3>
                            <p>
                                Sportief en stil. Een SUP is vooral leuk bij rustig weer en voor kortere routes rond
                                Wanneperveen en de beschutte wateren in de buurt.
                            </p>
                            <a href="/sup-board#booking">Bekijk SUP board →</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="content-section">
            <div class="container">
                <div class="destination-content">
                    <div class="section-title">
                        <h2>Waarom starten vanuit Wanneperveen?</h2>
                    </div>

                    <div class="destination-card">
                        <div class="destination-split">
                            <div class="content-prose">
                                <p>
                                    Wanneperveen ligt direct aan de vaarwegen van de Weerribben-Wieden. Dat maakt het een
                                    praktisch startpunt voor bezoekers die natuur, rust en vrijheid zoeken. Je hoeft niet eerst
                                    door drukke winkelstraten of volle parkeerterreinen, maar begint direct aan het water.
                                </p>
                                <ul class="destination-feature-list">
                                    <li>✅ <strong>Rustig vertrekpunt</strong> buiten het drukste deel van Giethoorn</li>
                                    <li>✅ <strong>Gratis parkeren</strong> bij Nijenhuis Botenverhuur</li>
                                    <li>✅ <strong>Geen vaarbewijs nodig</strong> voor vrijwel alle huurboten</li>
                                    <li>✅ <strong>Routekaart en uitleg</strong> voor vertrek</li>
                                    <li>✅ <strong>Veel keuze</strong>: korte rondes, dagtochten, Giethoorn of juist natuur</li>
                                </ul>
                                <p>
                                    Wil je toch naar Giethoorn varen? Bekijk dan onze specifieke pagina voor
                                    <a href="/giethoorn">boot huren bij Giethoorn zonder drukte</a>.
                                </p>
                            </div>
                            <div class="destination-split__img-wrap">
                                <?php echo responsiveImage(
                                    'frontend/Images/Wanneperveen/beulakerwijde-view.jpg',
                                    'Uitzicht over de Beulakerwijde in de Weerribben-Wieden',
                                    '(max-width: 768px) 100vw, 50vw'
                                ); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="content-section bg-secondary">
            <div class="container">
                <div class="destination-content">
                    <div class="section-title">
                        <h2>Populaire vaarroutes in de Weerribben-Wieden</h2>
                    </div>

                    <div class="facilities-grid">
                        <div class="facility-card">
                            <h3>Weerribben natuurroute</h3>
                            <p>
                                Een rustige tocht door rietlanden, petgaten en smalle watergangen. Ideaal met fluisterboot,
                                kano of kajak als je vooral natuur wilt ervaren.
                            </p>
                            <a href="/vaarkaart">Bekijk route op de vaarkaart →</a>
                        </div>
                        <div class="facility-card">
                            <h3>Giethoorn en terug</h3>
                            <p>
                                Vaar vanuit Wanneperveen naar het bekende waterdorp, maar begin en eindig je dag op een
                                rustiger plek. Vooral prettig in het hoogseizoen.
                            </p>
                            <a href="/giethoorn">Meer over varen naar Giethoorn →</a>
                        </div>
                        <div class="facility-card">
                            <h3>Belt-schutsloot</h3>
                            <p>
                                Een authentiek alternatief voor Giethoorn met grachten, bruggetjes en veel minder drukte.
                                Goed te combineren met een rustige route door de Weerribben.
                            </p>
                            <a href="/belt-schutsloot">Ontdek Belt-schutsloot →</a>
                        </div>
                        <div class="facility-card">
                            <h3>Beulakerwijde</h3>
                            <p>
                                Open water, brede uitzichten en veel ruimte. Een fijne route voor wie een dag ontspannen wil
                                varen en het landschap vanaf het water wil zien.
                            </p>
                            <a href="/vaarkaart">Bekijk onze vaarkaart →</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="content-section faq-section">
            <div class="container" style="max-width: 800px;">
                <h2 style="text-align: center; margin-bottom: 2rem;">Veelgestelde vragen over bootje huren in de Weerribben</h2>

                <script type="application/ld+json">
                {
                    "@context": "https://schema.org",
                    "@type": "FAQPage",
                    "mainEntity": [
                        {
                            "@type": "Question",
                            "name": "Heb ik een vaarbewijs nodig in de Weerribben-Wieden?",
                            "acceptedAnswer": {
                                "@type": "Answer",
                                "text": "Nee, voor vrijwel alle huurboten van Nijenhuis Botenverhuur heb je geen vaarbewijs nodig. Voor vertrek krijg je uitleg over de boot en de vaarregels."
                            }
                        },
                        {
                            "@type": "Question",
                            "name": "Kan ik vanuit Wanneperveen naar Giethoorn varen?",
                            "acceptedAnswer": {
                                "@type": "Answer",
                                "text": "Ja, vanuit Wanneperveen kun je naar Giethoorn varen. Je start rustiger dan in het centrum en vaart via de Weerribben-Wieden richting het dorp."
                            }
                        },
                        {
                            "@type": "Question",
                            "name": "Welke boot is het beste voor de Weerribben?",
                            "acceptedAnswer": {
                                "@type": "Answer",
                                "text": "Voor gezinnen en groepen is een electrosloep of fluisterboot het meest comfortabel. Voor natuurliefhebbers zijn kano's en kajaks populair omdat je stiller en dichter bij de rietlanden vaart."
                            }
                        },
                        {
                            "@type": "Question",
                            "name": "Waar kan ik parkeren?",
                            "acceptedAnswer": {
                                "@type": "Answer",
                                "text": "Bij Nijenhuis Botenverhuur in Wanneperveen kun je gratis parkeren bij de verhuurlocatie aan de Veneweg 199."
                            }
                        }
                    ]
                }
                </script>

                <div class="faq-accordion">
                    <details class="faq-item">
                        <summary><span class="faq-question">Heb ik een vaarbewijs nodig?</span><span class="faq-icon"></span></summary>
                        <p>Nee, voor vrijwel alle huurboten heb je geen vaarbewijs nodig. We geven je voor vertrek duidelijke uitleg.</p>
                    </details>
                    <details class="faq-item">
                        <summary><span class="faq-question">Kan ik naar Giethoorn varen?</span><span class="faq-icon"></span></summary>
                        <p>Ja. Je vaart vanuit Wanneperveen via de Weerribben-Wieden richting Giethoorn en start buiten de drukste vertrekpunten.</p>
                    </details>
                    <details class="faq-item">
                        <summary><span class="faq-question">Welke boot past het beste bij natuur varen?</span><span class="faq-icon"></span></summary>
                        <p>Een fluisterboot is comfortabel en stil. Wil je dichter bij de natuur komen, kies dan een kano of kajak.</p>
                    </details>
                    <details class="faq-item">
                        <summary><span class="faq-question">Kan ik online reserveren?</span><span class="faq-icon"></span></summary>
                        <p>Ja, daghuur kun je online reserveren. Uurhuur kan alleen ter plaatse bij de verhuurlocatie.</p>
                    </details>
                </div>
            </div>
        </section>

        <section class="cta-section" style="background: var(--secondary-color); color: white; padding: 4rem 0;">
            <div class="container">
                <div class="cta-content">
                    <h2 style="color: white; margin-bottom: 1rem;">Klaar om de Weerribben op te varen?</h2>
                    <p class="content-prose" style="margin-bottom: 2rem; opacity: 0.95;">
                        Reserveer online of bekijk eerst welke boot bij jouw groep en route past.
                    </p>
                    <div class="cta-buttons">
                        <a href="/botenverhuur" class="btn-cta-primary">Bekijk alle boten</a>
                        <a href="/booking" class="btn-cta-outline">Direct boeken</a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php include __DIR__ . '/../components/footer.php'; ?>
</body>
</html>
