<?php
/**
 * Vaarkaart Page - Nijenhuis Botenverhuur
 */
require_once __DIR__ . '/../components/config.php';
$basePath = '..';
$pageTitle = 'Vaarkaart Weerribben-Wieden | Routes';
$pageDescription = 'Interactieve vaarkaart voor de Weerribben. Ontdek vaarroutes naar Giethoorn en door Nationaal Park Weerribben-Wieden. Inclusief vaarregels.';
$pageKeywords = 'vaarkaart weerribben, vaarroutes giethoorn, waterkaart overijssel, varen in weerribben wieden';
$headerTitle = 'Vaarkaart';
$headerTitleI18n = 'vaarkaart_title';
$headerDescription = 'Navigatie informatie en routes voor het natuurgebied Weerribben';
$headerDescriptionI18n = 'vaarkaart_description';

// Breadcrumbs for this page
$breadcrumbs = [
    ['name' => 'Home', 'url' => '/'],
    ['name' => 'Vaarkaart', 'url' => '/vaarkaart']
];
$additionalStyles = ['/frontend/css/pages/vaarkaart.css'];
?>
<!DOCTYPE html>
<html lang="nl">
<?php include __DIR__ . '/../components/head.php'; ?>
<body data-page="vaarkaart">
<!-- HowTo Schema: Sail from Wanneperveen to Giethoorn -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "HowTo",
    "name": "Hoe vaar je van Wanneperveen naar Giethoorn?",
    "description": "Stapsgewijze route om met een huurboot van Nijenhuis Botenverhuur in Wanneperveen naar Giethoorn te varen door Nationaal Park Weerribben-Wieden.",
    "totalTime": "PT2H",
    "estimatedCost": {
        "@type": "MonetaryAmount",
        "currency": "EUR",
        "value": "80"
    },
    "supply": [
        {"@type": "HowToSupply", "name": "Gehuurde boot (electrosloep, kano of kajak)"},
        {"@type": "HowToSupply", "name": "Reddingsvest (inbegrepen bij huur)"},
        {"@type": "HowToSupply", "name": "Vaarkaart (inbegrepen bij huur)"}
    ],
    "step": [
        {
            "@type": "HowToStep",
            "position": 1,
            "name": "Vertrek vanuit Wanneperveen",
            "text": "Start bij Nijenhuis Botenverhuur, Veneweg 199 in Wanneperveen. Na een korte instructie vaar je het water op richting het noorden.",
            "url": "https://nijenhuis-botenverhuur.com/vaarkaart"
        },
        {
            "@type": "HowToStep",
            "position": 2,
            "name": "Vaar door de Weerribben",
            "text": "Volg de vaarroute door Nationaal Park Weerribben-Wieden. Geniet van rietvelden, moerassen en het open water. Afstand: circa 10 km.",
            "url": "https://nijenhuis-botenverhuur.com/vaarkaart"
        },
        {
            "@type": "HowToStep",
            "position": 3,
            "name": "Aankomst in Giethoorn",
            "text": "Na 1,5 tot 2 uur varen bereik je de grachten van Giethoorn. Let op: maximumsnelheid is 6 km/u in Giethoorn. Geniet van de bruggetjes en rietgedekte huisjes.",
            "url": "https://nijenhuis-botenverhuur.com/giethoorn"
        },
        {
            "@type": "HowToStep",
            "position": 4,
            "name": "Terugvaart naar Wanneperveen",
            "text": "Vaar dezelfde route terug of kies een alternatieve route via Belt-schutsloot. Zorg dat je op tijd terug bent (uiterlijk 18:00).",
            "url": "https://nijenhuis-botenverhuur.com/belt-schutsloot"
        }
    ]
}
</script>
    <?php include __DIR__ . '/../components/topbar.php'; ?>
    <?php include __DIR__ . '/../components/nav.php'; ?>
    <?php include __DIR__ . '/../components/breadcrumb.php'; ?>
    <?php include __DIR__ . '/../components/page-header.php'; ?>

    <main>
        <!-- Interactive Map -->
        <section class="content-section">
            <div class="container">
                <div class="section-title">
                    <h2 data-i18n="vaarkaart_interactive_map_title">Interactieve vaarkaart Weerribben-Wieden</h2>
                    <p data-i18n="vaarkaart_interactive_map_description">Ontdek de mooiste routes door Nationaal Park Weerribben-Wieden. Deze vaarkaart toont alle vaarroutes in het gebied.</p>
                </div>
                <div class="content-prose">
                    <p data-i18n="vaarkaart_intro_extra">Nationaal Park Weerribben-Wieden is het grootste laagveenmoeras van Noordwest-Europa. Het watergebied bestaat uit meren, sloten en vaarten die vroeger door turfwinning ontstonden. Tegenwoordig is het een paradijs voor booters, met rustige routes, rietkragen, moerassen en weidse uitzichten. Vanuit Nijenhuis Botenverhuur in Wanneperveen vaar je direct het netwerk in. Hieronder vind je de interactieve kaart, populaire routes en belangrijke vaarregels.</p>
                </div>

                <div class="map-section-card">
                    <h3 data-i18n="vaarkaart_interactive_map_map_title">Weerribben natuurgebied - interactieve vaarkaart</h3>
                    <div class="map-attribution">
                        <p><strong data-i18n="vaarkaart_interactive_map_attribution_source">Bron:</strong> <a href="https://waterkaart.net/" target="_blank" rel="noopener noreferrer" data-i18n="vaarkaart_interactive_map_attribution_source_link">Waterkaart.net</a> <span data-i18n="vaarkaart_interactive_map_attribution_suffix">– Professionele vaarkaarten voor Nederlandse wateren</span></p>
                    </div>
                    
                    <!-- Interactive Waterkaart iframe centered on Veneweg 199, Wanneperveen (52.6972, 6.0780) -->
                    <!-- Interactive Waterkaart iframe centered on Veneweg 199, Wanneperveen (52.6972, 6.0780) -->
                    <div id="interactiveMapContainer" class="map-container" style="border-radius: var(--radius-lg); overflow: hidden; margin: 1rem 0; position: relative; background: white;">
                        <div id="mapErrorFallback" style="display: none; padding: 2rem; text-align: center; background: #f8f9fa; border-radius: var(--radius-lg);">
                            <p style="margin-bottom: 1rem; color: #856404;">
                                <strong>⚠️ De interactieve kaart kon niet worden geladen.</strong>
                            </p>
                            <p style="margin-bottom: 1.5rem; color: #666;">
                                Bezoek <a href="https://waterkaart.net/" target="_blank" rel="noopener noreferrer" style="color: var(--primary-color, #003366); text-decoration: underline;">Waterkaart.net</a> voor de interactieve vaarkaart.
                            </p>
                            <a href="https://www.openstreetmap.org/?mlat=52.6972&mlon=6.0780#map=14/52.6972/6.0780" target="_blank" rel="noopener noreferrer" class="btn" style="display: inline-block;">
                                <span class="btn-icon">📍</span>
                                Open OpenStreetMap
                            </a>
                        </div>
                        <iframe 
                            id="waterkaartIframe"
                            src="https://waterkaart.net/api/integreer.php?locatie=52.6972,6.0780&zoom=14" 
                            width="100%" 
                            height="750" 
                            style="border: none;" 
                            allowfullscreen
                            loading="lazy"
                            title="Interactieve waterkaart - Veneweg 199, Wanneperveen"
                            onerror="handleMapError()"></iframe>
                        
                        <button class="map-fullscreen-btn-close" onclick="toggleMapFullscreen()">
                            <span style="font-size: 1.2rem;">✕</span>
                            <span data-i18n="vaarkaart_close_fullscreen">Sluiten</span>
                        </button>
                    </div>
                    
                    <div class="map-buttons" style="text-align: center; margin-top: 1rem; display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                        <button onclick="toggleMapFullscreen()" class="btn">
                            <span class="btn-icon">⤢</span>
                            <span data-i18n="vaarkaart_expand_map">Vergroot kaart</span>
                        </button>
                        <a href="https://www.openstreetmap.org/?mlat=52.6972&mlon=6.0780#map=14/52.6972/6.0780" target="_blank" rel="noopener noreferrer" class="btn btn-outline">
                            <span class="btn-icon">📍</span>
                            <span data-i18n="vaarkaart_view_osm">OpenStreetMap</span>
                        </a>
                    </div>
                    
                    <div class="alert alert-info" style="margin-top: 2rem; background-color: #d1ecf1; color: #0c5460; padding: 1rem; border-radius: var(--radius-md); border: 1px solid #bee5eb;">
                        <p style="margin: 0; font-size: 0.95rem; margin-bottom: 0.5rem;">
                            <strong>ℹ️ Technische informatie:</strong> De interactieve kaart wordt geleverd door Waterkaart.net. 
                            Als de kaart niet correct laadt, kan dit te maken hebben met een technisch probleem bij de externe service.
                        </p>
                        <p style="margin: 0; font-size: 0.95rem;">
                            <strong>Oplossing:</strong> Ververs de pagina of bezoek <a href="https://waterkaart.net/" target="_blank" rel="noopener noreferrer" style="color: #0c5460; text-decoration: underline; font-weight: 600;">Waterkaart.net</a> direct voor de volledige interactieve kaart.
                        </p>
                    </div>
                    
                    <div class="alert alert-warning" style="margin-top: 1rem; background-color: #fff3cd; color: #856404; padding: 1rem; border-radius: var(--radius-md); border: 1px solid #ffeeba;">
                        <p style="margin: 0; font-size: 0.95rem;">
                            <strong data-i18n="vaarkaart_disclaimer_title">Disclaimer:</strong> <span data-i18n="vaarkaart_disclaimer_text">Wij nemen geen verantwoordelijkheid voor de inhoud en juistheid van deze kaart. Lokale wetten, regels en borden langs het water dienen altijd als eerste te worden gevolgd.</span>
                        </p>
                    </div>
                    
                    <div class="map-footer">
                        <p><em data-i18n="vaarkaart_footer_source">Voor gedetailleerde vaarkaarten en actuele vaarinformatie, bezoek <a href="https://waterkaart.net/" target="_blank" rel="noopener noreferrer">Waterkaart.net</a>.</em></p>
                    </div>


                    <script>
                        var mapPlaceholder = null;
                        var mapErrorCheckTimeout = null;

                        // Error handling for iframe map loading
                        function handleMapError() {
                            const iframe = document.getElementById('waterkaartIframe');
                            const fallback = document.getElementById('mapErrorFallback');
                            if (iframe && fallback) {
                                iframe.style.display = 'none';
                                fallback.style.display = 'block';
                                // Clear any pending timeout
                                if (mapErrorCheckTimeout) {
                                    clearTimeout(mapErrorCheckTimeout);
                                }
                            }
                        }

                        // Monitor iframe for errors
                        document.addEventListener('DOMContentLoaded', function() {
                            const iframe = document.getElementById('waterkaartIframe');
                            
                            if (iframe) {
                                // Listen for iframe load event
                                iframe.addEventListener('load', function() {
                                    // Give the iframe time to initialize, then check for errors
                                    mapErrorCheckTimeout = setTimeout(function() {
                                        // Note: Due to cross-origin restrictions, we can't directly check
                                        // for errors inside the iframe, but we can provide user feedback
                                        console.log('Waterkaart iframe loaded. If you see MapLibre errors in console, the external service may have issues.');
                                    }, 2000);
                                });

                                // Handle iframe load errors
                                iframe.addEventListener('error', function() {
                                    console.error('Waterkaart iframe failed to load');
                                    handleMapError();
                                });
                            }

                            // Listen for global errors that might be related to the map
                            window.addEventListener('error', function(e) {
                                // Check if error is related to maplibreGL or the iframe
                                if (e.message && (
                                    e.message.includes('maplibreGL') || 
                                    e.message.includes('maplibre') ||
                                    e.message.includes('waterkaart')
                                )) {
                                    console.warn('Map-related error detected:', e.message);
                                    // Note: We can't directly fix errors in cross-origin iframes,
                                    // but we can inform the user
                                }
                            }, true);
                        });

                        function toggleMapFullscreen() {
                            const container = document.getElementById('interactiveMapContainer');
                            
                            // Check if we are checking checking based on position in DOM
                            const isFullscreen = container.parentElement === document.body;
                            
                            if (!isFullscreen) {
                                // --- ENTER FULLSCREEN ---
                                
                                // 1. Create a placeholder to hold the space
                                mapPlaceholder = document.createElement('div');
                                mapPlaceholder.id = 'map-placeholder-temp';
                                mapPlaceholder.style.width = '100%';
                                mapPlaceholder.style.height = container.offsetHeight + 'px'; // Keep original height
                                mapPlaceholder.style.margin = '1rem 0';
                                
                                // 2. Insert placeholder where map is currently
                                container.parentNode.insertBefore(mapPlaceholder, container);
                                
                                // 3. Move map to body (detaches from potential stacking contexts)
                                document.body.appendChild(container);
                                
                                // 4. Apply class and lock scroll
                                container.classList.add('map-fullscreen');
                                document.body.style.overflow = 'hidden';
                                
                            } else {
                                // --- EXIT FULLSCREEN ---
                                
                                // 1. Find placeholder
                                if (!mapPlaceholder) {
                                    mapPlaceholder = document.getElementById('map-placeholder-temp');
                                }
                                
                                if (mapPlaceholder) {
                                    // 2. Move map back to placeholder location
                                    mapPlaceholder.parentNode.insertBefore(container, mapPlaceholder);
                                    
                                    // 3. Remove placeholder
                                    mapPlaceholder.remove();
                                    mapPlaceholder = null;
                                }
                                
                                // 4. Remove class and unlock scroll
                                container.classList.remove('map-fullscreen');
                                document.body.style.overflow = '';
                            }
                        }
                        
                        // Close on escape key
                        document.addEventListener('keydown', function(event) {
                            if (event.key === "Escape") {
                                const container = document.getElementById('interactiveMapContainer');
                                if (container.classList.contains('map-fullscreen')) {
                                    toggleMapFullscreen();
                                }
                            }
                        });
                    </script>
                </div>
            </div>
        </section>

        <!-- Popular Routes -->
        <section class="content-section">
            <div class="container">
                <div class="section-title">
                    <h2 data-i18n="vaarkaart_popular_routes_title">Populaire routes</h2>
                    <p data-i18n="vaarkaart_popular_routes_description">Ontdek de mooiste vaarroutes in het gebied</p>
                </div>

                <div class="boat-categories">
                    <div class="category-card">
                        <div class="category-icon">🏘️</div>
                        <h3 data-i18n="vaarkaart_popular_routes_giethoorn_title">Giethoorn route</h3>
                        <div class="info-list">
                            <div class="info-list-item"><span data-i18n="vaarkaart_label_start">Start</span>: <?php echo SITE_NAME; ?></div>
                            <div class="info-list-item"><span data-i18n="vaarkaart_label_duration">Duur</span>: 2-3 <span data-i18n="unit_hours">uur</span></div>
                            <div class="info-list-item"><span data-i18n="vaarkaart_label_distance">Afstand</span>: 8 <span data-i18n="unit_km">km</span></div>
                            <div class="info-list-item"><span data-i18n="vaarkaart_label_difficulty">Moeilijkheidsgraad</span>: <span data-i18n="vaarkaart_difficulty_easy">Gemakkelijk</span></div>
                            <div class="info-list-item"><span data-i18n="vaarkaart_label_highlights">Hoogtepunten</span>: <span data-i18n="vaarkaart_highlight_giethoorn">Dorpsgezicht Giethoorn</span></div>
                        </div>
                        <p data-i18n="vaarkaart_popular_routes_giethoorn_perfect_for"><strong>Perfect voor beginners en families</strong></p>
                        <p class="route-desc" data-i18n="vaarkaart_route_giethoorn_desc">De route voert door smalle sloten en bredere vaarten naar het centrum van Giethoorn. Onderweg zie je rietgedekte boerderijen, bruggetjes en typische punters. In Giethoorn kun je aanleggen om te wandelen of te lunchen. Plan minstens 2–3 uur voor een ontspannen heen-en-terug tocht.</p>
                        <a href="/giethoorn" class="card-link">📖 Lees meer over Giethoorn →</a>
                    </div>

                    <div class="category-card">
                        <div class="category-icon">🌿</div>
                        <h3 data-i18n="vaarkaart_popular_routes_weerribben_route_title">Weerribben natuurroute</h3>
                        <div class="info-list">
                            <div class="info-list-item"><span data-i18n="vaarkaart_label_start">Start</span>: <?php echo SITE_NAME; ?></div>
                            <div class="info-list-item"><span data-i18n="vaarkaart_label_duration">Duur</span>: 4-5 <span data-i18n="unit_hours">uur</span></div>
                            <div class="info-list-item"><span data-i18n="vaarkaart_label_distance">Afstand</span>: 15 <span data-i18n="unit_km">km</span></div>
                            <div class="info-list-item"><span data-i18n="vaarkaart_label_difficulty">Moeilijkheidsgraad</span>: <span data-i18n="vaarkaart_difficulty_medium">Gemiddeld</span></div>
                            <div class="info-list-item"><span data-i18n="vaarkaart_label_highlights">Hoogtepunten</span>: <span data-i18n="vaarkaart_highlight_biotope">Wilde dieren, vogels</span></div>
                        </div>
                        <p data-i18n="vaarkaart_popular_routes_weerribben_route_for_nature_lovers"><strong>Voor natuur- en vogelliefhebbers</strong></p>
                        <p class="route-desc" data-i18n="vaarkaart_route_weerribben_desc">Deze route voert dieper het park in, langs moerassen, rietvelden en open water. Je kunt ijsvogels, reigers, libellen en diverse watervogels spotten. Neem een picknick mee en zoek een rustig plekje aan de oever. Een electrosloep of kano is ideaal voor deze route.</p>
                    </div>

                    <div class="category-card">
                        <div class="category-icon">🏘️</div>
                        <h3 data-i18n="vaarkaart_popular_routes_wanneperveen_title">Wanneperveen rondvaart</h3>
                        <div class="info-list">
                            <div class="info-list-item"><span data-i18n="vaarkaart_label_start">Start</span>: <?php echo SITE_NAME; ?></div>
                            <div class="info-list-item"><span data-i18n="vaarkaart_label_duration">Duur</span>: 1-2 <span data-i18n="unit_hours">uur</span></div>
                            <div class="info-list-item"><span data-i18n="vaarkaart_label_distance">Afstand</span>: 5 <span data-i18n="unit_km">km</span></div>
                            <div class="info-list-item"><span data-i18n="vaarkaart_label_difficulty">Moeilijkheidsgraad</span>: <span data-i18n="vaarkaart_difficulty_easy">Gemakkelijk</span></div>
                            <div class="info-list-item"><span data-i18n="vaarkaart_label_highlights">Hoogtepunten</span>: <span data-i18n="vaarkaart_highlight_wanneperveen">Dorpsgezicht Wanneperveen</span></div>
                        </div>
                        <p data-i18n="vaarkaart_popular_routes_wanneperveen_short_route"><strong>Korte route voor een snelle uitstap</strong></p>
                        <p class="route-desc" data-i18n="vaarkaart_route_wanneperveen_desc">Een ideale route voor een eerste kennismaking met het gebied of als je weinig tijd hebt. Je vaart rond Wanneperveen en geniet van het dorpsgezicht en de omliggende wateren. Geschikt voor alle boottypes, inclusief kajaks en kano's.</p>
                        <a href="/wanneperveen" class="card-link">📖 Lees meer over Wanneperveen →</a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Navigation Rules -->
        <section class="content-section">
            <div class="container">
                <div class="section-title">
                    <h2 data-i18n="vaarkaart_navigation_rules_title">Vaarregels & Veiligheid</h2>
                    <p data-i18n="vaarkaart_navigation_rules_description">Belangrijke informatie voor veilig varen</p>
                </div>

                <div class="boat-categories">
                    <div class="category-card">
                        <div class="category-icon">📋</div>
                        <h3 data-i18n="vaarkaart_navigation_rules_general_rules_title">Algemene regels</h3>
                        <div class="info-list">
                            <div class="info-list-item">Maximum snelheid: 6 km/u</div>
                            <div class="info-list-item">Zwemvesten verplicht</div>
                            <div class="info-list-item">Geen alcohol tijdens het varen</div>
                            <div class="info-list-item">Respecteer de natuur</div>
                            <div class="info-list-item">Houd afstand van andere boten</div>
                        </div>
                    </div>

                    <div class="category-card">
                        <div class="category-icon">🛡️</div>
                        <h3 data-i18n="vaarkaart_navigation_rules_safety_tips_title">Veiligheidstips</h3>
                        <div class="info-list">
                            <div class="info-list-item">Controleer het weer voor vertrek</div>
                            <div class="info-list-item">Neem voldoende water mee</div>
                            <div class="info-list-item">Zorg voor een opgeladen telefoon</div>
                            <div class="info-list-item">Ken de vaarregels</div>
                            <div class="info-list-item">Blijf op de bevaarbare routes</div>
                        </div>
                    </div>

                    <div class="category-card">
                        <div class="category-icon">📞</div>
                        <h3 data-i18n="vaarkaart_navigation_rules_emergency_numbers_title">Noodnummers</h3>
                        <div class="info-list">
                            <div class="info-list-item">Algemeen alarmnummer: 112</div>
                            <div class="info-list-item"><?php echo SITE_NAME; ?>: <?php echo SITE_PHONE; ?></div>
                            <div class="info-list-item">Waterpolitie: 0900-8844</div>
                            <div class="info-list-item">Weerbericht: 0900-9722</div>
                            <div class="info-list-item">Reddingsbrigade: 0900-0112</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php include __DIR__ . '/../components/footer.php'; ?>
</body>
</html>

