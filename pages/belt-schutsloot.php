<?php
/**
 * Belt-schutsloot Page - Nijenhuis Botenverhuur
 * Comprehensive guide about Belt-schutsloot, the hidden gem alternative to Giethoorn
 * Optimized for SEO and AI search
 */
require_once __DIR__ . '/../components/config.php';
$basePath = getBasePath();
$pageTitle = 'Belt-schutsloot | Alternatief Giethoorn';
$pageDescription = 'Ontdek Belt-schutsloot, het rustiger alternatief voor Giethoorn. Idyllische grachten, bruggetjes en rietgedekte huizen. Boot huren bij Nijenhuis Botenverhuur.';
$pageKeywords = 'belt-schutsloot, belt schutsloot, belt-schutsloot bezoeken, alternatief giethoorn, verborgen parel weerribben, belt-schutsloot boot huren, minder toeristisch dan giethoorn, belt-schutsloot grachten, weerribben dorpen';
$headerTitle = 'Belt-schutsloot';
$headerTitleI18n = 'belt_schutsloot_title';
$headerDescription = 'Ontdek het verborgen parel Belt-schutsloot: rustiger dan Giethoorn, maar met dezelfde charme';
$headerDescriptionI18n = 'belt_schutsloot_description';

// Breadcrumbs for this page
$breadcrumbs = [
    ['name' => 'Home', 'url' => '/'],
    ['name' => 'Belt-schutsloot', 'url' => '/belt-schutsloot']
];
$additionalStyles = ['/frontend/css/pages/destination-pages.css?v=2', '/frontend/css/pages/boats.css'];
?>
<!DOCTYPE html>
<html lang="nl">
<?php include __DIR__ . '/../components/head.php'; ?>

<body data-page="belt-schutsloot">
    <?php include __DIR__ . '/../components/gtm-body.php'; ?>
<!-- Place Schema.org Structured Data for Belt-schutsloot -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": ["Place", "TouristAttraction", "City"],
    "@id": "https://nijenhuis-botenverhuur.com/belt-schutsloot#place",
    "name": "Belt-schutsloot",
    "alternateName": ["Beltschutsloot", "Belt Schutsloot"],
    "description": "Belt-schutsloot is een charmant dorp in de Nederlandse provincie Overijssel, gelegen in Nationaal Park Weerribben-Wieden. Het dorp heeft dezelfde idyllische sfeer als Giethoorn met grachten en bruggetjes, maar is minder toeristisch en rustiger.",
    "url": "https://nijenhuis-botenverhuur.com/belt-schutsloot",
    "image": "https://upload.wikimedia.org/wikipedia/commons/thumb/6/64/20131022_Belt_Schutsloot.jpg/1280px-20131022_Belt_Schutsloot.jpg",
    "geo": {
        "@type": "GeoCoordinates",
        "latitude": 52.6778,
        "longitude": 6.0611
    },
    "address": {
        "@type": "PostalAddress",
        "addressLocality": "Belt-schutsloot",
        "addressRegion": "Overijssel",
        "addressCountry": "NL",
        "postalCode": "8356"
    },
    "containedInPlace": {
        "@type": "NaturalFeature",
        "name": "Nationaal Park Weerribben-Wieden"
    },
    "touristType": ["CulturalTourist", "NatureLover", "FamilyTourist"],
    "keywords": "belt-schutsloot, verborgen parel, alternatief giethoorn, weerribben, grachten, bruggetjes, boot huren, minder toeristisch"
}
</script>
    <?php include __DIR__ . '/../components/topbar.php'; ?>
    <?php include __DIR__ . '/../components/nav.php'; ?>
    <?php include __DIR__ . '/../components/breadcrumb.php'; ?>
    <?php include __DIR__ . '/../components/page-header.php'; ?>

    <main>
        <!-- Introduction Section -->
        <section class="content-section">
            <div class="container">
                <div class="destination-content">
                    <div class="destination-intro">
                        <div class="content-prose">
                            <p>
                                Ontdek een <strong>verborgen parel</strong> in de Weerribben: Belt-schutsloot heeft dezelfde 
                                idyllische charme als Giethoorn, maar zonder de drukte. Perfect voor wie op zoek is naar 
                                <strong>rust en authenticiteit</strong>.
                            </p>
                        </div>
                    </div>

                    <div class="destination-card">
                        <div class="section-title section-title--left">
                            <h2>Waarom Belt-schutsloot bezoeken?</h2>
                        </div>
                        <div class="destination-split">
                            <div class="content-prose">
                                <p>
                                    Belt-schutsloot is een klein, pittoresk dorpje gelegen op slechts enkele kilometers van Giethoorn, 
                                    midden in het prachtige <strong>Nationaal Park Weerribben-Wieden</strong>. Het dorp heeft alles wat 
                                    Giethoorn beroemd maakt gemaakt: grachten, karakteristieke bruggetjes, rietgedekte boerderijen en 
                                    een idyllische watersfeer. Het grote verschil? Belt-schutsloot is <strong>veel rustiger en minder toeristisch</strong>, 
                                    waardoor je de authentieke sfeer van de Weerribben in alle rust kunt ervaren.
                                </p>
                                <p>
                                    Voor bezoekers die de drukte van Giethoorn willen vermijden maar toch willen genieten van dezelfde 
                                    unieke sfeer, is Belt-schutsloot de perfecte keuze. Het dorp biedt een <strong>authentieke en rustige</strong> 
                                    ervaring, waarbij je op je gemak kunt varen door de smalle slootjes en kunt genieten van de prachtige 
                                    natuur en architectuur.
                                </p>
                            </div>
                            <div>
                                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/64/20131022_Belt_Schutsloot.jpg/1280px-20131022_Belt_Schutsloot.jpg" 
                                     alt="Rustige grachten in Belt-schutsloot met traditionele Nederlandse architectuur" 
                                     loading="lazy">
                            </div>
                        </div>
                    </div>

                    <div class="facilities-grid facilities-grid--2x2">
                        <div class="facility-card">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/64/20131022_Belt_Schutsloot.jpg/1280px-20131022_Belt_Schutsloot.jpg" 
                                 alt="Grachten en waterwegen in Belt-schutsloot" 
                                 loading="lazy">
                            <div class="facility-icon">🌊</div>
                            <h3>Grachten &amp; waterwegen</h3>
                            <p>
                                Net als Giethoorn heeft Belt-schutsloot prachtige grachten en waterwegen die perfect zijn voor 
                                het varen met bootjes, kano's en punters.
                            </p>
                        </div>

                        <div class="facility-card">
                            <?php echo responsiveImage(
                                'frontend/Images/belterwijde.jpg',
                                'Karakteristieke bruggetjes in Belt-schutsloot, Weerribben',
                                '(max-width: 768px) 100vw, (max-width: 1024px) 50vw, 25vw'
                            ); ?>
                            <div class="facility-icon">🌉</div>
                            <h3>Karakteristieke bruggetjes</h3>
                            <p>
                                Charmante bruggetjes verbinden de eilandjes waarop de traditionele rietgedekte huizen staan, 
                                precies zoals in Giethoorn.
                            </p>
                        </div>

                        <div class="facility-card">
                            <div class="facility-icon">🏡</div>
                            <h3>Rietgedekte huizen</h3>
                            <p>
                                De karakteristieke boerderijen met rietgedekte daken geven Belt-schutsloot dezelfde 
                                authentieke uitstraling als Giethoorn.
                            </p>
                        </div>

                        <div class="facility-card facility-card--highlight">
                            <div class="facility-icon">✨</div>
                            <h3>Rust &amp; authenticiteit</h3>
                            <p>
                                Het grootste voordeel: Belt-schutsloot is veel rustiger dan Giethoorn, waardoor je 
                                in alle rust van de sfeer kunt genieten.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Why Choose Belt-schutsloot Section -->
        <section class="content-section bg-secondary">
            <div class="container">
                <div class="destination-content">
                    <div class="section-title">
                        <h2>Belt-schutsloot vs Giethoorn: waarom dit verborgen parel?</h2>
                    </div>
                    
                    <div class="facilities-grid">
                        <div class="facility-card">
                            <div class="facility-icon">🌿</div>
                            <h3>Minder toeristisch</h3>
                            <p>
                                Belt-schutsloot trekt veel minder toeristen aan dan Giethoorn, vooral in het hoogseizoen. 
                                Dit betekent meer ruimte om te varen, rustiger vaarroutes en een authentiekere ervaring 
                                zonder de drukte van grote groepen bezoekers.
                            </p>
                        </div>

                        <div class="facility-card">
                            <div class="facility-icon">⏱️</div>
                            <h3>Kortere afstand</h3>
                            <p>
                                Vanaf Nijenhuis Botenverhuur in Wanneperveen is Belt-schutsloot <strong>sneller bereikbaar</strong> 
                                dan Giethoorn. Je kunt dus meer tijd besteden aan het verkennen en genieten, in plaats van 
                                onderweg zijn.
                            </p>
                        </div>

                        <div class="facility-card">
                            <div class="facility-icon">🎯</div>
                            <h3>Dezelfde charme</h3>
                            <p>
                                Belt-schutsloot heeft alle elementen die Giethoorn zo bijzonder maken: grachten, bruggetjes, 
                                rietgedekte huizen en een idyllische watersfeer. Je krijgt dezelfde ervaring, maar dan 
                                in een rustigere setting.
                            </p>
                        </div>

                        <div class="facility-card">
                            <div class="facility-icon">🔍</div>
                            <h3>Verborgen parel</h3>
                            <p>
                                Ontdek een plek die nog niet door iedereen is ontdekt. Belt-schutsloot is perfect voor 
                                avonturiers die op zoek zijn naar iets unieks en bijzonders, weg van de gebaande paden.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Route Section -->
        <section class="content-section">
            <div class="container">
                <div class="destination-content">
                    <div class="section-title">
                        <h2>Vaarroute naar Belt-schutsloot vanuit Wanneperveen</h2>
                    </div>
                    
                    <div class="destination-card route-info">
                        <h3>📍 Vanaf Nijenhuis Botenverhuur</h3>
                        <div class="destination-split">
                            <div class="content-prose">
                                <p>
                                    Vanaf onze bootverhuur in <a href="/wanneperveen" style="color: var(--primary-color); font-weight: 500;">Wanneperveen</a> (Veneweg 199) is het ongeveer <strong>6-8 kilometer varen</strong> 
                                    naar Belt-schutsloot. Dit is <strong>korter dan de route naar Giethoorn</strong> (10 km), waardoor je 
                                    meer tijd hebt om het dorp te verkennen en te genieten van de vaarroute door het prachtige 
                                    <strong>Nationaal Park Weerribben-Wieden</strong>.
                                </p>
                            </div>
                            <div>
                                <?php echo responsiveImage(
                                    'frontend/Images/belterwijde.jpg',
                                    'Vaarroute naar Belt-schutsloot door de Weerribben natuur',
                                    '(max-width: 768px) 100vw, 50vw'
                                ); ?>
                            </div>
                        </div>
                        
                        <div class="route-details">
                            <h4>Route details</h4>
                            <ul>
                                <li>✅ <strong>Afstand:</strong> 6-8 km (enkele reis)</li>
                                <li>✅ <strong>Duur:</strong> 1-1,5 uur enkele reis</li>
                                <li>✅ <strong>Moeilijkheidsgraad:</strong> Gemakkelijk</li>
                                <li>✅ <strong>Landschap:</strong> Weerribben natuurgebied</li>
                            </ul>
                        </div>

                        <p>
                            De vaarroute naar Belt-schutsloot gaat door het schitterende natuurgebied van de Weerribben, langs 
                            rietvelden, moerassen en kleine eilandjes. Onderweg kun je genieten van de rustige omgeving en 
                            diverse vogelsoorten spotten. Eenmaal aangekomen in Belt-schutsloot kun je op je gemak door de 
                            smalle slootjes varen en de karakteristieke architectuur bewonderen.
                        </p>

                        <p style="margin-top: 1rem;">
                            <a href="/vaarkaart">Bekijk onze interactieve vaarkaart voor gedetailleerde routes →</a>
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Boat Rental Section (Option A: cards on white, CTA below) -->
        <section class="content-section">
            <div class="container">
                <div class="destination-content">
                    <div class="section-title">
                        <h2>Boot huren voor Belt-schutsloot</h2>
                        <p>Bij Nijenhuis Botenverhuur in Wanneperveen kun je verschillende boten huren om naar het verborgen parel Belt-schutsloot te varen.</p>
                    </div>
                    
                    <div class="facilities-grid">
                        <div class="facility-card">
                            <div class="facility-icon">⚡</div>
                            <h3>Electrosloepen</h3>
                            <p>
                                Fluisterstille elektrische sloepen voor 8 of 10 personen. Perfect voor een comfortabele tocht 
                                naar Belt-schutsloot met het hele gezin of vriendengroep.
                            </p>
                            <a href="/electrosloop-8#booking">Bekijk electrosloep 8 pers →</a><br>
                            <a href="/electrosloop-10#booking">Bekijk electrosloep 10 pers →</a>
                        </div>

                        <div class="facility-card">
                            <div class="facility-icon">🛶</div>
                            <h3>Kano's &amp; kajaks</h3>
                            <p>
                                Voor een actieve en intieme ervaring: peddel door de smalle slootjes van Belt-schutsloot 
                                met een kano of kajak. Perfect voor avonturiers!
                            </p>
                            <a href="/canoe-3#booking">Bekijk Canadese kano 3 pers →</a>
                        </div>

                        <div class="facility-card">
                            <div class="facility-icon">⛵</div>
                            <h3>Zeilpunters &amp; zeilboten</h3>
                            <p>
                                Beleef de authentieke sfeer van de Weerribben met een traditionele zeilpunter of zeilboot. 
                                Perfect voor het varen door de smalle grachten van Belt-schutsloot.
                            </p>
                            <a href="/sailpunter-3-4#booking">Bekijk zeilpunter 3/4 pers →</a><br>
                            <a href="/sailboat-4-5#booking">Bekijk zeilboot 4/5 pers →</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Boat Rental CTA -->
        <section class="cta-section" style="background: var(--secondary-color); color: white; padding: 4rem 0;">
            <div class="container">
                <div class="cta-content">
                    <p class="content-prose" style="margin-bottom: 2rem; opacity: 0.95;">
                        Onze locatie in <a href="/wanneperveen" style="color: white; text-decoration: underline; font-weight: 500;">Wanneperveen</a> ligt ideaal tussen Giethoorn en Belt-schutsloot, waardoor je gemakkelijk beide dorpen kunt bezoeken tijdens je vaartocht.
                    </p>
                    <div class="cta-buttons">
                        <a href="/electrosloop-8#booking" class="btn-cta-primary">Bekijk electrosloep 8 pers</a>
                        <a href="/booking" class="btn-cta-outline">Direct boeken</a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Tips Section -->
        <section class="content-section bg-secondary">
            <div class="container">
                <div class="destination-content">
                    <div class="section-title">
                        <h2>Tips voor je bezoek aan Belt-schutsloot</h2>
                    </div>
                    
                    <div class="facilities-grid">
                        <div class="facility-card">
                            <div class="facility-icon">🌅</div>
                            <h3>Beste tijd</h3>
                            <p>
                                Plan je bezoek in de ochtend of late middag voor het mooiste licht en de rustigste sfeer. 
                                Belt-schutsloot is het hele jaar door mooi, maar het voorjaar en de vroege zomer bieden 
                                de meest idyllische omstandigheden.
                            </p>
                        </div>

                        <div class="facility-card">
                            <div class="facility-icon">📸</div>
                            <h3>Fotografie</h3>
                            <p>
                                Belt-schutsloot is perfect voor fotografie: de rustige omgeving betekent dat je de tijd hebt 
                                om mooie composities te maken zonder dat andere bezoekers in beeld komen. De grachten, 
                                bruggetjes en rietgedekte huizen zijn prachtige onderwerpen.
                            </p>
                        </div>

                        <div class="facility-card">
                            <div class="facility-icon">🚣</div>
                            <h3>Combineer met Giethoorn</h3>
                            <p>
                                Wil je beide dorpen bezoeken? Belt-schutsloot ligt op slechts enkele kilometers van Giethoorn. 
                                Je kunt een dagtocht maken waarbij je beide dorpen bezoekt, of Belt-schutsloot als rustpunt 
                                gebruiken tijdens een tocht naar Giethoorn.
                            </p>
                        </div>

                        <div class="facility-card">
                            <div class="facility-icon">🌿</div>
                            <h3>Natuur genieten</h3>
                            <p>
                                Neem de tijd om te genieten van de natuur rondom Belt-schutsloot. De Weerribben bieden 
                                prachtige mogelijkheden voor vogelspotting en natuurobservatie. Houd je ogen open voor 
                                aalscholvers, reigers en andere watervogels.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Comparison with Giethoorn -->
        <section class="content-section comparison-section">
            <div class="container">
                <div class="destination-content">
                    <div class="section-title">
                        <h2>Belt-schutsloot of Giethoorn?</h2>
                        <p>Waarom kiezen tussen beide? Ontdek beide dorpen tijdens je verblijf in de Weerribben!</p>
                    </div>
                    
                    <div class="comparison-box comparison-box--prose">
                        <p>
                            <strong>Belt-schutsloot</strong> is perfect voor wie op zoek is naar rust en authenticiteit. 
                            Het dorp biedt dezelfde charmante sfeer als Giethoorn, maar dan in een rustigere, intiemere setting. 
                            Perfect voor romantische uitjes, fotografie of gewoon om in alle rust te genieten van de unieke 
                            sfeer van de Weerribben.
                        </p>
                        <p style="margin-top: 1rem;">
                            <strong>Giethoorn</strong> is ideaal voor wie de beroemde bezienswaardigheden wil zien en 
                            niet bang is voor wat meer drukte. Het dorp heeft meer faciliteiten, restaurants en winkeltjes, 
                            en is perfect voor families en groepen.
                        </p>
                        <p style="margin-top: 1rem; font-weight: 500; color: var(--primary-color);">
                            💡 Tip: Bezoek beide! Start met Belt-schutsloot voor rust en authenticiteit, en vaar daarna 
                            door naar Giethoorn om het beroemde dorp te ervaren. Of andersom: bezoek eerst Giethoorn en 
                            zoek daarna de rust op in Belt-schutsloot.
                        </p>
                    </div>
                    
                    <div style="text-align: center;">
                        <a href="/giethoorn" class="btn-primary-link">Lees meer over Giethoorn →</a>
                    </div>
                </div>
            </div>
        </section>

        <!-- FAQ Section -->
        <section class="content-section" style="background: #fcfcfc; border-top: 1px solid #eee;">
            <div class="container" style="max-width: 800px;">
                <h2 style="text-align: center; margin-bottom: 2rem;">Veelgestelde vragen over Belt-schutsloot</h2>
                
                <!-- FAQ Structured Data -->
                <script type="application/ld+json">
                {
                    "@context": "https://schema.org",
                    "@type": "FAQPage",
                    "mainEntity": [
                        {
                            "@type": "Question",
                            "name": "Waarom is Belt-schutsloot minder toeristisch dan Giethoorn?",
                            "acceptedAnswer": {
                                "@type": "Answer",
                                "text": "Belt-schutsloot is minder bekend bij internationale toeristen dan Giethoorn, waardoor het veel rustiger is. Het dorp heeft dezelfde charmante sfeer met grachten, bruggetjes en rietgedekte huizen, maar trekt veel minder bezoekers aan. Dit maakt het perfect voor wie op zoek is naar een authentieke en rustige ervaring."
                            }
                        },
                        {
                            "@type": "Question",
                            "name": "Hoe ver is Belt-schutsloot vanaf Wanneperveen?",
                            "acceptedAnswer": {
                                "@type": "Answer",
                                "text": "Vanaf Nijenhuis Botenverhuur in Wanneperveen is Belt-schutsloot ongeveer 6-8 kilometer varen, wat korter is dan de route naar Giethoorn (10 km). De tocht duurt ongeveer 1 tot 1,5 uur enkele reis, afhankelijk van het type boot."
                            }
                        },
                        {
                            "@type": "Question",
                            "name": "Is Belt-schutsloot geschikt voor gezinnen?",
                            "acceptedAnswer": {
                                "@type": "Answer",
                                "text": "Ja, Belt-schutsloot is zeer geschikt voor gezinnen. De rustige omgeving maakt het zelfs ideaal voor gezinnen met jonge kinderen die op zoek zijn naar een veilige en rustige vaarervaring. Het dorp heeft dezelfde idyllische sfeer als Giethoorn, maar zonder de drukte."
                            }
                        },
                        {
                            "@type": "Question",
                            "name": "Kun je zowel Belt-schutsloot als Giethoorn bezoeken op één dag?",
                            "acceptedAnswer": {
                                "@type": "Answer",
                                "text": "Ja, dat is zeker mogelijk! Belt-schutsloot ligt op slechts enkele kilometers van Giethoorn. Je kunt een dagtocht maken waarbij je beide dorpen bezoekt. Start bijvoorbeeld bij Belt-schutsloot voor rust en authenticiteit, en vaar daarna door naar Giethoorn. Houd er rekening mee dat dit een langere vaartocht wordt, dus plan voldoende tijd in."
                            }
                        },
                        {
                            "@type": "Question",
                            "name": "Welke boten zijn het beste voor Belt-schutsloot?",
                            "acceptedAnswer": {
                                "@type": "Answer",
                                "text": "Alle boten van Nijenhuis Botenverhuur zijn geschikt voor Belt-schutsloot. Electrosloepen zijn comfortabel voor groepen, kano's en kajaks zijn perfect voor het varen door de smalle slootjes, en zeilpunters geven een authentieke ervaring. Kies het type boot dat het beste bij jouw voorkeur en gezelschap past."
                            }
                        },
                        {
                            "@type": "Question",
                            "name": "Zijn er restaurants of cafés in Belt-schutsloot?",
                            "acceptedAnswer": {
                                "@type": "Answer",
                                "text": "Belt-schutsloot is kleiner en heeft minder faciliteiten dan Giethoorn. Er zijn wel enkele horecagelegenheden, maar minder dan in Giethoorn. Het is aan te raden om een picknick mee te nemen of na je bezoek aan Belt-schutsloot door te varen naar Giethoorn waar meer restaurants zijn. Of geniet gewoon van de rust en neem je eigen eten en drinken mee voor een heerlijke picknick aan het water."
                            }
                        }
                    ]
                }
                </script>
                
                <div class="faq-accordion">
                    <details class="faq-item">
                        <summary>
                            <span class="faq-question">Waarom is Belt-schutsloot minder toeristisch dan Giethoorn?</span>
                            <span class="faq-icon"></span>
                        </summary>
                        <div class="faq-answer">
                            <p>Belt-schutsloot is minder bekend bij internationale toeristen dan Giethoorn, waardoor het veel rustiger is. Het dorp heeft dezelfde charmante sfeer met grachten, bruggetjes en rietgedekte huizen, maar trekt veel minder bezoekers aan. Dit maakt het perfect voor wie op zoek is naar een authentieke en rustige ervaring.</p>
                        </div>
                    </details>

                    <details class="faq-item">
                        <summary>
                            <span class="faq-question">Hoe ver is Belt-schutsloot vanaf Wanneperveen?</span>
                            <span class="faq-icon"></span>
                        </summary>
                        <div class="faq-answer">
                            <p>Vanaf Nijenhuis Botenverhuur in Wanneperveen is Belt-schutsloot ongeveer 6-8 kilometer varen, wat korter is dan de route naar Giethoorn (10 km). De tocht duurt ongeveer 1 tot 1,5 uur enkele reis, afhankelijk van het type boot.</p>
                        </div>
                    </details>

                    <details class="faq-item">
                        <summary>
                            <span class="faq-question">Is Belt-schutsloot geschikt voor gezinnen?</span>
                            <span class="faq-icon"></span>
                        </summary>
                        <div class="faq-answer">
                            <p>Ja, Belt-schutsloot is zeer geschikt voor gezinnen. De rustige omgeving maakt het zelfs ideaal voor gezinnen met jonge kinderen die op zoek zijn naar een veilige en rustige vaarervaring. Het dorp heeft dezelfde idyllische sfeer als Giethoorn, maar zonder de drukte.</p>
                        </div>
                    </details>

                    <details class="faq-item">
                        <summary>
                            <span class="faq-question">Kun je zowel Belt-schutsloot als Giethoorn bezoeken op één dag?</span>
                            <span class="faq-icon"></span>
                        </summary>
                        <div class="faq-answer">
                            <p>Ja, dat is zeker mogelijk! Belt-schutsloot ligt op slechts enkele kilometers van Giethoorn. Je kunt een dagtocht maken waarbij je beide dorpen bezoekt. Start bijvoorbeeld bij Belt-schutsloot voor rust en authenticiteit, en vaar daarna door naar Giethoorn. Houd er rekening mee dat dit een langere vaartocht wordt, dus plan voldoende tijd in.</p>
                        </div>
                    </details>

                    <details class="faq-item">
                        <summary>
                            <span class="faq-question">Welke boten zijn het beste voor Belt-schutsloot?</span>
                            <span class="faq-icon"></span>
                        </summary>
                        <div class="faq-answer">
                            <p>Alle boten van Nijenhuis Botenverhuur zijn geschikt voor Belt-schutsloot. <a href="/electrosloop-8#booking">Electrosloepen</a> zijn comfortabel voor groepen, <a href="/canoe-3#booking">kano's</a> zijn perfect voor het varen door de smalle slootjes, en <a href="/sailpunter-3-4#booking">zeilpunters</a> geven een authentieke ervaring. Kies het type boot dat het beste bij jouw voorkeur en gezelschap past.</p>
                        </div>
                    </details>

                    <details class="faq-item">
                        <summary>
                            <span class="faq-question">Zijn er restaurants of cafés in Belt-schutsloot?</span>
                            <span class="faq-icon"></span>
                        </summary>
                        <div class="faq-answer">
                            <p>Belt-schutsloot is kleiner en heeft minder faciliteiten dan Giethoorn. Er zijn wel enkele horecagelegenheden, maar minder dan in Giethoorn. Het is aan te raden om een picknick mee te nemen of na je bezoek aan Belt-schutsloot door te varen naar Giethoorn waar meer restaurants zijn. Of geniet gewoon van de rust en neem je eigen eten en drinken mee voor een heerlijke picknick aan het water.</p>
                        </div>
                    </details>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="cta-section" style="background: var(--secondary-color); color: white; padding: 4rem 0;">
            <div class="container">
                <div class="cta-content">
                    <h2 style="color: white; margin-bottom: 1rem;">Klaar om Belt-schutsloot te ontdekken?</h2>
                    <p class="content-prose" style="margin-bottom: 2rem; opacity: 0.95;">
                        Reserveer nu je boot en ontdek dit verborgen parel in de Weerribben. Geniet van rust, authenticiteit 
                        en de unieke sfeer die Belt-schutsloot zo bijzonder maakt.
                    </p>
                    <div class="cta-buttons">
                        <a href="/booking" class="btn-cta-primary">Direct boeken</a>
                        <a href="/canoe-3#booking" class="btn-cta-outline">Bekijk kano 3 pers</a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php include __DIR__ . '/../components/footer.php'; ?>
</body>
</html>
