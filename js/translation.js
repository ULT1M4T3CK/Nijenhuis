(() => {
    /* ---------- 1. DICTIONARY -------------------------------- */
    const t = {
      nl: {
        /* global */
        nav_opening: "Openingstijden: 9:00 - 18:00",
        nav_boats: "Botenverhuur",
        nav_house: "Vakantiehuis",
        nav_forsale: "Te Koop",
        nav_camping: "Camping",
        nav_marina: "Jachthaven",
        nav_chart: "Vaarkaart",
        nav_contact: "Contact",
        /* index.html */
        hero_book_h2: "Direct boeken",
        hero_book_p: "Reserveer eenvoudig je boot voor een dag op het water",
        hero_book_date: "Datum",
        hero_book_boat_type: "Boot type",  
        hero_book_boat_type_select: "Selecteer een boot",
        hero_book_btn: "Beschikbaarheid controleren",
        btn_outline: "📞 Bel direct!", 
        hero_book_badge: "100% veilig &amp; vrijblijvend",
        hero_h1: "Ontsnap naar het natuurparadijs",
        hero_h1_p: "Ervaar de schoonheid van het natuurgebied Weerribben met onze premium botenverhuur. Perfect voor families, vrienden en natuurliefhebbers.",
        hero_btn: "Beschikbaarheid controleren",
        intro_h2: "Even helemaal weg uit de dagelijkse routine",
        intro_h2_p: "In de drukke wereld van vandaag heeft iedereen een moment nodig om los te koppelen. Laat files, stress en de dagelijkse sleur achter je. Ervaar het leven vanuit een ander perspectief - vanaf het water.",
        intro_h2_p2: "Onze boten bieden de perfecte manier om het adembenemende natuurgebied Weerribben te verkennen, met onvergetelijke herinneringen voor jou en je dierbaren.",
        intro_h3: "Waarom kiezen voor Nijenhuis?",
        intro_h3_li1: "📍 Gelegen in het hart van het natuurgebied Weerribben",
        intro_h3_li2: "🚤 Breed assortiment boten voor alle voorkeuren",
        intro_h3_li3: "🌿 Milieuvriendelijke elektrische boten beschikbaar",
        intro_h3_li4: "👨‍👩‍👧‍👦 Perfect voor families en groepen",
        intro_h3_li5: "💰 Concurrentiële prijzen voor alle budgetten",
        intro_h3_li6: "📞 Persoonlijke service en ondersteuning", 
        intro_cta_p: "Voor meer informatie, bel 0522 - 281 528",
        intro_cta_p2: "Contant en pin betalingen geaccepteerd", 
        services_h2: "Onze diensten",
        services_h3_1: "Botenverhuur",
        services_p_1: "Elektrische boten, kano's, kajaks en SUP boards voor alle leeftijden en ervaringsniveaus.",
        services_btn_1: "Meer Info",
        services_h3_2: "Vakantiehuis",
        services_p_2: "Comfortabele vakantie accommodatie perfect voor families en groepen.",   
        services_btn_2: "Meer Info",
        services_h3_3: "Camping",
        services_p_3: "Prachtige kampeerplaatsen in het natuurgebied met moderne faciliteiten en adembenemende uitzichten.",
        services_btn_3: "Meer Info",
        services_h3_4: "Jachthaven",
        services_p_4: "Professionele jachthaven diensten voor booteigenaren met veilige ligplaatsen en onderhoud.", 
        services_btn_4: "Meer Info",
        map_h2: "Vind ons",
        footer_p: "Hier begint jouw avontuur in de prachtige Weerribben!",
        footer_company_name: "Nijenhuis Botenverhuur",
        footer_company_location: "Camping & Jachthaven",
        footer_company_address: "Veneweg 199",
        footer_company_postal: "7946 LP Wanneperveen",
        footer_company_phone: "Tel: 0522 281 528",
        footer_company_kvk: "Kvk: 6769 7097",
        footer_company_btw: "Btw nr: NL 195153637 B01",
        footer_li1: "Botenverhuur",
        footer_li2: "Vakantiehuis",
        footer_li3: "Te Koop",  
        footer_li4: "Camping",
        footer_li5: "Jachthaven",
        footer_li6: "Vaarkaart",
        footer_li7: "Contact",
        footer_bottom: "&copy; 2025 Nijenhuis Botenverhuur. Alle rechten voorbehouden.", 
        /* boats page */
        boats_header_h1: "Botenverhuur",
        boats_header_p: "Stap aan boord en ontdek het mooie natuurgebied de Weerribben met onze boten, kano’s en kajaks!",
        boats_h2: "Onze boten",
        boats_intro: "Wij bieden een breed assortiment boten voor alle voorkeuren en ervaringsniveaus",
        boats_cat_electric: "Elektrische boten",
        boats_cat_electric_desc: "Milieuvriendelijke boten met elektrische aandrijving, perfect voor rustige vaartochten door het natuurgebied.",
        boats_cat_gasoline: "Benzine boten",
        boats_cat_gasoline_desc: "Krachtige boten met benzinemotor, ideaal voor grotere groepen en langere vaartochten.",
        boats_cat_sailing: "Zeilboten",
        boats_cat_sailing_desc: "Traditionele zeilboten voor de ervaren zeiler. Geniet van de wind en de natuur.",
        boats_cat_canoe: "Kano's & Kajaks",
        boats_cat_canoe_desc: "Perfect voor sportieve activiteiten en het verkennen van kleinere waterwegen.",
        boats_cat_sup: "Sup boards",
        boats_cat_sup_desc: "Stand-up paddleboards voor een unieke manier om het water te ervaren.",
        boats_cat_all: "Alle boten",
        boats_cat_all_desc: "Bekijk alle beschikbare boten en hun details.",
        prices_h2: "Prijzen & Beschikbaarheid",
        prices_intro: "Alle prijzen zijn per dag inclusief veiligheidsuitrusting en instructies",
        prices_intro_2: "De borg is afhankelijk van het type boot en moet contant worden betaald.",
        prices_table_title: "Dagprijzen botenverhuur",
        prices_season: "Seizoen: 1 april - 1 november 2025",
        prices_th_type: "Boot type",
        prices_th_capacity: "Capaciteit",
        prices_th_price: "Prijs per dag",
        prices_th_deposit: "Borg",
        multi_day_note: "Boten kunnen ook voor meerdere dagen worden gehuurd. Neem contact op voor meer informatie en tarieven.",
        rentinfo_h2: "Huurinformatie",
        rentinfo_intro: "Alles wat u moet weten over het huren van een boot",
        rentinfo_book_title: "📅 Reserveringen",
        rentinfo_book_desc: "Reserveringen kunnen telefonisch of online worden gemaakt. Wij raden aan om vooraf te reserveren, vooral in het hoogseizoen.",
        rentinfo_open_title: "⏰ Openingstijden",
        rentinfo_open_desc: "Dagelijks geopend van 09:00 tot 18:00 uur tijdens het seizoen (1 april - 1 november).",
        rentinfo_pay_title: "💰 Betaling",
        rentinfo_pay_desc: "Alleen contante betaling wordt geaccepteerd. Een borg van €50-€100 is vereist afhankelijk van het boottype. Zie de prijzentabel voor specifieke borgbedragen.",
        /* house page */
        house_header_h1: "Vakantiehuis",
        house_header_p1: "Ervaar een heerlijk verblijf in ons vakantiehuis, midden in het prachtige natuurgebied de Weerribben.",
        house_overview_h2: "Vakantiehuis Belterwiede",
        house_overview_p1: "<strong>HET HELE JAAR GEOPEND</strong>",
        house_overview_h3: "Perfecte uitvalsbasis in de kop van Overijssel",
        house_overview_p2: "Wilt u een weekend, midweek, week of een hele vakantie doorbrengen in een prachtig natuur- of watersportgebied? Kom dan naar de Kop van Overijssel, waar u kunt genieten van varen, vissen, zwemmen, fietsen, wandelen en het bezoeken van andere dorpen in de omgeving. Het huis is direct gelegen aan het Belterwijde meer.",
        house_overview_p3: "<strong>Beneden:</strong> <span>U heeft 1 slaapkamer, een douche, toilet en wasmachine. U kunt ontspannen in de ruime woonkamer met TV en radio. De kamer heeft een open keuken met diverse huishoudelijke apparaten (oven, magnetron, koelkast). Er is een ruime hal en het huis is volledig voorzien van centrale verwarming.</span>",
        house_overview_p4: "<strong>Boven:</strong> <span>U heeft vier slaapkamers, waarvan er twee een wastafel hebben. Er is ook een douche en toilet op de tweede verdieping.</span>",
        house_overview_li1: "Kinderbedje, box en kinderstoel kunnen worden aangevraagd bij Waterpark Belterwijde.",
        house_overview_li2: "Kussens en dekbedden beschikbaar",
        house_overview_li3: "Linnengoed graag zelf meenemen",
        house_overview_li4: "Linnengoed ook te huur bij ons (graag vooraf melden)",
        house_overview_li5: "Voor verdere vragen kunt u contact met ons opnemen",
        house_amenities_h2: "Faciliteiten",
        house_amenities_p1: "Alles voor een comfortabel verblijf",
        house_amenities_h3: "5 Slaapkamers",
        house_amenities_p2: "1 slaapkamer beneden, 4 slaapkamers boven (2 met wastafel)",
        house_amenities_h4: "Open keuken",
        house_amenities_p3: "Oven, magnetron, koelkast en alle huishoudelijke apparaten",
        house_amenities_h5: "2 Badkamers",
        house_amenities_p4: "Douche en toilet op beide verdiepingen",
        house_amenities_h6: "Woonkamer",
        house_amenities_p5: "Ruime woonkamer met TV en radio",
        house_amenities_h7: "Wasmachine",
        house_amenities_p6: "Wasmachine beschikbaar in het huis",
        house_amenities_h8: "Centrale verwarming",
        house_amenities_p7: "Volledig verwarmd voor comfort het hele jaar door",
        house_contact_h2: "Contact & Reserveringen",
        house_contact_p1: "Voor meer informatie en reserveringen",
        house_contact_h3: "Waterpark Belterwiede",
        house_contact_p2: "E-mail: info@parkbelterwiede.nl",
        house_contact_p3: "Telefon: 0522-281828",
        /* te-koop page */
        te_koop_h1: "Te koop",
        te_koop_p1: "Bekijk hier onze nieuwste aanbiedingen.",
        te_koop_h2: "Chalets & Stacaravans",
        te_koop_h3: "Geen aanbod beschikbaar",
        te_koop_p2: "Op dit moment hebben wij geen chalets of stacaravans te koop. Zodra er nieuw aanbod is, vindt u dat hier terug.",
        te_koop_p3: "Heeft u interesse in een chalet of stacaravan in de toekomst? Neem gerust contact met ons op voor meer informatie of om op de wachtlijst te komen.",
        te_koop_h4: "Interesse in een chalet of stacaravan?",
        te_koop_p4: "Neem contact met ons op:",
        te_koop_p5: "📞 <strong>Telefoon</strong>: 0522 281 528",
        te_koop_p6: "📍 <strong>Adres</strong>: Veneweg 199, 7946 LP Wanneperveen",
        te_koop_p7: "⏰ <strong>Openingstijden</strong>: Dagelijks 09:00 - 18:00 uur",

        /* camping page */
        camping_title: "Camping",
        camping_description: "Kom helemaal tot rust tijdens het kamperen midden in het prachtige natuurgebied de Weerribben.",

        camping_overview_title: "Onze camping",
        camping_overview_description: "Een rustige en sfeervolle camping midden in de natuur",

        camping_overview_permanent_description: "Onze camping biedt seizoensplaatsen aan, met vaste plekken voor caravans. Het is een gezellige, kleinschalige camping waar elke plaats een eigen aanlegsteiger heeft.",
        camping_overview_permanent_list_item_1: "Permanente seizonplaatsen",
        camping_overview_permanent_list_item_2: "Wateraansluiting",
        camping_overview_permanent_list_item_3: "Elektriciteitsmeter",
        camping_overview_permanent_list_item_4: "Centrale antenne",
        camping_overview_permanent_list_item_5: "Rioolafvoer",
        camping_overview_permanent_list_item_6: "Eigen aanlegplaats",
        camping_overview_permanent_list_item_7: "Douches en toiletten beschikbaar",
        camping_overview_permanent_list_item_8: "Kleine maar gezellige camping",
        camping_overview_permanent_list_item_9: "Wasmachine en droger beschikbaar",

        camping_overview_cta_strong: "Interesse in een permanente plaats?",
        camping_overview_cta_button: "BEL NU",

        facilities_title: "Faciliteiten",
        facilities_description: "Alle voorzieningen voor permanente plaatsen",

        facilities_sanitary_title: "Sanitair",
        facilities_sanitary_description: "Douches en toiletten beschikbaar voor alle gasten",
        facilities_electricity_title: "Elektriciteit",
        facilities_electricity_description: "Elektriciteitsmeter op elke plaats voor eigen verbruik",
        facilities_water_title: "Water",
        facilities_water_description: "Wateraansluiting beschikbaar op elke plaats",
        facilities_antenna_title: "Centrale antenne",
        facilities_antenna_description: "Centrale antenne voor TV-ontvangst",
        facilities_mooring_title: "Eigen aanlegplaats",
        facilities_mooring_description: "Elke plaats heeft een eigen aanlegplaats",
        facilities_sewerage_title: "Riool",
        facilities_sewerage_description: "Rioolafvoer beschikbaar op alle plaatsen",

        /* jachthaven page */
        jachthaven_title: "Jachthaven",
        jachthaven_description: "Professionele jachthaven diensten voor booteigenaren in het natuurgebied Weerribben",
        jachthaven_ligplaatsen_title: "Jachthaven ligplaatsen",
        jachthaven_ligplaatsen_description: "Ligplaatsen in de jachthaven zijn alleen voor permanente jaarlijkse ligplaatsen. Ze zijn uitgerust met water, elektriciteit, douche en toilet. De maximale diepgang is +/- 1,00 m. De jachthaven is direct gelegen aan het Belterwijde meer, van waaruit u in alle richtingen kunt varen.",
        jachthaven_ligplaatsen_cta: "Neem gerust <a href='contact.html'>contact</a> op voor meer informatie over beschikbaarheid en voorwaarden.",
        jachthaven_services_title: "Jachthaven diensten",
        jachthaven_services_description: "Complete jachthaven faciliteiten voor uw boot",
        jachthaven_services_ligplaatsen_title: "Ligplaatsen",
        jachthaven_services_ligplaatsen_description: "Veilige ligplaatsen voor boten tot 12 meter. Dagelijks en seizoensligplaatsen beschikbaar.",
        jachthaven_services_onderhoud_title: "Onderhoud",
        jachthaven_services_onderhoud_description: "Professioneel onderhoud en reparatie van alle boottypes. Winterstalling beschikbaar.",
        jachthaven_services_faciliteiten_title: "Faciliteiten",
        jachthaven_services_faciliteiten_description: "Sanitaire voorzieningen, douches, wasmachines en drogers voor gasten.",
        /* vaarkaart page */
        vaarkaart_title: "Vaarkaart",
        vaarkaart_description: "Navigatie informatie en routes voor het natuurgebied Weerribben",

        vaarkaart_interactive_map_title: "Interactieve vaarkaart",
        vaarkaart_interactive_map_description: "Ontdek de mooiste routes door het natuurgebied Weerribben",
        vaarkaart_interactive_map_map_title: "Weerribben natuurgebied - interactieve vaarkaart",
        vaarkaart_interactive_map_attribution_source: "Bron:",
        vaarkaart_interactive_map_attribution_source_link: "Waterkaart.net",
        vaarkaart_interactive_map_placeholder_title: "Interactieve vaarkaart",
        vaarkaart_interactive_map_placeholder_description: "Voor de meest actuele en gedetailleerde vaarkaarten van het Weerribben gebied, bezoek de professionele waterkaart van Nederland.",
        vaarkaart_interactive_map_placeholder_button: "Open waterkaart.net",
        vaarkaart_interactive_map_footer_description: "Deze interactieve vaarkaart wordt verzorgd door Waterkaart.net. Voor de meest actuele informatie en gedetailleerde vaarkaarten, bezoek hun website.",

        vaarkaart_popular_routes_title: "Populaire routes",
        vaarkaart_popular_routes_description: "Ontdek de mooiste vaarroutes in het gebied",

        vaarkaart_popular_routes_giethoorn_title: "Giethoorn route",
        vaarkaart_popular_routes_giethoorn_start: "Start: Nijenhuis Botenverhuur",
        vaarkaart_popular_routes_giethoorn_duration: "Duur: 2-3 uur",
        vaarkaart_popular_routes_giethoorn_distance: "Afstand: 8 km",
        vaarkaart_popular_routes_giethoorn_difficulty: "Moeilijkheidsgraad: Gemakkelijk",
        vaarkaart_popular_routes_giethoorn_highlights: "Hoogtepunten: Dorpsgezicht Giethoorn",
        vaarkaart_popular_routes_giethoorn_perfect_for: "Perfect voor beginners en families",

        vaarkaart_popular_routes_weerribben_route_title: "Weerribben natuurroute",
        vaarkaart_popular_routes_weerribben_route_start: "Start: Nijenhuis Botenverhuur",
        vaarkaart_popular_routes_weerribben_route_duration: "Duur: 4-5 uur",
        vaarkaart_popular_routes_weerribben_route_distance: "Afstand: 15 km",
        vaarkaart_popular_routes_weerribben_route_difficulty: "Moeilijkheidsgraad: Gemiddeld",
        vaarkaart_popular_routes_weerribben_route_highlights: "Hoogtepunten: Wilde dieren, vogels",
        vaarkaart_popular_routes_weerribben_route_for_nature_lovers: "Voor natuur- en vogelliefhebbers",

        vaarkaart_popular_routes_wanneperveen_title: "Wanneperveen rondvaart",
        vaarkaart_popular_routes_wanneperveen_start: "Start: Nijenhuis Botenverhuur",
        vaarkaart_popular_routes_wanneperveen_duration: "Duur: 1-2 uur",
        vaarkaart_popular_routes_wanneperveen_distance: "Afstand: 5 km",
        vaarkaart_popular_routes_wanneperveen_difficulty: "Moeilijkheidsgraad: Gemakkelijk",
        vaarkaart_popular_routes_wanneperveen_highlights: "Hoogtepunten: Dorpsgezicht Wanneperveen",
        vaarkaart_popular_routes_wanneperveen_short_route: "Korte route voor een snelle uitstap",

        vaarkaart_navigation_rules_title: "Vaarregels & Veiligheid",
        vaarkaart_navigation_rules_description: "Belangrijke informatie voor veilig varen",

        vaarkaart_navigation_rules_general_rules_title: "Algemene regels",
        vaarkaart_navigation_rules_general_rules_max_speed: "Maximum snelheid: 6 km/u",
        vaarkaart_navigation_rules_general_rules_lifejackets: "Zwemvesten verplicht",
        vaarkaart_navigation_rules_general_rules_alcohol: "Geen alcohol tijdens het varen",
        vaarkaart_navigation_rules_general_rules_respect_nature: "Respecteer de natuur",
        vaarkaart_navigation_rules_general_rules_distance_from_other_boats: "Houd afstand van andere boten",

        vaarkaart_navigation_rules_safety_tips_title: "Veiligheidstips",
        vaarkaart_navigation_rules_safety_tips_check_weather: "Controleer het weer voor vertrek",
        vaarkaart_navigation_rules_safety_tips_bring_water: "Neem voldoende water mee",
        vaarkaart_navigation_rules_safety_tips_charge_phone: "Zorg voor een opgeladen telefoon",
        vaarkaart_navigation_rules_safety_tips_know_rules: "Ken de vaarregels",
        vaarkaart_navigation_rules_safety_tips_stay_on_navigable_routes: "Blijf op de bevaarbare routes",

        vaarkaart_navigation_rules_emergency_numbers_title: "Noodnummers",
        vaarkaart_navigation_rules_emergency_numbers_general_alarm: "Algemeen alarmnummer: 112",
        vaarkaart_navigation_rules_emergency_numbers_nijenhuis: "Nijenhuis Botenverhuur: 0522 281 528",
        vaarkaart_navigation_rules_emergency_numbers_water_police: "Waterpolitie: 0900-8844",
        vaarkaart_navigation_rules_emergency_numbers_weather_report: "Weerbericht: 0900-9722",
        vaarkaart_navigation_rules_emergency_numbers_rescue_brigade: "Reddingsbrigade: 0900-0112",

        /* contact page */
        contact_title: "Contact",
        contact_p: "Neem contact met ons op voor vragen, reserveringen of meer informatie",

        contact_h2: "Contactgegevens",
        contact_h2_p: "Wij staan klaar om jou te helpen",

        contact_h3: "Contact informatie",

        contact_address_title: "Adres",
        contact_address: "Veneweg 199",
        contact_zip: "7946 LP Wanneperveen",
        contact_country: "Nederland",

        contact_phone_title: "Telefoon",
        contact_phone: "0522 281 528",

        contact_opening_title: "Openingstijden",
        contact_opening_p: "Dagelijks: 09:00 - 18:00 uur",
        contact_season_p: "Seizoen: 1 april - 1 november",

        contact_business_title: "Bedrijfsgegevens",
        contact_kvk: "Kvk: 6769 7097",
        contact_btw: "Btw nr: NL 195153637 B01",

        contact_call_title: "Direct contact",
        contact_call_p: "Voor vragen, reserveringen of meer informatie, bel ons direct:",
        contact_call_button: "Bel Nu",
        contact_call_info_p: "Beschikbaar: Dagelijks van 09:00 - 18:00 uur",
        contact_call_info_p2: "Seizoen: 1 april - 1 november",

        contact_map_title: "Waar vind je ons?",
        contact_map_p: "Bekijk onze locatie op de kaart",
      },
  
      /* ---------- German – informal (“du”) ------------------- */
      de: {
        nav_opening: "Öffnungszeiten: 9:00 – 18:00 Uhr",
        nav_boats: "Bootsverleih",
        nav_house: "Ferienhaus",
        nav_forsale: "Zu verkaufen",
        nav_camping: "Camping",
        nav_marina: "Yachthafen",
        nav_chart: "Seekarte",
        nav_contact: "Kontakt",
        /* index.html */
        hero_book_h2: "Direkt buchen",
        hero_book_p: "Buche einfach dein Boot für einen Tag auf dem Wasser",
        hero_book_date: "Datum",
        hero_book_boat_type: "Boot type",
        hero_book_boat_type_select: "Boot type wählen",
        hero_book_btn: "Verfügbarkeit prüfen",
        btn_outline: "📞 Jetzt anrufen!",
        hero_book_badge: "100% sicher &amp; unverbindlich",
        hero_h1: "Entflieh ins Naturparadies",
        hero_h1_p: "Erlebe die Schönheit des Weerribben-Gebiets mit unserem Premium-Bootsverleih – perfekt für Familien, Freunde und Naturliebhaber.",
        hero_btn: "Verfügbarkeit prüfen",
        intro_h2: "Raus aus dem Alltag – rein ins Abenteuer",   
        intro_h2_p: "In der hektischen Welt von heute hat jeder eine Gelegenheit, sich loszulassen. Lassen Sie sich von Dateien, Stress und der täglichen Routine verziehen. Erleben Sie das Leben aus einer anderen Perspektive - von der Wasserseite.",
        intro_h2_p2: "Unsere Booten bieten die perfekte Möglichkeit, das atemberaubende Weerribben-Naturschutzgebiet zu erkunden und unvergessliche Erinnerungen für Sie und Ihre Lieben zu schaffen.",
        intro_h3: "Warum Nijenhuis wählen?",
        intro_h3_li1: "📍 Gelegen im Herzen des Weerribben-Naturschutzgebietes",
        intro_h3_li2: "🚤 Große Auswahl an Booten für alle Vorlieben",
        intro_h3_li3: "🌿 Verfügbarkeit milieubewusster elektrischer Booten",   
        intro_h3_li4: "👨‍👩‍👧‍👦 Perfekt für Familien und Gruppen",
        intro_h3_li5: "💰 Konkurrenzstarke Preise für alle Budgets",
        intro_h3_li6: "📞 Persönliche Service und Unterstützung", 
        intro_cta_p: "Für mehr Informationen, rufen Sie uns an 0522 - 281 528", 
        intro_cta_p2: "Barzahlung und PIN-Zahlung akzeptiert",
        services_h2: "Unsere Dienstleistungen",
        services_h3_1: "Bootsverleih",
        services_p_1: "Elektrische Booten, Kanus, Kajaks und SUP-Boards für alle Altersgruppen und Erfahrungsstufen.",
        services_btn_1: "Mehr erfahren",
        services_h3_2: "Ferienhaus",
        services_p_2: "Komfortable Ferienunterkünfte für Familien und Gruppen.",    
        services_btn_2: "Mehr erfahren",
        services_h3_3: "Camping",
        services_p_3: "Prachtige Campingplätze im Weerribben-Gebiet mit modernen Anlagen und atemberaubenden Aussichten.",
        services_btn_3: "Mehr erfahren",
        services_h3_4: "Yachthafen",
        services_p_4: "Professionelle Yachthafen-Dienstleistungen für Bootseigentümer mit sicherer Liegeplatz- und Wartungsdiensten.",  
        services_btn_4: "Mehr erfahren",
        map_h2: "Unser Standort",
        footer_p: "Hier beginnt Ihr Abenteuer in den wunderschönen Weerribben!",
        footer_bottom: "&copy; 2025 Nijenhuis Bootsverleih. Alle Rechte vorbehalten.", 
        footer_company_name: "Nijenhuis Bootsverleih",
        footer_company_location: "Camping & Yachthaven",
        footer_company_address: "Veneweg 199",
        footer_company_postal: "7946 LP Wanneperveen",
        footer_company_phone: "Tel: 0522 281 528",
        footer_company_kvk: "Kvk: 6769 7097",
        footer_company_btw: "Btw nr: NL 195153637 B01",
        footer_rights: "© 2025 Nijenhuis Bootsverleih. Alle Rechte vorbehalten.",
        /* boats page */
        boats_header_h1: "Bootsverleih",
        boats_header_p: "Steig ein und entdecke das wunderschöne Weerribben-Naturschutzgebiet mit unseren Booten, Kanus und Kajaks!",
        boats_h2: "Unsere Boote",
        boats_intro: "Wir bieten eine große Auswahl an Booten für alle Vorlieben und Erfahrungsstufen",
        boats_cat_electric: "Elektroboote",
        boats_cat_electric_desc: "Umweltfreundliche Boote mit Elektroantrieb – perfekt für ruhige Touren durch die Natur.",
        boats_cat_gasoline: "Benzinboote",
        boats_cat_gasoline_desc: "Leistungsstarke Boote mit Benzinmotor, ideal für größere Gruppen und längere Ausflüge.",
        boats_cat_sailing: "Segelboote",
        boats_cat_sailing_desc: "Traditionelle Segelboote für erfahrene Segler*innen. Genieße Wind und Natur.",
        boats_cat_canoe: "Kanus & Kajaks",
        boats_cat_canoe_desc: "Perfekt für sportliche Aktivitäten und zum Erkunden kleiner Wasserwege.",
        boats_cat_sup: "SUP-Boards",
        boats_cat_sup_desc: "Stand-Up-Paddle-Boards für ein außergewöhnliches Wassererlebnis.",
        boats_cat_all: "Alle Boote",
        boats_cat_all_desc: "Sieh dir alle verfügbaren Boote und Details an.",
        prices_h2: "Preise & Verfügbarkeit",
        prices_intro: "Alle Tagespreise beinhalten Sicherheitsausrüstung und Einweisung",
        prices_intro_2: "Die Kaution richtet sich nach dem Bootstyp und muss bar bezahlt werden.",
        prices_table_title: "Tagespreise Bootsverleih",
        prices_season: "Saison: 1. April – 1. November 2025",
        prices_th_type: "Bootstyp",
        prices_th_capacity: "Kapazität",
        prices_th_price: "Preis pro Tag",
        prices_th_deposit: "Kaution",
        multi_day_note: "Boote können auch für mehrere Tage gemietet werden. Kontaktiere uns für Infos und Tarife.",
        rentinfo_h2: "Mietinfo",
        rentinfo_intro: "Alles, was du über das Mieten wissen musst",
        rentinfo_book_title: "📅 Reservierungen",
        rentinfo_book_desc: "Reservierungen per Telefon oder online möglich. Wir empfehlen Vorab-Buchung, besonders in der Hochsaison.",
        rentinfo_open_title: "⏰ Öffnungszeiten",
        rentinfo_open_desc: "Täglich 09:00–18:00 Uhr während der Saison (1. April – 1. November).",
        rentinfo_pay_title: "💰 Zahlung",
        rentinfo_pay_desc: "Nur Barzahlung möglich. Kaution €50–€100 je nach Bootstyp. Sieh Preistabelle für Details.",
        /* Ferierhaus page */
        house_header_h1: "Ferienhaus",
        house_header_p1: "Erlebt einen herrlichen Aufenthalt in unserem Ferienhaus inmitten des wunderschönen Naturschutzgebietes Weerribben.",
        house_overview_h2: "Ferienhaus Belterwiede",
        house_overview_p1: "<strong>DAS GANZE JAHR GEÖFFNET</strong>",
        house_overview_h3: "Perfekte Basis in der Kop van Overijssel",
        house_overview_p2: "Möchtest du einen Wochenende, Mittwoch, eine Woche oder eine ganze Ferien in einem wunderschönen Natur- oder Wassersportgebiet verbringen? Dann komm nach der Kop van Overijssel, wo du Booten, Angeln, Schwimmen, Fahrradfahren, Wandern und Besuchen anderer Dörfer in der Umgebung genießen kannst. Das Haus ist direkt am Belterwijde-Meer gelegen.",
        house_overview_p3: "<strong>Untergeschoss:</strong> <span>Du hast 1 Schlafzimmer, ein Dusche, ein Toilette und eine Waschmaschine. Entspanne dich in der großen Wohnzimmer mit TV und Radio. Die Kammer hat eine offene Küche mit verschiedenen Haushaltsgeräten (Ofen, Mikrowelle, Kühlschrank). Es gibt eine große Halle und das Haus ist vollständig mit Zentralheizung ausgestattet.</span>",
        house_overview_p4: "<strong>Obergeschoss:</strong> <span>Du hast vier Schlafzimmer, von denen zwei ein Waschbecken haben. Es gibt auch eine Dusche und ein Toilette auf der zweiten Etage.</span>",
        house_overview_li1: "Kinderbett, Laufstall und Hochstuhl können im Wasserpark Belterwijde angefordert werden.",
        house_overview_li2: "Kuscheln und Decken verfügbar",
        house_overview_li3: "Bitte bringe dein eigenes Bettbezug",
        house_overview_li4: "Bettbezug kann auch gemietet werden (bitte vorab melden)",
        house_overview_li5: "Für weitere Fragen kontaktiere uns",
        house_amenities_h2: "Einrichtungen",
        house_amenities_p1: "Alles, was du für einen gemütlichen Aufenthalt brauchst",
        house_amenities_h3: "5 Schlafzimmer",
        house_amenities_p2: "1 Schlafzimmer unten, 4 Schlafzimmer oben (2 mit Waschbecken)",
        house_amenities_h4: "Offene Küche",
        house_amenities_p3: "Ofen, Mikrowelle, Kühlschrank und alle Haushaltsgeräte",
        house_amenities_h5: "2 Badezimmer",
        house_amenities_p4: "Dusche und Toilette auf beiden Etagen",
        house_amenities_h6: "Wohnzimmer",
        house_amenities_p5: "Großes Wohnzimmer mit TV und Radio",
        house_amenities_h7: "Wäschemaschine",
        house_amenities_p6: "Wäschemaschine im Haus verfügbar",
        house_amenities_h8: "Zentralheizung",
        house_amenities_p7: "Vollständig mit Zentralheizung ausgestattet",
        house_contact_h2: "Kontakt & Reservierungen",
        house_contact_p1: "Für mehr Informationen und Reservierungen", 
        house_contact_h3: "Waterpark Belterwiede",
        house_contact_p2: "E-mail: info@parkbelterwiede.nl",
        house_contact_p3: "Telefon: 0522-281828",
        /* te-koop page */
        te_koop_h1: "Zu verkaufen",
        te_koop_p1: "Schau dir hier unsere neuesten Angebote an.",
        te_koop_h2: "Chalets & Mobilheime",
        te_koop_h3: "Kein Angebot verfügbar",
        te_koop_p2: "Im Moment haben wir keine Chalets oder Mobilheime zum Verkauf. Sobald es neue Angebote gibt, findest du sie hier.",
        te_koop_p3: "Interessierst du dich zukünftig für ein Chalet oder Mobilheim? Melde dich gerne bei uns für mehr Infos oder um auf die Warteliste zu kommen.",
        te_koop_h4: "Interesse an einem Chalet oder Mobilheim?",
        te_koop_p4: "Kontaktier uns:",
        te_koop_p5: "📞 <strong>Telefon</strong>: +31522 281 528",
        te_koop_p6: "📍 <strong>Adresse</strong>: Veneweg 199, 7946 LP Wanneperveen",
        te_koop_p7: "⏰ <strong>Öffnungszeiten</strong>: Täglich 09:00 - 18:00 Uhr",
        
        /* camping page */

        camping_title: "Camping",
        camping_description: "Entspannen Sie sich vollkommen beim Camping mitten im wunderschönen Naturschutzgebiet De Weerribben.",

        camping_overview_title: "Unser Campingplatz",
        camping_overview_description: "Ein ruhiger und stimmungsvoller Campingplatz mitten in der Natur",

        camping_overview_permanent_description: "Unser Campingplatz bietet Saisonplätze mit festen Stellplätzen für Wohnwagen. Es ist ein gemütlicher, kleiner Campingplatz, auf dem jeder Platz einen eigenen Anlegeplatz hat.",
        camping_overview_permanent_list_item_1: "Permanente Saisonplätze",
        camping_overview_permanent_list_item_2: "Wasseranschluss",
        camping_overview_permanent_list_item_3: "Stromzähler",
        camping_overview_permanent_list_item_4: "Zentralantenne",    
        camping_overview_permanent_list_item_5: "Abwasseranschluss",
        camping_overview_permanent_list_item_6: "Eigener Anlegeplatz",
        camping_overview_permanent_list_item_7: "Duschen und Toiletten verfügbar",
        camping_overview_permanent_list_item_8: "Kleiner, aber gemütlicher Campingplatz",
        camping_overview_permanent_list_item_9: "Waschmaschine und Trockner verfügbar",
        camping_overview_cta_strong: "Interesse an einem dauerhaften Platz?",
        camping_overview_cta_button: "JETZT ANRUFEN",
        facilities_title: "Ausstattung",
        facilities_description: "Alle Einrichtungen für Dauerplätze",
        facilities_sanitary_title: "Sanitäranlagen",
        facilities_sanitary_description: "Duschen und Toiletten für alle Gäste verfügbar",
        facilities_electricity_title: "Strom",
        facilities_electricity_description: "Stromzähler auf jedem Platz für den eigenen Verbrauch",
        facilities_water_title: "Wasser",
        facilities_water_description: "Wasseranschluss auf jedem Platz verfügbar",
        facilities_antenna_title: "Zentralantenne",
        facilities_antenna_description: "Zentralantenne für TV-Empfang",
        facilities_mooring_title: "Eigener Bootsanleger",
        facilities_mooring_description: "Jeder Platz hat seinen eigenen Bootsanleger",
        facilities_sewerage_title: "Abwasser",
        facilities_sewerage_description: "Abwasseranschluss auf allen Plätzen verfügbar",
        /* jachthaven page */
        jachthaven_title: "Yachthafen",
        jachthaven_description: "Professionelle Yachthafendienste für Bootsbesitzer im Naturschutzgebiet Weerribben",
        jachthaven_ligplaatsen_title: "Yachthafen Liegeplätze",
        jachthaven_ligplaatsen_description: "Die Liegeplätze im Yachthafen sind ausschließlich für dauerhafte Jahresliegeplätze reserviert. Sie sind mit Wasser, Strom, Dusche und Toilette ausgestattet. Die maximale Wassertiefe beträgt ca. 1,00 m. Der Yachthafen liegt direkt am Belterwijde-See, von wo aus Sie in alle Richtungen fahren können.",
        jachthaven_ligplaatsen_cta: "Kontaktieren Sie uns gerne <a href='contact.html'>hier</a> für weitere Informationen zu Verfügbarkeit und Bedingungen.",
        jachthaven_services_title: "Yachthafendienste",
        jachthaven_services_description: "Umfassende Yachthafen-Einrichtungen für Ihr Boot",
        jachthaven_services_ligplaatsen_title: "Liegeplätze",
        jachthaven_services_ligplaatsen_description: "Sichere Liegeplätze für Boote bis zu 12 Metern. Tages- und Saisonliegeplätze verfügbar.",
        jachthaven_services_onderhoud_title: "Wartung",
        jachthaven_services_onderhoud_description: "Professionelle Wartung und Reparatur aller Bootstypen. Winterlager vorhanden.",
        jachthaven_services_faciliteiten_title: "Einrichtungen",
        jachthaven_services_faciliteiten_description: "Sanitäre Anlagen, Duschen, Waschmaschinen und Trockner für Gäste.",
        /* vaarkaart page */
        vaarkaart_title: "Wasserkarte",
        vaarkaart_description: "Navigationsinformationen und Routen für das Naturschutzgebiet Weerribben",
    
        vaarkaart_interactive_map_title: "Interaktive Wasserkarte",
        vaarkaart_interactive_map_description: "Entdecken Sie die schönsten Routen durch das Naturschutzgebiet Weerribben",
        vaarkaart_interactive_map_map_title: "Naturschutzgebiet Weerribben - Interaktive Wasserkarte",
        vaarkaart_interactive_map_attribution_source: "Quelle:",
        vaarkaart_interactive_map_attribution_source_link: "Waterkaart.net",
        vaarkaart_interactive_map_placeholder_title: "Interaktive Wasserkarte",
        vaarkaart_interactive_map_placeholder_description: "Für die aktuellsten und detailliertesten Wasserkarte des Weerribben-Gebiets besuchen Sie die professionelle Waterkaart der Niederlande.",
        vaarkaart_interactive_map_placeholder_button: "Öffne waterkaart.net",
        vaarkaart_interactive_map_footer_description: "Diese interaktive Wasserkarte wird von Waterkaart.net bereitgestellt. Für die aktuellsten Informationen und detaillierten Karten besuchen Sie deren Website.",
    
        vaarkaart_popular_routes_title: "Beliebte Routen",
        vaarkaart_popular_routes_description: "Entdecken Sie die schönsten Bootsfahrten in der Umgebung",
    
        vaarkaart_popular_routes_giethoorn_title: "Giethoorn Route",
        vaarkaart_popular_routes_giethoorn_start: "Start: Nijenhuis Bootsverleih",
        vaarkaart_popular_routes_giethoorn_duration: "Dauer: 2-3 Stunden",
        vaarkaart_popular_routes_giethoorn_distance: "Entfernung: 8 km",
        vaarkaart_popular_routes_giethoorn_difficulty: "Schwierigkeit: Einfach",
        vaarkaart_popular_routes_giethoorn_highlights: "Highlights: Dorfansicht Giethoorn",
        vaarkaart_popular_routes_giethoorn_perfect_for: "Perfekt für Anfänger und Familien",
    
        vaarkaart_popular_routes_weerribben_route_title: "Weerribben Naturroute",
        vaarkaart_popular_routes_weerribben_route_start: "Start: Nijenhuis Bootsverleih",
        vaarkaart_popular_routes_weerribben_route_duration: "Dauer: 4-5 Stunden",
        vaarkaart_popular_routes_weerribben_route_distance: "Entfernung: 15 km",
        vaarkaart_popular_routes_weerribben_route_difficulty: "Schwierigkeit: Mittel",
        vaarkaart_popular_routes_weerribben_route_highlights: "Highlights: Wildtiere, Vögel",
        vaarkaart_popular_routes_weerribben_route_for_nature_lovers: "Für Natur- und Vogel Liebhaber",
    
        vaarkaart_popular_routes_wanneperveen_title: "Wanneperveen Rundfahrt",
        vaarkaart_popular_routes_wanneperveen_start: "Start: Nijenhuis Bootsverleih",
        vaarkaart_popular_routes_wanneperveen_duration: "Dauer: 1-2 Stunden",
        vaarkaart_popular_routes_wanneperveen_distance: "Entfernung: 5 km",
        vaarkaart_popular_routes_wanneperveen_difficulty: "Schwierigkeit: Einfach",
        vaarkaart_popular_routes_wanneperveen_highlights: "Highlights: Dorfansicht Wanneperveen",
        vaarkaart_popular_routes_wanneperveen_short_route: "Kurze Route für einen schnellen Ausflug",
    
        vaarkaart_navigation_rules_title: "Fahrregeln & Sicherheit",
        vaarkaart_navigation_rules_description: "Wichtige Informationen für sicheres Bootfahren",
    
        vaarkaart_navigation_rules_general_rules_title: "Allgemeine Regeln",
        vaarkaart_navigation_rules_general_rules_max_speed: "Höchstgeschwindigkeit: 6 km/h",
        vaarkaart_navigation_rules_general_rules_lifejackets: "Rettungswesten Pflicht",
        vaarkaart_navigation_rules_general_rules_alcohol: "Kein Alkohol beim Fahren",
        vaarkaart_navigation_rules_general_rules_respect_nature: "Respektieren Sie die Natur",
        vaarkaart_navigation_rules_general_rules_distance_from_other_boats: "Halten Sie Abstand von anderen Booten",
    
        vaarkaart_navigation_rules_safety_tips_title: "Sicherheitstipps",
        vaarkaart_navigation_rules_safety_tips_check_weather: "Überprüfen Sie das Wetter vor der Abfahrt",
        vaarkaart_navigation_rules_safety_tips_bring_water: "Nehmen Sie ausreichend Wasser mit",
        vaarkaart_navigation_rules_safety_tips_charge_phone: "Stellen Sie sicher, dass Ihr Telefon aufgeladen ist",
        vaarkaart_navigation_rules_safety_tips_know_rules: "Kennen Sie die Fahrregeln",
        vaarkaart_navigation_rules_safety_tips_stay_on_navigable_routes: "Bleiben Sie auf schiffbaren Routen",
    
        vaarkaart_navigation_rules_emergency_numbers_title: "Notrufnummern",
        vaarkaart_navigation_rules_emergency_numbers_general_alarm: "Allgemeiner Notruf: 112",
        vaarkaart_navigation_rules_emergency_numbers_nijenhuis: "Nijenhuis Bootsverleih: 0522 281 528",
        vaarkaart_navigation_rules_emergency_numbers_water_police: "Wasserschutzpolizei: 0900-8844",
        vaarkaart_navigation_rules_emergency_numbers_weather_report: "Wetterbericht: 0900-9722",
        vaarkaart_navigation_rules_emergency_numbers_rescue_brigade: "Rettungsbrigade: 0900-0112",
        /* contact page */
        contact_title: "Kontakt",
        contact_p: "Kontaktieren Sie uns für Fragen, Reservierungen oder weitere Informationen",

        contact_h2: "Kontaktdaten",
        contact_h2_p: "Wir sind für Sie da",

        contact_h3: "Kontaktinformationen",

        contact_address_title: "Adresse",
        contact_address: "Veneweg 199",
        contact_zip: "7946 LP Wanneperveen",
        contact_country: "Niederlande",

        contact_phone_title: "Telefon",
        contact_phone: "0522 281 528",

        contact_opening_title: "Öffnungszeiten",
        contact_opening_p: "Täglich: 09:00 - 18:00 Uhr",
        contact_season_p: "Saison: 1. April - 1. November",

        contact_business_title: "Firmendaten",
        contact_kvk: "Handelsregister: 6769 7097",
        contact_btw: "USt.-Nr.: NL 195153637 B01",

        contact_call_title: "Direkter Kontakt",
        contact_call_p: "Für Fragen, Reservierungen oder weitere Informationen rufen Sie uns direkt an:",
        contact_call_button: "Jetzt Anrufen",
        contact_call_info_p: "Verfügbar: Täglich von 09:00 - 18:00 Uhr",
        contact_call_info_p2: "Saison: 1. April - 1. November",

        contact_map_title: "Wo finden Sie uns?",
        contact_map_p: "Sehen Sie unseren Standort auf der Karte",
        
      },

  
      /* ---------- English ------------------------------------ */
      en: {
        nav_opening: "Opening hours: 9:00 am – 6:00 pm",
        nav_boats: "Boat Rental",
        nav_house: "Holiday Home",
        nav_forsale: "For Sale",
        nav_camping: "Camping",
        nav_marina: "Marina",
        nav_chart: "Water Map",
        nav_contact: "Contact",
        /* index.html */
        hero_book_h2: "Book directly",
        hero_book_p: "Simply book your boat for a day on the water",
        hero_book_date: "Datum",
        hero_book_boat_type: "Boot type",
        hero_book_boat_type_select: "Choose a boat",
        hero_book_btn: "Check availability",
        btn_outline: "📞 Call now!",
        hero_book_badge: "100% safe &amp; free",
        hero_h1: "Escape to Nature’s Paradise",
        hero_h1_p: "Experience the beauty of the Weerribben nature reserve with our premium boat rentals – perfect for families, friends and nature lovers.",
        hero_btn: "Check availability",
        intro_h2: "Take a break from daily routine",    
        intro_h2_p: "In the hectic world of today, everyone needs a moment to unwind. Let go of files, stress and the daily routine. Experience life from a different perspective - from the water's edge.",
        intro_h2_p2: "Our boats offer the perfect way to explore the breathtaking Weerribben nature reserve and create unforgettable memories for you and your loved ones.",
        intro_h3: "Why choose Nijenhuis?",
        intro_h3_li1: "📍 Located in the heart of the Weerribben nature reserve",
        intro_h3_li2: "🚤 Wide range of boats for all preferences",
        intro_h3_li3: "🌿 Availability of eco-friendly electric boats", 
        intro_h3_li4: "👨‍👩‍👧‍👦 Perfect for families and groups",
        intro_h3_li5: "💰 Competitive prices for all budgets",
        intro_h3_li6: "📞 Personal service and support", 
        intro_cta_p: "For more information, call 0522 - 281 528",
        intro_cta_p2: "Cash and pin payments accepted",
        services_h2: "Our Services",
        services_h3_1: "Boat Rental",
        services_p_1: "Electric boats, canoes, kayaks and SUP boards for all ages and experience levels.",
        services_btn_1: "Learn More",
        services_h3_2: "Holiday Home",
        services_p_2: "Comfortable holiday accommodation perfect for families and groups.",
        services_btn_2: "Learn More",
        services_h3_3: "Camping",
        services_p_3: "Beautiful camping sites in the Weerribben area with modern facilities and breathtaking views.",
        services_btn_3: "Learn More",
        services_h3_4: "Marina",
        services_p_4: "Professional marina services for boat owners with secure docking and maintenance.",
        services_btn_4: "Learn More",
        map_h2: "Our Location",
        footer_p: "Your adventure in the beautiful Weerribben starts here!",
        footer_bottom: "&copy; 2025 Nijenhuis Boat Rental. All rights reserved.", 
        footer_company_name: "Nijenhuis Boat Rental",
        footer_company_location: "Camping & Marina",
        footer_company_address: "Veneweg 199",
        footer_company_postal: "7946 LP Wanneperveen",
        footer_company_phone: "Tel: 0522 281 528",
        footer_company_kvk: "Kvk: 6769 7097",
        footer_company_btw: "Btw nr: NL 195153637 B01",
        footer_rights: "© 2025 Nijenhuis Boat Rental. All rights reserved.",
        /* boats page */
        boats_header_h1: "Boat Rental",
        boats_header_p: "Hop on board and explore the stunning Weerribben area with our boats, canoes and kayaks!",
        boats_h2: "Our Boats",
        boats_intro: "We offer a wide range of boats for all preferences and experience levels",
        boats_cat_electric: "Electric boats",
        boats_cat_electric_desc: "Eco-friendly boats with electric propulsion – ideal for quiet cruises through nature.",
        boats_cat_gasoline: "Petrol boats",
        boats_cat_gasoline_desc: "Powerful petrol-engine boats, perfect for larger groups and longer trips.",
        boats_cat_sailing: "Sailboats",
        boats_cat_sailing_desc: "Traditional sailboats for the experienced sailor. Enjoy the wind and nature.",
        boats_cat_canoe: "Canoes & Kayaks",
        boats_cat_canoe_desc: "Perfect for sporty activities and exploring smaller waterways.",
        boats_cat_sup: "SUP boards",
        boats_cat_sup_desc: "Stand-up paddleboards for a unique water experience.",
        boats_cat_all: "All boats",
        boats_cat_all_desc: "View all available boats and details.",
        prices_h2: "Prices & Availability",
        prices_intro: "All daily prices include safety equipment and briefing",
        prices_intro_2: "The deposit depends on the type of boat and must be paid in cash.",	
        prices_table_title: "Daily Boat-Rental Prices",
        prices_season: "Season: 1 April – 1 November 2025",
        prices_th_type: "Boat type",
        prices_th_capacity: "Capacity",
        prices_th_price: "Price per day",
        prices_th_deposit: "Deposit",
        multi_day_note: "Boats can be rented for multiple days. Contact us for more information and rates.",
        rentinfo_h2: "Rental Information",
        rentinfo_intro: "Everything you need to know about renting a boat",
        rentinfo_book_title: "📅 Reservations",
        rentinfo_book_desc: "Reservations can be made by phone or online. We recommend booking in advance, especially during high season.",
        rentinfo_open_title: "⏰ Opening hours",
        rentinfo_open_desc: "Open daily 09:00-18:00 during the season (1 April – 1 November).",
        rentinfo_pay_title: "💰 Payment",
        rentinfo_pay_desc: "Cash payment only. A €50–€100 deposit is required depending on boat type. See price table for specific deposits.",
        /* house page */
        house_header_h1: "Holiday home",
        house_header_p1: "Enjoy a wonderful stay in our holiday home, right in the beautiful nature reserve De Weerribben.",
        house_overview_h2: "Holiday Home Belterwiede",
        house_overview_p1: "<strong>OPEN ALL YEAR ROUND</strong>",
        house_overview_h3: "Perfect base in the Kop van Overijssel",
        house_overview_p2: "Would you like to spend a weekend, midweek, a week, or a whole holiday in a beautiful nature or watersports area? Then come to the Kop van Overijssel, where you can enjoy boating, fishing, swimming, cycling, hiking, and visiting other villages nearby. The house is located directly on Lake Belterwijde.",
        house_overview_p3: "<strong>Downstairs:</strong> <span>You have 1 bedroom, a shower, a toilet, and a washing machine. Relax in the spacious living room with TV and radio. The room has an open kitchen with various household appliances (oven, microwave, fridge). There is a large hallway, and the house is fully equipped with central heating.</span>",
        house_overview_p4: "<strong>Upstairs:</strong> <span>You have four bedrooms, two of which have a washbasin. There is also a shower and toilet on the second floor.</span>",
        house_overview_li1: "Crib, playpen, and high chair can be requested at the Waterpark Belterwijde.",
        house_overview_li2: "Pillows and duvets provided",
        house_overview_li3: "Please bring your own bed linen",
        house_overview_li4: "Bed linen can also be rented from us (please notify in advance)",
        house_overview_li5: "For further questions, you can contact us",
        house_amenities_h2: "Facilities",
        house_amenities_p1: "Everything for a comfortable stay",
        house_amenities_h3: "5 Bedrooms",
        house_amenities_p2: "1 bedroom downstairs, 4 bedrooms upstairs (2 with washbasin)",
        house_amenities_h4: "Open Kitchen",
        house_amenities_p3: "Oven, microwave, fridge, and all household appliances",
        house_amenities_h5: "2 Bathrooms",
        house_amenities_p4: "Shower and toilet on both floors",
        house_amenities_h6: "Living Room",
        house_amenities_p5: "Spacious living room with TV and radio",
        house_amenities_h7: "Washing Machine",
        house_amenities_p6: "Washing machine available in the house",
        house_amenities_h8: "Central Heating",
        house_amenities_p7: "Fully heated for comfort all year round",
        house_contact_h2: "Contact & Reservations",
        house_contact_p1: "For more information and bookings",
        house_contact_h3: "Waterpark Belterwiede",
        house_contact_p2: "Email: info@parkbelterwiede.nl",
        house_contact_p3: "Phone: 0522-281828",
        /* te-koop page */
        te_koop_h1: "For Sale",
        te_koop_p1: "Check out our latest offers here.",
        te_koop_h2: "Chalets & Mobile Homes",
        te_koop_h3: "No offers available",
        te_koop_p2: "At the moment, we don’t have any chalets or mobile homes for sale. As soon as new offers come in, you’ll find them here.",
        te_koop_p3: "Interested in a chalet or mobile home in the future? Feel free to contact us for more info or to join the waiting list.",
        te_koop_h4: "Interested in a chalet or mobile home?",
        te_koop_p4: "Get in touch with us:",
        te_koop_p5: "📞 <strong>Phone</strong>: +31522 281 528",
        te_koop_p6: "📍 <strong>Address</strong>: Veneweg 199, 7946 LP Wanneperveen",
        te_koop_p7: "⏰ <strong>Opening Hours</strong>: Daily 09:00 - 18:00",

        /* camping page */
        camping_title: "Camping",
        camping_description: "Completely relax while camping in the middle of the beautiful nature reserve De Weerribben.",

        camping_overview_title: "Our campsite",
        camping_overview_description: "A quiet and atmospheric campsite in the middle of nature",

        camping_overview_permanent_description: "Our campsite offers seasonal pitches with permanent spots for caravans. It is a cozy, small campsite where each pitch has its own mooring place.",
        camping_overview_permanent_list_item_1: "Permanent seasonal pitches",
        camping_overview_permanent_list_item_2: "Water connection",
        camping_overview_permanent_list_item_3: "Electricity meter",
        camping_overview_permanent_list_item_4: "Central antenna",
        camping_overview_permanent_list_item_5: "Sewer connection",
        camping_overview_permanent_list_item_6: "Own mooring place",
        camping_overview_permanent_list_item_7: "Showers and toilets available",
        camping_overview_permanent_list_item_8: "Small but cozy campsite",
        camping_overview_permanent_list_item_9: "Washing machine and dryer available",
        camping_overview_cta_strong: "Interested in a permanent pitch?",
        camping_overview_cta_button: "CALL NOW",
        facilities_title: "Facilities",
        facilities_description: "All facilities for permanent pitches",
        facilities_sanitary_title: "Sanitary Facilities",
        facilities_sanitary_description: "Showers and toilets available for all guests",
        facilities_electricity_title: "Electricity",
        facilities_electricity_description: "Electricity meter at each pitch for personal use",
        facilities_water_title: "Water",
        facilities_water_description: "Water connection available at each pitch",
        facilities_antenna_title: "Central antenna",
        facilities_antenna_description: "Central antenna for TV reception",
        facilities_mooring_title: "Own Mooring",
        facilities_mooring_description: "Each pitch has its own mooring",
        facilities_sewerage_title: "Sewer Connection",
        facilities_sewerage_description: "Sewer connection available at all pitches",
        /* jachthaven page */
        jachthaven_title: "Marina",
        jachthaven_description: "Professional marina services for boat owners in the Weerribben nature reserve",
        jachthaven_ligplaatsen_title: "Marina Moorings",
        jachthaven_ligplaatsen_description: "Moorings in the marina are for permanent annual moorings only. They are equipped with water, electricity, shower, and toilet. The maximum depth is approximately 1.00 m. The marina is located directly on Lake Belterwijde, from where you can sail in all directions.",
        jachthaven_ligplaatsen_cta: "Feel free to <a href='contact.html'>contact us</a> for more information about availability and conditions.",
        jachthaven_services_title: "Marina Services",
        jachthaven_services_description: "Complete marina facilities for your boat",
        jachthaven_services_ligplaatsen_title: "Moorings",
        jachthaven_services_ligplaatsen_description: "Secure moorings for boats up to 12 meters. Daily and seasonal moorings available.",
        jachthaven_services_onderhoud_title: "Maintenance",
        jachthaven_services_onderhoud_description: "Professional maintenance and repair of all boat types. Winter storage available.",
        jachthaven_services_faciliteiten_title: "Facilities",
        jachthaven_services_faciliteiten_description: "Sanitary facilities, showers, washing machines, and dryers for guests.",
        /* vaarkaart page */
        vaarkaart_title: "Water Map",
        vaarkaart_description: "Navigation info and routes for the Weerribben nature reserve",

        vaarkaart_interactive_map_title: "Interactive Water Map",
        vaarkaart_interactive_map_description: "Discover the most beautiful routes through the Weerribben nature reserve",
        vaarkaart_interactive_map_map_title: "Weerribben Nature Reserve - Interactive Water Map",
        vaarkaart_interactive_map_attribution_source: "Source:",
        vaarkaart_interactive_map_attribution_source_link: "Waterkaart.net",
        vaarkaart_interactive_map_placeholder_title: "Interactive Water Map",
        vaarkaart_interactive_map_placeholder_description: "For the most current and detailed water maps of the Weerribben area, visit the professional Waterkaart of the Netherlands.",
        vaarkaart_interactive_map_placeholder_button: "Open waterkaart.net",
        vaarkaart_interactive_map_footer_description: "This interactive water map is provided by Waterkaart.net. For the latest info and detailed maps, visit their website.",

        vaarkaart_popular_routes_title: "Popular Routes",
        vaarkaart_popular_routes_description: "Discover the most beautiful boating routes in the area",

        vaarkaart_popular_routes_giethoorn_title: "Giethoorn Route",
        vaarkaart_popular_routes_giethoorn_start: "Start: Nijenhuis Boat Rental",
        vaarkaart_popular_routes_giethoorn_duration: "Duration: 2-3 hours",
        vaarkaart_popular_routes_giethoorn_distance: "Distance: 8 km",
        vaarkaart_popular_routes_giethoorn_difficulty: "Difficulty: Easy",
        vaarkaart_popular_routes_giethoorn_highlights: "Highlights: Village view of Giethoorn",
        vaarkaart_popular_routes_giethoorn_perfect_for: "Perfect for beginners and families",


        vaarkaart_popular_routes_weerribben_route_title: "Weerribben Nature Route",
        vaarkaart_popular_routes_weerribben_route_start: "Start: Nijenhuis Boat Rental",
        vaarkaart_popular_routes_weerribben_route_duration: "Duration: 4-5 hours",
        vaarkaart_popular_routes_weerribben_route_distance: "Distance: 15 km",
        vaarkaart_popular_routes_weerribben_route_difficulty: "Difficulty: Medium",
        vaarkaart_popular_routes_weerribben_route_highlights: "Highlights: Wildlife, birds",
        vaarkaart_popular_routes_weerribben_route_for_nature_lovers: "For nature and bird lovers",

        vaarkaart_popular_routes_wanneperveen_title: "Wanneperveen Boat Tour",
        vaarkaart_popular_routes_wanneperveen_start: "Start: Nijenhuis Boat Rental",
        vaarkaart_popular_routes_wanneperveen_duration: "Duration: 1-2 hours",
        vaarkaart_popular_routes_wanneperveen_distance: "Distance: 5 km",
        vaarkaart_popular_routes_wanneperveen_difficulty: "Difficulty: Easy",
        vaarkaart_popular_routes_wanneperveen_highlights: "Highlights: Village view of Wanneperveen",
        vaarkaart_popular_routes_wanneperveen_short_route: "Short route for a quick trip",

        vaarkaart_navigation_rules_title: "Navigation Rules & Safety",
        vaarkaart_navigation_rules_description: "Important information for safe boating",

        vaarkaart_navigation_rules_general_rules_title: "General Rules",
        vaarkaart_navigation_rules_general_rules_max_speed: "Maximum speed: 6 km/h",
        vaarkaart_navigation_rules_general_rules_lifejackets: "Life jackets mandatory",
        vaarkaart_navigation_rules_general_rules_alcohol: "No alcohol while boating",
        vaarkaart_navigation_rules_general_rules_respect_nature: "Respect nature",
        vaarkaart_navigation_rules_general_rules_distance_from_other_boats: "Keep distance from other boats",

        vaarkaart_navigation_rules_safety_tips_title: "Safety Tips",
        vaarkaart_navigation_rules_safety_tips_check_weather: "Check the weather before departure",
        vaarkaart_navigation_rules_safety_tips_bring_water: "Bring enough water",
        vaarkaart_navigation_rules_safety_tips_charge_phone: "Make sure your phone is charged",
        vaarkaart_navigation_rules_safety_tips_know_rules: "Know the navigation rules",
        vaarkaart_navigation_rules_safety_tips_stay_on_navigable_routes: "Stay on navigable routes",

        vaarkaart_navigation_rules_emergency_numbers_title: "Emergency Numbers",
        vaarkaart_navigation_rules_emergency_numbers_general_alarm: "General emergency number: 112",
        vaarkaart_navigation_rules_emergency_numbers_nijenhuis: "Nijenhuis Boat Rental: 0522 281 528",
        vaarkaart_navigation_rules_emergency_numbers_water_police: "Water police: 0900-8844",
        vaarkaart_navigation_rules_emergency_numbers_weather_report: "Weather report: 0900-9722",
        vaarkaart_navigation_rules_emergency_numbers_rescue_brigade: "Rescue brigade: 0900-0112",
        /* contact page */
        contact_title: "Contact",
        contact_p: "Contact us for questions, reservations or more information",

        contact_h2: "Contact Details",
        contact_h2_p: "We are here to help you",

        contact_h3: "Contact Information",

        contact_address_title: "Address",
        contact_address: "Veneweg 199",
        contact_zip: "7946 LP Wanneperveen",
        contact_country: "Netherlands",

        contact_phone_title: "Phone",
        contact_phone: "0522 281 528",

        contact_opening_title: "Opening Hours",
        contact_opening_p: "Daily: 09:00 AM - 06:00 PM",
        contact_season_p: "Season: April 1 - November 1",

        contact_business_title: "Company Details",
        contact_kvk: "Chamber of Commerce: 6769 7097",
        contact_btw: "VAT No.: NL 195153637 B01",

        contact_call_title: "Direct Contact",
        contact_call_p: "For questions, reservations or more information, call us directly:",
        contact_call_button: "Call Now",
        contact_call_info_p: "Available: Daily from 09:00 AM - 06:00 PM",
        contact_call_info_p2: "Season: April 1 - November 1",

        contact_map_title: "Where to find us?",
        contact_map_p: "View our location on the map",
    
        
        },
    
    };
  
    /* ---------- 2. STATE MANAGEMENT -------------------------- */
    const DEFAULT_LANG = "nl";
    const LANG_KEY = "selected-language";
  
    function getStoredLang() {
      return localStorage.getItem(LANG_KEY) || DEFAULT_LANG;
    }
  
    function storeLang(lang) {
      localStorage.setItem(LANG_KEY, lang);
    }
  
    /* ---------- 3. APPLY TRANSLATION ------------------------- */
    function applyTranslations(lang) {
      const dict = t[lang] || t[DEFAULT_LANG];
  
      /* Text content */
      document.querySelectorAll("[data-i18n]").forEach(el => {
        const key = el.getAttribute("data-i18n");
        if (dict[key]) el.innerHTML = dict[key];
      });
  
      /* Attribute values */
      document.querySelectorAll("[data-i18n-attr]").forEach(el => {
        const key = el.getAttribute("data-i18n");
        const attrs = el.getAttribute("data-i18n-attr").split(",");
        attrs.forEach(attr => {
          if (dict[key]) el.setAttribute(attr.trim(), dict[key]);
        });
      });
  
      /* Update active state on flag buttons */
      document
        .querySelectorAll("#languageSwitcher .lang-btn")
        .forEach(btn => btn.classList.toggle("active", btn.dataset.lang === lang));
    }
  
    /* ---------- 4. LANGUAGE SWITCHER UI ---------------------- */
    function buildSwitcher() {
      const switcher = document.getElementById("languageSwitcher");
      if (!switcher) return;

      const langs = [
        { code: "nl", flag: "nl.svg", label: "Nederlands" },
        { code: "de", flag: "de.svg", label: "Deutsch" },
        { code: "en", flag: "gb.svg", label: "English" }
      ];

      langs.forEach(({ code, flag, label }) => {
        const btn = document.createElement("button");
        btn.className = "lang-btn";
        btn.dataset.lang = code;
        btn.setAttribute("aria-label", label);
        btn.innerHTML = `<img src="../flags/${flag}" alt="${label}" class="flag-icon" />`;
        btn.addEventListener("click", () => {
          storeLang(code);
          applyTranslations(code);
        });
        switcher.appendChild(btn);
      });

      /* Minimal CSS-in-JS for circular flag buttons */
      const css = document.createElement("style");
      css.textContent = `
        #languageSwitcher {
          display: flex; gap: .75rem;
        }
        #languageSwitcher .lang-btn {
          width: 32px; height: 32px;
          border-radius: 50%;
          border: 2px solid transparent;
          padding: 0; cursor: pointer;
          background: none;
          display: flex; align-items: center; justify-content: center;
        }
        #languageSwitcher .lang-btn.active {
          border-color: var(--primary-color, #007bff);
        }
        #languageSwitcher .flag-icon {
          width: 24px; height: 24px;
          border-radius: 50%;
          object-fit: cover;
          display: block;
        }
      `;
      document.head.appendChild(css);
    }
  
    /* ---------- 5. INITIALISE ON DOM READY ------------------- */
    document.addEventListener("DOMContentLoaded", () => {
      buildSwitcher();
      applyTranslations(getStoredLang());
    });
  
    /* --------- 6. Expose API for other scripts --------------- */
    window.setLanguage = lang => {
      if (t[lang]) {
        storeLang(lang);
        applyTranslations(lang);
      }
    };
  })();
