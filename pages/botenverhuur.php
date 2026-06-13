<?php
/**
 * Botenverhuur Page - Nijenhuis Botenverhuur
 * Deposit amounts loaded from boats.json (single source of truth)
 */
require_once __DIR__ . '/../components/config.php';
require_once __DIR__ . '/../components/faq_price_helper.php';
require_once __DIR__ . '/../components/schema-botenverhuur-ld.php';
require_once __DIR__ . '/../components/schema-botenverhuur-faq-ld.php';
$basePath = getBasePath();

$faqBoats = faq_load_boats();
$faqDepositBreakdown = faq_get_deposit_breakdown($faqBoats);
$faqDepositSloep = null;
$faqDepositZeilboot = null;
foreach ($faqDepositBreakdown as $b) {
    if ($b['label'] === 'sloepen') $faqDepositSloep = $b['amount'];
    if ($b['label'] === 'zeilboot') $faqDepositZeilboot = $b['amount'];
}

$boatsFile = nijenhuis_data_path('boats.json');
$allBoats = file_exists($boatsFile) ? json_decode(file_get_contents($boatsFile), true) : [];
$boatsById = [];
foreach ($allBoats as $boat) {
    if (!empty($boat['id'])) {
        $boatsById[$boat['id']] = $boat;
    }
}

function boatFleetImagePath(string $relative): string
{
    return ltrim(str_replace(['../', '\\'], ['', '/'], $relative), '/');
}

function boatFleetPrice(array $boat): int
{
    return (int) ($boat['pricePerDay'] ?? 0);
}

$fleetSections = [
    'electric' => [
        'id' => 'electric-boats',
        'badge' => 'boats_fleet_electric_badge',
        'title' => 'boats_fleet_electric_title',
        'desc' => 'boats_fleet_electric_desc',
        'badgeClass' => 'badge-electric',
        'bg' => '#fff',
        'boatIds' => ['classic-tender-720', 'classic-tender-570', 'electrosloop-10', 'electrosloop-8', 'electroboat-5'],
    ],
    'sailing' => [
        'id' => 'sailing-boats',
        'badge' => 'boats_fleet_sail_badge',
        'title' => 'boats_fleet_sail_title',
        'desc' => 'boats_fleet_sail_desc',
        'badgeClass' => 'badge-sail',
        'bg' => '#f8f9fa',
        'boatIds' => ['sailboat-4-5', 'sailpunter-3-4'],
    ],
    'active' => [
        'id' => 'canoes-sups',
        'badge' => 'boats_fleet_active_badge',
        'title' => 'boats_fleet_active_title',
        'desc' => 'boats_fleet_active_desc',
        'badgeClass' => 'badge-active',
        'bg' => '#fff',
        'boatIds' => ['canoe-3', 'kayak-2', 'kayak-1', 'sup-board'],
    ],
];

$pageTitle = 'Bootverhuur Nijenhuis | Alle boten, sloepen & kano\'s';
$pageDescription = 'Bekijk alle boten van Nijenhuis Botenverhuur: luxe sloepen (8–12 pers.), fluisterboten, zeilpunters, kano\'s en SUP boards. Prijzen vanaf €20/dag. Online reserveren.';
$pageKeywords = 'fluisterboot huren giethoorn, luxe sloep huren giethoorn, sloepverhuur giethoorn, bootje huren giethoorn, bootje varen giethoorn, bootverhuur giethoorn, sup giethoorn, kano huren weerribben, kajak huren giethoorn, bootjes verhuur, boten verhuur, verhuur boot, vakantie boot huren';
$headerTitle = 'Onze vloot — sloepen, fluisterboten, kano\'s & meer';
$headerTitleI18n = 'boats_header_h1';
$headerDescription = 'Stap aan boord en ontdek het mooie natuurgebied de Weerribben met onze boten, kano\'s en kajaks!';
$headerDescriptionI18n = 'boats_header_p';
$includeBoatData = true;
$additionalStyles = ['/frontend/css/pages/boats.css'];

$breadcrumbs = [
    ['name' => 'Home', 'url' => '/'],
    ['name' => 'Botenverhuur', 'url' => '/botenverhuur'],
];
?>
<!DOCTYPE html>
<html lang="nl">
<?php include __DIR__ . '/../components/head.php'; ?>
<body data-page="boats">
    <?php include __DIR__ . '/../components/topbar.php'; ?>
    <?php include __DIR__ . '/../components/nav.php'; ?>
    <?php include __DIR__ . '/../components/breadcrumb.php'; ?>
    <?php include __DIR__ . '/../components/page-header.php'; ?>

    <script type="application/ld+json"><?php echo json_encode(schema_botenverhuur_ld(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?></script>

    <main>
        <!-- Intro -->
        <section class="content-section boats-intro-section">
            <div class="container">
                <div class="seo-intro">
                    <h2 data-i18n="boats_intro_title">Ontdek de Weerribben op jouw manier</h2>
                    <p class="boats-intro-summary" data-i18n="boats_bluf_summary">Bootje huren in de Weerribben? Bij Nijenhuis Botenverhuur in Wanneperveen ervaar je de rust en ruimte van Nationaal Park Weerribben-Wieden vanaf het water.</p>
                    <div class="boats-intro-body" data-i18n="boats_intro_text">
                        <p>Bootje huren in de Weerribben? Bij Nijenhuis Botenverhuur in Wanneperveen ervaar je de rust en ruimte van Nationaal Park Weerribben-Wieden vanaf het water. Al meer dan 50 jaar zijn wij het familiebedrijf waar gezinnen, stellen en vriendengroepen terugkomen voor een onvergetelijke dag op het water.</p>
                        <p>Onze locatie aan de Veneweg 199 in Wanneperveen ligt direct aan het water — slechts 10 kilometer van Giethoorn. Dat betekent: geen drukte bij het vertrek, gratis parkeren recht voor de deur, en directe toegang tot de mooiste vaarroutes door het grootste laagveenmoeras van Noordwest-Europa.</p>
                        <p>Of je nu kiest voor een fluisterstille electrosloep voor het hele gezin, een sportieve kano voor twee, of een traditionele zeilpunter — bij ons vind je de perfecte boot voor elke gelegenheid. En het mooiste: je hebt géén vaarbewijs nodig. We geven je voor vertrek een duidelijke uitleg én een gedetailleerde routekaart mee.</p>
                    </div>
                </div>
            </div>
        </section>

        <?php include __DIR__ . '/../components/boat-finder.php'; ?>

        <!-- Fleet -->
        <section class="content-section boat-fleet-overview" id="onze-vloot">
            <div class="container">
                <h2 class="section-title-center" data-i18n="boats_fleet_title">Onze vloot: 25+ boten voor elk gezelschap</h2>
            </div>
        </section>

        <?php foreach ($fleetSections as $section): ?>
        <section class="content-section boat-fleet-section" id="<?php echo htmlspecialchars($section['id']); ?>" style="background: <?php echo htmlspecialchars($section['bg']); ?>;">
            <div class="container">
                <span class="category-badge <?php echo htmlspecialchars($section['badgeClass']); ?>" data-i18n="<?php echo htmlspecialchars($section['badge']); ?>">Meest populair</span>
                <h2 data-i18n="<?php echo htmlspecialchars($section['title']); ?>">Luxe Electrosloepen (Fluisterboten)</h2>
                <p class="boat-fleet-section-desc" data-i18n="<?php echo htmlspecialchars($section['desc']); ?>"></p>

                <div class="boat-fleet-grid">
                    <?php foreach ($section['boatIds'] as $boatId):
                        $boat = $boatsById[$boatId] ?? null;
                        if (!$boat) continue;
                        $imgPath = boatFleetImagePath($boat['image'] ?? '');
                        $price = boatFleetPrice($boat);
                        $cardTitleKey = 'boats_card_title_' . str_replace('-', '_', $boatId);
                        $cardSpecsKey = 'boats_card_specs_' . str_replace('-', '_', $boatId);
                        $altText = htmlspecialchars($boat['name'] ?? $boatId);
                    ?>
                    <article class="boat-card boat-fleet-card reveal-card">
                        <div class="boat-image boat-fleet-image">
                            <?php if ($imgPath): echo responsiveImage(
                                $imgPath,
                                $altText,
                                '(max-width: 768px) 100vw, 33vw',
                                ['width' => '400', 'height' => '200', 'loading' => 'lazy']
                            ); endif; ?>
                            <span class="boat-fleet-card-anchor" aria-hidden="true"></span>
                        </div>
                        <div class="boat-info">
                            <h3 data-i18n="<?php echo htmlspecialchars($cardTitleKey); ?>"><?php echo htmlspecialchars($boat['name'] ?? $boatId); ?></h3>
                            <ul class="boat-card-specs anchor-list" data-i18n="<?php echo htmlspecialchars($cardSpecsKey); ?>"></ul>
                            <p class="boat-price">
                                <span data-i18n="boats_price_from">Vanaf</span>
                                €<?php echo number_format($price, 0, ',', '.'); ?>
                                <span data-i18n="boats_price_per_day">/ dag</span>
                            </p>
                            <a href="/<?php echo htmlspecialchars($boatId); ?>#booking" class="btn btn-sm btn-outline" data-i18n="boats_card_reserve">Reserveer</a>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        <?php endforeach; ?>

        <!-- Why Us -->
        <section class="content-section boats-why-section" id="waarom-nijenhuis" style="background: #f8f9fa;">
            <div class="container">
                <h2 class="section-title-center" data-i18n="boats_why_title">Waarom kiezen voor Nijenhuis Botenverhuur?</h2>
                <div class="why-us-grid">
                    <div class="why-us-card reveal-card">
                        <span class="why-us-anchor" aria-hidden="true"></span>
                        <h3 data-i18n="boats_why_water_title">Direct aan het water</h3>
                        <p data-i18n="boats_why_water_desc">Onze locatie ligt letterlijk aan het water. Je stapt uit de auto, loopt naar de steiger en vaart weg. Geen gedoe met trailers, geen wachtrijen.</p>
                    </div>
                    <div class="why-us-card reveal-card">
                        <span class="why-us-anchor" aria-hidden="true"></span>
                        <h3 data-i18n="boats_why_parking_title">Gratis parkeren</h3>
                        <p data-i18n="boats_why_parking_desc">Bij ons parkeer je altijd gratis, direct bij de verhuurlocatie. In Giethoorn zelf betaal je al snel €10–15 voor parkeren.</p>
                    </div>
                    <div class="why-us-card reveal-card">
                        <span class="why-us-anchor" aria-hidden="true"></span>
                        <h3 data-i18n="boats_why_quiet_title">Rust in plaats van drukte</h3>
                        <p data-i18n="boats_why_quiet_desc">Giethoorn is prachtig, maar in het hoogseizoen ook druk. Door vanuit Wanneperveen te vertrekken mis je de drukte bij het instappen en geniet je direct van de rust op het water. Na 15–20 minuten varen ben je in Giethoorn.</p>
                    </div>
                    <div class="why-us-card reveal-card">
                        <span class="why-us-anchor" aria-hidden="true"></span>
                        <h3 data-i18n="boats_why_service_title">Persoonlijke service</h3>
                        <p data-i18n="boats_why_service_desc">Als familiebedrijf kennen we elke boot en elke vaarroute. We nemen de tijd voor een uitgebreide uitleg en geven je tips voor de mooiste plekjes die niet in de reisgids staan.</p>
                    </div>
                    <div class="why-us-card why-us-card-wide reveal-card">
                        <span class="why-us-anchor" aria-hidden="true"></span>
                        <h3 data-i18n="boats_why_flexible_title">Flexibel huren</h3>
                        <div data-i18n="boats_why_flexible_desc">
                            <ul class="anchor-list">
                                <li><strong>Per dag:</strong> reserveer online of telefonisch</li>
                                <li><strong>Per uur:</strong> kan alleen ter plaatse, voor spontane bezoekers</li>
                                <li><strong>Contant en pin</strong> geaccepteerd</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Routes -->
        <section class="content-section boats-routes-section" id="vaarroutes">
            <div class="container">
                <h2 class="section-title-center" data-i18n="boats_routes_title">Populaire vaarroutes vanaf Wanneperveen</h2>
                <div class="routes-grid">
                    <a href="/vaarkaart" class="destination-card route-card reveal-card route-card-link">
                        <span class="route-anchor" aria-hidden="true"><span class="route-anchor-num">1</span></span>
                        <h3 data-i18n="boats_route_1_title">Route 1: Naar Giethoorn</h3>
                        <p class="route-meta" data-i18n="boats_route_1_meta">8–10 km, 1,5–2 uur enkele reis</p>
                        <p data-i18n="boats_route_1_desc">Vaar via de kanalen richting het beroemde Giethoorn. Bewonder de rietgedekte boerderijen, karakteristieke bruggetjes en het schilderachtige dorpscentrum. Ideaal als dagtrip.</p>
                        <span class="route-card-cta" data-i18n="boats_route_cta">Bekijk op vaarkaart →</span>
                    </a>
                    <a href="/vaarkaart" class="destination-card route-card reveal-card route-card-link">
                        <span class="route-anchor" aria-hidden="true"><span class="route-anchor-num">2</span></span>
                        <h3 data-i18n="boats_route_2_title">Route 2: Belt-Schutsloot</h3>
                        <p class="route-meta" data-i18n="boats_route_2_meta">6–8 km, 1–1,5 uur</p>
                        <p data-i18n="boats_route_2_desc">Het "verborgen Giethoorn" — dezelfde charme, maar zonder de toeristische drukte. Authentieke bruggetjes, historische boerderijen en een rustieke sfeer.</p>
                        <span class="route-card-cta" data-i18n="boats_route_cta">Bekijk op vaarkaart →</span>
                    </a>
                    <a href="/vaarkaart" class="destination-card route-card reveal-card route-card-link">
                        <span class="route-anchor" aria-hidden="true"><span class="route-anchor-num">3</span></span>
                        <h3 data-i18n="boats_route_3_title">Route 3: Weerribben Natuur</h3>
                        <p class="route-meta" data-i18n="boats_route_3_meta">15 km, 3–4 uur</p>
                        <p data-i18n="boats_route_3_desc">Diep het Nationaal Park in. Peddel of vaar door smalle slootjes, ontdek petgaten en spot bijzondere flora en fauna. Ideaal per kano of kajak.</p>
                        <span class="route-card-cta" data-i18n="boats_route_cta">Bekijk op vaarkaart →</span>
                    </a>
                    <a href="/vaarkaart" class="destination-card route-card reveal-card route-card-link">
                        <span class="route-anchor" aria-hidden="true"><span class="route-anchor-num">4</span></span>
                        <h3 data-i18n="boats_route_4_title">Route 4: Beulakerwijde</h3>
                        <p class="route-meta" data-i18n="boats_route_4_meta">10 km, 2–3 uur</p>
                        <p data-i18n="boats_route_4_desc">Het grote meer ten zuiden van Wanneperveen. Open water, prachtige vergezichten en perfecte plek voor zeilen.</p>
                        <span class="route-card-cta" data-i18n="boats_route_cta">Bekijk op vaarkaart →</span>
                    </a>
                </div>
                <p class="routes-footer">
                    <a href="/vaarkaart" data-i18n="boats_routes_map_link">Bekijk onze interactieve vaarkaart voor gedetailleerde routes →</a>
                </p>
                <p class="boats-routes-guides" style="margin-top: 1.5rem; text-align: center;">
                    Meer inspiratie? Lees onze gidsen over
                    <a href="/weerribben">bootje huren in de Weerribben</a>,
                    <a href="/giethoorn">boot huren bij Giethoorn zonder drukte</a>
                    en <a href="/blog/bootje-huren-drenthe">bootje huren in Drenthe</a>.
                </p>
            </div>
        </section>

        <!-- FAQ -->
        <section class="content-section boats-faq-section" style="background: #fcfcfc; border-top: 1px solid #eee;">
            <div class="container boats-faq-container">
                <h2 class="section-title-center" data-i18n="faq_title">Veelgestelde vragen</h2>
                <script type="application/ld+json"><?php echo json_encode(schema_botenverhuur_faq_ld(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?></script>
                <div class="faq-accordion">
                    <details class="faq-item">
                        <summary>
                            <span class="faq-question" data-i18n="boats_faq_q1">Heb ik een vaarbewijs nodig?</span>
                            <span class="faq-icon"></span>
                        </summary>
                        <div class="faq-answer">
                            <p data-i18n="boats_faq_a1">Nee, voor geen van onze boten is een vaarbewijs nodig. Alle boten zijn kleiner dan 15 meter en varen langzamer dan 20 km/u. Je krijgt voor vertrek een persoonlijke instructie.</p>
                        </div>
                    </details>
                    <details class="faq-item">
                        <summary>
                            <span class="faq-question" data-i18n="boats_faq_q2">Hoe ver kan ik varen met een elektrische boot?</span>
                            <span class="faq-icon"></span>
                        </summary>
                        <div class="faq-answer">
                            <p data-i18n="boats_faq_a2">De accu's gaan een hele dag mee bij normaal gebruik. De route naar Giethoorn en terug (±20 km) is geen probleem. Bij aankomst controleren we altijd of de accu volledig is opgeladen.</p>
                        </div>
                    </details>
                    <details class="faq-item">
                        <summary>
                            <span class="faq-question" data-i18n="boats_faq_q3">Wat kost bootje huren bij Nijenhuis?</span>
                            <span class="faq-icon"></span>
                        </summary>
                        <div class="faq-answer">
                            <p data-i18n="boats_faq_a3">Prijzen starten vanaf €20 per dag voor een kano of kajak. Electrosloepen zijn er vanaf €95 per halve dag. Bekijk de volledige prijslijst op onze boekingspagina.</p>
                        </div>
                    </details>
                    <details class="faq-item">
                        <summary>
                            <span class="faq-question" data-i18n="boats_faq_q4">Kan ik een boot huren voor 12 personen?</span>
                            <span class="faq-icon"></span>
                        </summary>
                        <div class="faq-answer">
                            <p data-i18n="boats_faq_a4">Ja, onze Classic Tender 720 is geschikt voor maximaal 12 personen. Voor optimaal comfort raden we 10 personen aan. Bij grotere groepen kun je ook twee boten naast elkaar boeken.</p>
                        </div>
                    </details>
                    <details class="faq-item">
                        <summary>
                            <span class="faq-question" data-i18n="boats_faq_q5">Mag ik mijn hond meenemen?</span>
                            <span class="faq-icon"></span>
                        </summary>
                        <div class="faq-answer">
                            <p data-i18n="boats_faq_a5">Huisdieren zijn toegestaan op de electrosloepen, kano's, de zeilpunter en de electroboot. Op de Classic Tenders zijn huisdieren niet toegestaan.</p>
                        </div>
                    </details>
                    <details class="faq-item">
                        <summary>
                            <span class="faq-question" data-i18n="boats_faq_q6">Wat als het slecht weer is?</span>
                            <span class="faq-icon"></span>
                        </summary>
                        <div class="faq-answer">
                            <p data-i18n="boats_faq_a6">Bij lichte regen kun je gewoon varen — neem regenkleding mee. Bij onweer of storm adviseren we om niet het water op te gaan. Bij extreme omstandigheden kun je gratis omboeken naar een andere datum.</p>
                        </div>
                    </details>
                    <details class="faq-item">
                        <summary>
                            <span class="faq-question" data-i18n="boats_faq_q7">Hoe laat kan ik vertrekken?</span>
                            <span class="faq-icon"></span>
                        </summary>
                        <div class="faq-answer">
                            <p data-i18n="boats_faq_a7">Je kunt vanaf 9:00 uur 's ochtends vertrekken. De laatste verhuurtijden zijn afhankelijk van het seizoen. In de zomer kun je tot 18:00 uur een boot ophalen voor een avondvaart.</p>
                        </div>
                    </details>
                    <details class="faq-item">
                        <summary>
                            <span class="faq-question" data-i18n="boats_faq_q8">Is er parkeergelegenheid?</span>
                            <span class="faq-icon"></span>
                        </summary>
                        <div class="faq-answer">
                            <p data-i18n="boats_faq_a8">Ja, bij Nijenhuis Botenverhuur in Wanneperveen is parkeren helemaal gratis. Je parkeert direct naast de steiger, zodat je meteen het water op kunt.</p>
                        </div>
                    </details>
                </div>
                <p class="faq-all-link">
                    <a href="/veelgestelde-vragen" data-i18n="boats_faq_all_link">Bekijk alle veelgestelde vragen →</a>
                </p>
            </div>
        </section>

        <!-- Fishing -->
        <section class="content-section boat-category-section" id="fishing" style="background: #f8f9fa;">
            <div class="container">
                <h2 data-i18n="boats_fishing_title">Vissen vanaf onze boten in de Weerribben</h2>
                <p data-i18n="boats_fishing_p1">Ontdek waarom de Weerribben een visparadijs in Overijssel is – perfect voor visliefhebbers. Hoewel we bij Nijenhuis Botenverhuur geen gespecialiseerde visboten met tenten of karperboten te huur aanbieden, zijn onze ruime elektrische sloepen en kano's ideaal voor een dagje vissen.</p>
                <p data-i18n="boats_fishing_p2">De stille elektromotoren storen de karpers en roofbleien niet, en met een kajakverhuur in de Weerribben kunt u de rustigste visplekken bereiken waar motorboten niet kunnen komen. Neem uw hengel mee voor een visvakantie in Wanneperveen – <a href="/vaarkaart">bekijk onze vaarkaart</a> voor de beste visplekken in Belterwiede!</p>
            </div>
        </section>

        <!-- CTA -->
        <section class="cta-section boats-cta-section" id="boek-nu">
            <div class="container">
                <div class="boats-cta-inner reveal-card">
                    <span class="boats-cta-anchor" aria-hidden="true">⚓</span>
                    <div class="boats-cta-header">
                        <h2 data-i18n="boats_cta_h2">Boek nu jouw boot</h2>
                        <p class="boats-cta-lead" data-i18n="boats_cta_p">Klaar om de Weerribben te ontdekken? Reserveer vandaag nog je boot en geniet van een onvergetelijke dag op het water bij Giethoorn.</p>
                    </div>
                    <div class="boats-cta-panel">
                        <ul class="boats-cta-list anchor-list" data-i18n="boats_cta_details">
                            <li><strong>Online boeken:</strong> gebruik het reserveringsformulier bovenaan deze pagina</li>
                            <li><strong>Bellen:</strong> <a href="tel:0522281528">0522 281 528</a></li>
                            <li><strong>Bezoek ons:</strong> Veneweg 199, 7946 LP Wanneperveen</li>
                        </ul>
                        <p class="boats-cta-hours" data-i18n="boats_cta_hours">Open van 1 april t/m 31 oktober, dagelijks 09:00–18:00. Geen vaarbewijs nodig. Contant en pin geaccepteerd.</p>
                    </div>
                    <div class="cta-buttons boats-cta-buttons">
                        <a href="/booking" class="btn boats-cta-btn-primary" data-i18n="boats_cta_btn">Nu boeken</a>
                        <a href="tel:<?php echo SITE_PHONE_LINK; ?>" class="btn boats-cta-btn-outline" data-i18n="boats_cta_phone">📞 Bel ons</a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php include __DIR__ . '/../components/footer.php'; ?>

    <div id="availabilityCalendarModal" class="availability-calendar-modal">
        <div class="calendar-modal-content">
            <button class="calendar-modal-close" id="calendarModalClose">&times;</button>
            <div class="calendar-header">
                <h2 id="calendarBoatName">Boot beschikbaarheid</h2>
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
            <div class="calendar-grid" id="calendarGrid"></div>
            <div class="calendar-selection-info" id="selectionInfo" style="display:none;">
                <p style="margin: 0; font-weight: 600;">Geselecteerde periode:</p>
                <p id="selectionRangeText" style="margin: 5px 0;">-</p>
                <p id="selectionPriceText" style="margin: 5px 0; font-size: 1.1em; color: var(--primary-color);">Totaal: €0</p>
            </div>
            <div id="selectionError" style="display:none; margin-top: 10px; color: #c62828; text-align: center; font-size: 0.9em;">
                De geselecteerde periode bevat niet-beschikbare dagen.
            </div>
            <div id="boatOptions" class="calendar-options"></div>
            <div class="calendar-actions">
                <a class="btn btn-primary disabled" id="bookSelectedBoat" href="#">🛒 Reserveer Nu</a>
            </div>
        </div>
    </div>

    <script src="<?php echo assetPath('js/boat-data-service.js'); ?>"></script>
    <script src="<?php echo assetPath('frontend/src/js/hooks/useBoatData.js'); ?>"></script>
    <script src="<?php echo assetPath('frontend/src/js/hooks/useBookingAvailability.js'); ?>"></script>
    <script src="<?php echo assetPath('frontend/src/js/pages/boats.js'); ?>"></script>
</body>
</html>
