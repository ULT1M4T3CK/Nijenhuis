(() => {
  /* ---------- 1. DICTIONARY -------------------------------- */
  const t = {
    nl: {
      /* global */
      nav_opening: "Openingstijden: 9:00 - 18:00",
      nav_boats: "Botenverhuur",
      nav_house: "Vakantiehuis",
      nav_forsale: "Te koop",
      nav_camping: "Camping",
      nav_chart: "Vaarkaart",
      nav_blog: "Blog",
      nav_faq: "Veelgestelde vragen",
      nav_more: "Meer",
      nav_contact: "Contact",
      /* Boat Modal & Dynamic JS */
      boat_modal_description_title: "Beschrijving",
      boat_modal_features_title: "Kenmerken",
      boat_modal_rates_title: "Tarieven",
      boat_modal_capacity: "{n} personen capaciteit",
      capacity_short: "{n} pers.",
      feature_electric_motor: "Elektrische motor",
      feature_silent_eco: "Stil en milieuvriendelijk",
      feature_sailing: "Zeilen",
      feature_traditional: "Traditioneel",
      feature_paddling: "Paddelen",
      feature_sporty: "Sportief",
      feature_sup: "Stand-up paddleboard",
      feature_unique: "Unieke ervaring",
      price_per_day: "€{price} per dag",
      price_per_day_without_motor: "€{price} / dag (zonder motor)",
      price_per_day_with_motor: "€{price} / dag (met motor)",
      price_deposit: "Borg: €{price}",
      price_deposit_none: "Geen borg vereist",
      status_available: "Beschikbaar",
      status_occupied: "Bezet",
      btn_more_info: "ℹ️ Meer info",
      btn_reserve: "📅 Reserveren",
      btn_close: "Sluiten",
      rate_duration: "Duur",
      rate_price: "Prijs",
      duration_day_1: "1 dag",
      duration_day_other: "{n} dagen",
      duration_week_1: "1 week",

      /* index.html */
      hero_book_h2: "Direct boeken",
      hero_book_p: "Reserveer eenvoudig je boot voor een dag op het water",
      hero_book_date: "Datum",
      hero_book_boat_type: "Boot type",
      hero_book_boat_type_select: "Selecteer een boot",
      hero_book_boat_type_classic_tender_720: "Classic tender 720 10/12 pers",
      hero_book_boat_type_classic_tender_570: "Classic tender 570 8 pers",
      hero_book_boat_type_electrosloop_10: "Electrosloep voor 10 pers",
      hero_book_boat_type_electrosloop_8: "Electrosloep voor 8 pers",
      hero_book_boat_type_electroboat_5: "Electrosloep voor 5 pers",
      hero_book_boat_type_sailboat_4_5: "Zeilboot",
      hero_book_boat_type_sailpunter_3_4: "Zeilpunter 3/4 pers",
      hero_book_boat_type_canoe_3: "Canadese kano 3 pers",
      hero_book_boat_type_kayak_2: "Kajak 2 pers",
      hero_book_boat_type_kayak_1: "Kajak 1 pers",
      hero_book_boat_type_sup_board: "SUP board 1 pers",
      /* Boat descriptions and features */
      boat_classic_tender_720_name: "Classic tender 720",
      boat_classic_tender_720_description: "Een luxe elektrische tender voor grotere groepen. Perfect voor comfortabele vaartochten door het natuurgebied.",
      boat_classic_tender_720_features: "10-12 personen capaciteit, Elektrische motor, Luxe en comfortabel, Stil en milieuvriendelijk, Perfect voor grotere groepen, Huisdieren niet toegestaan",

      boat_classic_tender_570_name: "Classic tender 570",
      boat_classic_tender_570_description: "Een elegante elektrische tender voor middelgrote groepen. Ideaal voor ontspannen vaartochten.",
      boat_classic_tender_570_features: "8 personen capaciteit, Elektrische motor, Elegant en comfortabel, Stil en milieuvriendelijk, Perfect voor families, Huisdieren niet toegestaan",

      boat_electrosloop_10_name: "Electrosloep voor 10 pers",
      boat_electrosloop_10_description: "Een ruime elektrische sloep voor grotere groepen. Perfect voor gezellige vaartochten.",
      boat_electrosloop_10_features: "10 personen capaciteit, Elektrische motor, Ruim en comfortabel, Stil en milieuvriendelijk, Perfect voor groepen, Huisdieren welkom",

      boat_electrosloop_8_name: "Electrosloep voor 8 pers",
      boat_electrosloop_8_description: "Een comfortabele elektrische sloep voor families en vriendengroepen.",
      boat_electrosloop_8_features: "8 personen capaciteit, Elektrische motor, Comfortabel en stabiel, Stil en milieuvriendelijk, Perfect voor families, Huisdieren welkom",

      boat_electroboat_5_name: "Electrosloep voor 5 pers",
      boat_electroboat_5_description: "Een compacte elektrische sloep voor kleine groepen. Ideaal voor rustige vaartochten.",
      boat_electroboat_5_features: "5 personen capaciteit, Elektrische motor, Compact en wendbaar, Stil en milieuvriendelijk, Perfect voor kleine groepen, Huisdieren toegestaan",

      boat_sailboat_name: "Zeilboot",
      boat_sailboat_description: "Een traditionele zeilboot beschikbaar met of zonder motor. Zonder motor voor ervaren zeilers, met motor voor meer flexibiliteit.",
      boat_sailboat_features: "4-5 personen capaciteit, Zeilen zonder motor: €70, Zeilen met motor: €85, Flexibel, Voor alle niveaus, Ervaren zeiler vereist, Huisdieren niet toegestaan",

      boat_sailpunter_name: "Zeilpunter",
      boat_sailpunter_description: "Een traditionele zeilpunter voor de ervaren zeiler. Geniet van wind en natuur.",
      boat_sailpunter_features: "3-4 personen capaciteit, Zeilen, Traditioneel, Sportief, Voor ervaren zeilers, Ervaren zeiler vereist, Huisdieren toegestaan",

      boat_canoe_name: "Canadese kano",
      boat_canoe_description: "Een stabiele Canadese kano voor sportieve activiteiten en het verkennen van kleinere waterwegen.",
      boat_canoe_features: "3 personen capaciteit, Paddelen, Sportief, Stabiel, Voor alle niveaus, Huisdieren toegestaan",

      boat_kayak_2_name: "Kajak 2 pers",
      boat_kayak_2_description: "Een tandem kajak voor twee personen. Perfect voor sportieve activiteiten.",
      boat_kayak_2_features: "2 personen capaciteit, Paddelen, Sportief, Wendbaar, Voor alle niveaus, Huisdieren niet toegestaan",

      boat_kayak_1_name: "Kajak 1 pers",
      boat_kayak_1_description: "Een solo kajak voor individuele vaartochten. Ideaal voor sportieve activiteiten.",
      boat_kayak_1_features: "1 persoon capaciteit, Paddelen, Sportief, Wendbaar, Voor alle niveaus, Huisdieren niet toegestaan",

      boat_sup_name: "SUP board",
      boat_sup_description: "Een stand-up paddleboard voor een unieke manier om het water te ervaren.",
      boat_sup_features: "1 persoon capaciteit, Paddelen, Uniek, Balanceren, Voor alle niveaus, Huisdieren niet toegestaan",

      hero_book_btn: "Boek nu",
      btn_outline: "📞 Bel direct!",
      btn_add_to_cart: "🛒 Toevoegen aan winkelwagen",
      hero_book_badge: "100% veilig &amp; vrijblijvend",
      hero_h1: "Botenverhuur Wanneperveen & Weerribben | Nijenhuis",
      hero_h1_p: "Ervaar de schoonheid van het natuurgebied Weerribben met onze premium botenverhuur. Verhuur boot, sloep huren Overijssel – perfect voor families, vrienden en natuurliefhebbers.",
      hero_btn: "Beschikbaarheid controleren",
      intro_h2: "Ontsnap aan de dagelijkse sleur met dé botenverhuur van de Weerribben",
      intro_h2_p: "In onze drukke wereld snak iedereen naar rust. Laat files, stress en dagelijkse routine achter je – ontdek Nationaal Park Weerribben-Wieden vanaf het water bij Nijenhuis Botenverhuur in Wanneperveen, dé bootverhuur van de Weerribben.",
      intro_h2_p2: "Verhuur boot voor quality time met familie of vrienden. Onze fluisterboten en electrosloepen glijden stil door smalste slootjes, weg van de massa. Sloep huren Overijssel – creëer onvergetelijke momenten, perfect om even helemaal weg te zijn.",
      deposit_notice_cash: "<strong>Let op:</strong> De borg dient contant te worden betaald bij aankomst.",
      intro_h3: "Waarom kiezen voor Nijenhuis?",
      intro_h3_li1: "📍 Gelegen in het hart van het natuurgebied Weerribben",
      intro_h3_li2: "🚤 Breed assortiment boten voor alle voorkeuren",
      intro_h3_li3: "🌿 Milieuvriendelijke elektrische boten beschikbaar",
      intro_h3_li4: "👨‍👩‍👧‍👦 Perfect voor families en groepen",
      intro_h3_li5: "💰 Concurrentiële prijzen voor alle budgetten",
      intro_h3_li6: "📞 Persoonlijke service en ondersteuning",
      intro_cta_p: "Voor meer informatie, bel 0522 - 281 528",
      intro_cta_p2: "Contant en pin betalingen geaccepteerd",

      /* About Grid */
      about_location_title: "Locatie",
      about_location_desc: "Wanneperveen, Overijssel<br><span class='fact-sub'>10 km van Giethoorn</span>",
      about_season_title: "Seizoen",
      about_season_desc: "1 april – 31 oktober<br><span class='fact-sub'>Dagelijks 09:00-18:00</span>",
      about_fleet_title: "Boten",
      about_fleet_desc: "25+ vaartuigen<br><span class='fact-sub'>1 tot 12 personen</span>",
      about_prices_title: "Prijzen",
      about_prices_desc: "Vanaf €20/dag<br><span class='fact-sub'>Geen vaarbewijs nodig – sloep huren Overijssel</span>",

      index_season_title: "Seizoenscamping",
      index_season_dates: "Open van 1 april tot 31 oktober",
      index_season_status: "Nu open voor reserveringen",

      index_camping_title: "Seizoenscamping in de Weerribben",
      index_camping_description: "Geniet van een unieke kampeerervaring midden in het prachtige natuurgebied Weerribben. Onze seizoenscamping is open van 1 april tot 31 oktober en biedt een rustige, gezellige omgeving voor je caravan.",
      index_camping_feature_1_title: "Seizoenscamping",
      index_camping_feature_1_desc: "Open van 1 april tot 31 oktober",
      index_camping_feature_2_title: "Caravans het hele jaar",
      index_camping_feature_2_desc: "Caravans kunnen het hele jaar blijven staan",
      index_camping_feature_3_title: "Moderne faciliteiten",
      index_camping_feature_3_desc: "Water, elektriciteit, sanitair en eigen aanlegplaats",
      index_camping_feature_4_title: "Botenverhuur seizoen",
      index_camping_feature_4_desc: "Botenverhuur alleen tijdens het seizoen beschikbaar",
      index_camping_cta_text: "Voor meer informatie over onze seizoenscamping",
      services_h2: "Onze diensten",
      services_h3_1: "Botenverhuur",
      services_p_1: "Botenverhuur Weerribben: elektrische boten, kano's, kajaks en SUP boards. Verhuur boot voor alle leeftijden en ervaringsniveaus.",
      services_btn_1: "Bekijk botenverhuur",
      services_h3_2: "Vakantiehuis",
      services_p_2: "Comfortabele vakantie accommodatie perfect voor families en groepen.",
      services_btn_2: "Bekijk vakantiehuis",
      services_h3_3: "Camping",
      services_p_3: "Seizoenscamping van 1 april tot 31 oktober. Prachtige kampeerplaatsen in het natuurgebied met moderne faciliteiten en adembenemende uitzichten.",
      services_btn_3: "Bekijk camping",
      map_h2: "Vind ons",
      footer_p: "Hier begint je avontuur in de prachtige Weerribben!",
      footer_company_name: "Nijenhuis Botenverhuur",
      footer_company_location: "Camping",
      footer_company_address: "Veneweg 199",
      footer_company_postal: "7946 LP Wanneperveen",
      footer_company_phone: "Tel: 0522 281 528",
      footer_company_kvk: "Kvk: 6769 7097",
      footer_company_btw: "Btw nr: NL857 1361 48 B01",
      footer_li1: "Botenverhuur",
      footer_li2: "Vakantiehuis",
      footer_li3: "Te koop",
      footer_li4: "Camping",
      footer_li6: "Vaarkaart",
      footer_li7: "Contact",
      footer_bottom: "&copy; 2026 <a href=\"https://ultimAItech.com\" target=\"_blank\" rel=\"noopener noreferrer\">ultimAItech</a>. Alle rechten voorbehouden.",
      footer_rights: "&copy; 2026 <a href=\"https://ultimAItech.com\" target=\"_blank\" rel=\"noopener noreferrer\">ultimAItech</a>. Alle rechten voorbehouden.",
      /* image alt text (SEO) */
      alt_electrosloop: "Electrosloep huren Giethoorn Weerribben",
      alt_zeilpunter: "Zeilpunter huren Weerribben",
      alt_kano: "Kano huren Weerribben Overijssel",
      alt_sup: "SUP huren Giethoorn",
      alt_kajak: "Kajak huren Weerribben",
      alt_camping_banner: "Seizoenscamping Nijenhuis aan het water in Nationaal Park Weerribben-Wieden bij Giethoorn",
      alt_house_interior: "Vakantiehuis Belterwiede interieur - vakantiewoning bij Giethoorn",
      alt_logo: "Nijenhuis Botenverhuur",
      /* boats page */
      boats_header_h1: "Boot en sloep huren in de Weerribben bij Giethoorn | Botenverhuur",
      boats_header_p: "Stap aan boord en ontdek het mooie natuurgebied de Weerribben met onze boten, kano’s en kajaks!",
      boats_h2: "Onze boten",
      boats_intro: "Wij bieden een breed assortiment boten voor alle voorkeuren en ervaringsniveaus",
      fleet_h2: "Onze boten",
      fleet_p: "Kies uit ons ruime aanbod van elektrische sloepen, zeilboten en kano's",
      fleet_hourly_note: "ℹ️ Let op: Voor alle boten is het ook mogelijk om per uur te huren in plaats van per dag. Uurverhuur kan alleen direct ter plaatse bij de bootverhuur worden geboekt, niet online of telefonisch. Kom langs bij onze verhuurlocatie voor beschikbaarheid en directe boeking.",
      boats_cat_electric: "Elektrische boten",
      boats_cat_electric_desc: "Milieuvriendelijke boten met elektrische aandrijving, perfect voor rustige vaartochten door het natuurgebied.",
      boats_cat_gasoline: "Benzine boten",
      boats_cat_gasoline_desc: "Krachtige boten met benzinemotor, ideaal voor grotere groepen en langere vaartochten.",
      boats_cat_sailing: "Zeilboten",
      boats_cat_sailing_desc: "Traditionele zeilboten voor de ervaren zeiler. Geniet van de wind en de natuur.",
      boats_cat_canoe: "Kano's & kajaks",
      boats_cat_canoe_desc: "Perfect voor sportieve activiteiten en het verkennen van kleinere waterwegen.",
      boats_cat_sup: "Sup boards",
      boats_cat_sup_desc: "Stand-up paddleboards voor een unieke manier om het water te ervaren.",
      boats_cat_all: "Alle boten",
      boats_cat_all_desc: "Bekijk alle beschikbare boten en hun details.",
      prices_h2: "Prijzen & beschikbaarheid",
      prices_intro: "Alle prijzen zijn per dag inclusief veiligheidsuitrusting en instructies",
      prices_intro_2: "De Borg is afhankelijk van het type boot en moet contant worden betaald.",
      prices_table_title: "Dagprijzen botenverhuur",
      prices_season: "Seizoen: 1 april - 31 oktober 2026",
      prices_th_type: "Boot type",
      prices_th_capacity: "Capaciteit",
      prices_th_price: "Prijs per dag",
      prices_th_deposit: "Borg",
      multi_day_note: "Boten kunnen ook voor meerdere dagen worden gehuurd. Neem contact op voor meer informatie en tarieven.",
      boats_cta_h2: "Boek nu jouw boot",
      boats_cta_p: "Klaar om de Weerribben te ontdekken? Reserveer vandaag nog je boot en geniet van een onvergetelijke dag op het water bij Giethoorn.",
      boats_cta_btn: "Nu boeken",
      boats_cta_phone: "📞 Bel ons",
      btn_call: "Nu bellen",
      rentinfo_h2: "Huurinformatie",
      rentinfo_intro: "Alles wat je moet weten over het huren van een boot",
      rentinfo_book_title: "📅 Reserveringen",
      rentinfo_book_desc: "Reserveringen kunnen telefonisch of online worden gemaakt. Wij raden aan om vooraf te reserveren, vooral in het hoogseizoen.",
      rentinfo_open_title: "⏰ Openingstijden",
      rentinfo_open_desc: "Dagelijks geopend van 09:00 tot 18:00 uur tijdens het seizoen (1 april - 31 oktober).",
      rentinfo_pay_title: "💰 Betaling",
      rentinfo_pay_desc: "Contante betaling en pinbetaling worden geaccepteerd. Een borg van €50-€100 is vereist afhankelijk van het boottype. Zie de prijzentabel voor specifieke borgbedragen.",
      /* booking page */
      booking_title: "Boek je boot - reservering bij Nijenhuis Botenverhuur",
      booking_subtitle: "Je boot is beschikbaar! Vul je gegevens in om de reservering te bevestigen.",
      booking_details_title: "Reserveringsgegevens",
      booking_date_label: "Selecteer datum *",
      booking_days_label: "Aantal dagen *",
      booking_select_duration: "-- Selecteer duur --",
      booking_end_date_label: "Eind datum *",
      booking_1_day: "1 dag",
      booking_2_days: "2 dagen",
      booking_3_days: "3 dagen",
      booking_4_days: "4 dagen",
      booking_5_days: "5 dagen",
      booking_6_days: "6 dagen",
      booking_7_days: "7 dagen",
      booking_boat_label: "Selecteer boot *",
      booking_quantity_label: "Aantal boten *",
      booking_select_boat: "-- Selecteer een boot --",
      booking_total_price: "Totale prijs:",
      booking_summary_title: "Reserveringsoverzicht",
      booking_summary_date: "Datum:",
      booking_summary_boat: "Boot type:",
      booking_summary_duration: "Duur:",
      booking_summary_price: "Totale prijs:",
      booking_summary_status: "Status:",
      booking_status_select: "Selecteer bovenstaande opties",
      booking_status_available: "Beschikbaar ✓",
      booking_your_info: "Jouw gegevens",
      booking_name_label: "Volledige naam *",
      booking_email_label: "E-mailadres *",
      booking_phone_label: "Telefoonnummer *",
      booking_address_label: "Adres (optioneel)",
      booking_notes_label: "Speciale verzoeken of opmerkingen",
      booking_notes_placeholder: "Eventuele speciale wensen...",
      booking_confirm_btn: "Reservering bevestigen",
      booking_back_btn: "Terug naar home",
      booking_processing: "Je reservering wordt verwerkt...",
      /* house page */
      house_header_h1: "Vakantiehuis Belterwiede nabij Giethoorn en Weerribben",
      house_header_p1: "Ervaar een heerlijk verblijf in ons vakantiehuis, midden in het prachtige natuurgebied de Weerribben.",
      house_overview_h2: "Vakantiehuis Belterwiede bij Giethoorn",
      house_overview_p1: "<strong>HET HELE JAAR GEOPEND</strong> – Ons vakantiehuis Belterwiede ligt nabij Giethoorn in het hart van de Weerribben.",
      house_overview_h3: "Perfecte uitvalsbasis in de kop van Overijssel",
      house_overview_p2: "Wil je een weekend, midweek, week of een hele vakantie doorbrengen in een prachtig natuur- of watersportgebied? Kom dan naar de Kop van Overijssel, waar je kunt genieten van varen, vissen, zwemmen, fietsen, wandelen en het bezoeken van andere dorpen in de omgeving. Het huis is direct gelegen aan het Belterwijde meer.",
      house_overview_p3: "<strong>Beneden:</strong> <span>Je hebt 1 slaapkamer, een douche, toilet en wasmachine. Je kunt ontspannen in de ruime woonkamer met TV en radio. De kamer heeft een open keuken met diverse huishoudelijke apparaten (oven, magnetron, koelkast). Er is een ruime hal en het huis is volledig voorzien van centrale verwarming.</span>",
      house_overview_p4: "<strong>Boven:</strong> <span>Je hebt vier slaapkamers, waarvan er twee een wastafel hebben. Er is ook een douche en toilet op de tweede verdieping.</span>",
      house_overview_li1: "Kinderbedje, box en kinderstoel kunnen aangevraagd worden in het Waterpark Belterwijde.",
      house_overview_li2: "Kussens en dekbedden beschikbaar",
      house_overview_li3: "Linnengoed graag zelf meenemen",
      house_overview_li4: "Linnengoed ook te huur bij ons (graag vooraf melden)",
      house_overview_li5: "Voor verdere vragen kun je contact met ons opnemen",
      house_overview_h4: "Wat bieden wij?",
      house_amenities_h1: "Faciliteiten",
      house_amenities_p1: "Alles voor een comfortabel verblijf",
      house_amenities_h2: "5 Slaapkamers",
      house_amenities_p2: "1 slaapkamer beneden, 4 slaapkamers boven (2 met wastafel)",
      house_amenities_h3: "Open keuken",
      house_amenities_p3: "Oven, magnetron, koelkast en alle huishoudelijke apparaten",
      house_amenities_h4: "2 Badkamers",
      house_amenities_p4: "Douche en toilet op beide verdiepingen",
      house_amenities_h5: "Woonkamer",
      house_amenities_p5: "Ruime woonkamer met TV en radio",
      house_amenities_h6: "Wasmachine",
      house_amenities_p6: "Wasmachine beschikbaar in het huis",
      house_amenities_h7: "Centrale verwarming",
      house_amenities_p7: "Volledig verwarmd voor comfort het hele jaar door",
      house_why_title: "Waarom kiezen voor dit vakantiehuis?",
      house_why_p1: "Het vakantiehuis Belterwiede biedt het beste van twee werelden: de rust van het Nationaal Park Weerribben-Wieden en de levendigheid van Giethoorn om de hoek. Omdat het huis direct aan het Belterwijde meer ligt, stap je letterlijk vanuit de tuin je boot of kano in. Ideaal voor gezinnen die willen varen, vissen of zwemmen zonder steeds te moeten in- en uitladen.",
      house_why_p2: "Het huis is het hele jaar geopend, waardoor je ook in de herfst en winter kunt genieten van wandelingen, fietstochten en de unieke sfeer van de Weerribben. In de zomer is het een perfecte uitvalsbasis voor dagtrips naar Giethoorn, Belt-Schutsloot of andere dorpen in de omgeving. Waterpark Belterwiede verzorgt de reserveringen en het beheer van het vakantiehuis.",
      house_surroundings_title: "Omgeving & activiteiten",
      house_surroundings_p1: "Vanuit het vakantiehuis Belterwiede heb je direct toegang tot het uitgestrekte waternetwerk van de Weerribben. Varen, kanoën of vissen – het kan allemaal vanaf je eigen aanlegsteiger. Fiets- en wandelroutes lopen door het gebied en verbinden je met pittoreske dorpen als Giethoorn, Wanneperveen en Blokzijl.",
      house_surroundings_p2: "In de omgeving vind je restaurants, winkels en attracties. Giethoorn ligt op ongeveer 15 minuten rijden en staat bekend om zijn grachten en rietgedekte huizen. Voor gezinnen zijn er speeltuinen en strandjes aan het water. Het vakantiehuis is geschikt voor maximaal twaalf personen en biedt voldoende ruimte voor een ontspannen verblijf.",
      house_contact_h2: "Contact & Reserveringen",
      house_contact_p1: "Voor meer informatie en reserveringen",
      house_contact_h3: "Waterpark Belterwiede",
      house_contact_p2: "E-mail: info@parkbelterwiede.nl",
      house_contact_p3: "Telefoon: 0522-281828",
      /* te-koop page */
      te_koop_h1: "Chalets en stacaravans te koop in de Weerribben",
      te_koop_p1: "Bekijk hier onze nieuwste aanbiedingen.",
      te_koop_h2: "Chalets & stacaravans",
      te_koop_h3: "Geen aanbod beschikbaar",
      te_koop_p2: "Op dit moment hebben wij geen chalets of stacaravans te koop. Zodra er nieuw aanbod is, vind je dat hier terug.",
      te_koop_p3: "Heb je interesse in een chalet of stacaravan in de toekomst? Neem gerust contact met ons op voor meer informatie of om op de wachtlijst te komen.",
      te_koop_h4: "Interesse in een chalet of stacaravan?",
      te_koop_p4: "Neem contact met ons op:",
      te_koop_p5: "📞 <strong>Telefoon</strong>: 0522 281 528",
      te_koop_p6: "📍 <strong>Adres</strong>: Veneweg 199, 7946 LP Wanneperveen",
      te_koop_p7: "⏰ <strong>Openingstijden</strong>: Dagelijks 09:00 - 18:00 uur",
      te_koop_intro_h2: "Chalets en stacaravans in de Weerribben",
      te_koop_intro_p1: "Bij Camping Nijenhuis komen regelmatig chalets en stacaravans vrij die te koop staan op een vaste staanplaats in Nationaal Park Weerribben-Wieden. Een eigen chalet of stacaravan op onze camping betekent een vaste plek aan het water, direct toegang tot de vaarroutes naar Giethoorn en de Weerribben, en een rustige omgeving waar je het hele jaar kunt genieten van de natuur.",
      te_koop_intro_p2: "Kopers krijgen een seizoensplaats met alle voorzieningen: water, elektriciteit, riool en een eigen aanlegplaats. De caravans en chalets mogen het hele jaar op de plaats blijven staan. Door de kleinschaligheid van de camping is het aanbod beperkt – nieuw aanbod wordt op deze pagina geplaatst zodra het beschikbaar is.",
      te_koop_why_h2: "Waarom bij Nijenhuis kopen?",
      te_koop_why_p1: "Camping Nijenhuis is een familiebedrijf met meer dan 50 jaar ervaring in de Weerribben. Onze camping biedt een unieke locatie direct aan het water, met eigen aanlegplaatsen en alle moderne voorzieningen. Chalets en stacaravans die hier te koop staan, hebben een bewezen staanplaats in een gewild natuurgebied. Geïnteresseerd? Neem contact op voor beschikbaarheid, prijzen en de mogelijkheid om op de wachtlijst te komen voor toekomstig aanbod.",

      /* camping page */
      camping_title: "Seizoenscamping in de Weerribben bij Giethoorn",
      camping_description: "Kom helemaal tot rust tijdens het kamperen midden in het prachtige natuurgebied de Weerribben.",

      camping_season_title: "Seizoenscamping",
      camping_season_dates: "Open van 1 april tot 31 oktober",
      camping_season_status: "Nu open voor reserveringen",

      camping_overview_title: "Onze camping",
      camping_overview_description: "Een rustige en sfeervolle camping midden in de natuur",

      camping_overview_seasonal_title: "Al meer dan 50 jaar een familiebegrip",
      camping_overview_seasonal_description: "Al ruim een halve eeuw is Camping Nijenhuis een verborgen parel in het hart van de Weerribben. Wat meer dan vijftig jaar geleden begon als een passie voor gastvrijheid en natuur, is uitgegroeid tot een unieke familiecamping waar generaties gasten zich thuis voelen. Nog steeds in familiehanden koesteren wij de persoonlijke sfeer en de rust die onze camping zo bijzonder maken. <br><br> Onze seizoenscamping is kleinschalig opgezet, waardoor je geniet van maximale privacy en ruimte. Het is de perfecte plek om te ontsnappen aan de dagelijkse drukte. Uniek aan onze camping is dat elke staanplaats beschikt over een eigen aanlegplaats, zodat je direct vanaf je caravan het water op kunt om de prachtige waterwegen van Giethoorn en de Weerribben te verkennen.",
      camping_overview_seasonal_list_item_1: "Seizoenscamping (1 april - 31 oktober)",
      camping_overview_seasonal_list_item_2: "Caravans kunnen het hele jaar blijven staan",
      camping_overview_seasonal_list_item_3: "Wateraansluiting",
      camping_overview_seasonal_list_item_4: "Elektriciteitsmeter",
      camping_overview_seasonal_list_item_5: "Centrale antenne",
      camping_overview_seasonal_list_item_6: "Rioolafvoer",
      camping_overview_seasonal_list_item_7: "Eigen aanlegplaats",
      camping_overview_seasonal_list_item_8: "Douches en toiletten beschikbaar",
      camping_overview_seasonal_list_item_9: "Kleine maar gezellige camping",
      camping_overview_seasonal_list_item_10: "Wasmachine en droger beschikbaar",

      camping_overview_cta_strong: "Interesse in een seizoensplaats?",
      camping_overview_cta_button: "Bel nu",
      camping_area_title: "Omgeving & recreatie",
      camping_area_p1: "Camping Nijenhuis ligt midden in Nationaal Park Weerribben-Wieden, een van de mooiste natuurgebieden van Nederland. Vanaf je staanplaats vaar je direct het water op – geen gedoe met trailers of slepen. De waterwegen verbinden je met Giethoorn, Wanneperveen, Belt-Schutsloot en talloze rustige plekjes waar je alleen de vogels hoort.",
      camping_area_p2: "Naast varen kun je fietsen, wandelen, vissen en zwemmen. Er zijn uitgestippelde routes voor elke afstand. In de omgeving vind je restaurants, musea en bootverhuur. Veel gasten combineren hun verblijf met een boot of kano van Nijenhuis Botenverhuur – vraag naar de mogelijkheden bij reservering.",
      camping_tips_title: "Praktische informatie seizoenscamping",
      camping_tips_p1: "De camping is open van 1 april tot 31 oktober. Caravans mogen het hele jaar op de plaats blijven staan, waardoor je in het voor- en naseizoen zonder stress kunt komen en gaan. Elke staanplaats heeft water, elektriciteit (met eigen meter), rioolaansluiting en een eigen aanlegplaats. Sanitair met douches en toiletten is aanwezig, evenals een wasmachine en droger.",
      camping_tips_p2: "Vanwege de kleinschaligheid en de gewilde locatie raden we aan tijdig te reserveren. Bel ons voor beschikbaarheid en prijzen. Honden zijn welkom, mits aangelijnd op de camping. De sfeer is rustig en geschikt voor gezinnen en natuurliefhebbers die genieten van eenvoud en direct contact met het water.",

      facilities_title: "Faciliteiten",
      facilities_description: "Alle voorzieningen voor seizoensplaatsen",

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

      /* vaarkaart page */
      vaarkaart_title: "Vaarkaart Weerribben-Wieden - routes en vaarinformatie",
      vaarkaart_description: "Navigatie informatie en routes voor het natuurgebied Weerribben",

      vaarkaart_interactive_map_title: "Interactieve vaarkaart Weerribben-Wieden",
      vaarkaart_interactive_map_description: "Ontdek de mooiste routes door Nationaal Park Weerribben-Wieden. Deze vaarkaart toont alle vaarroutes in het gebied.",
      vaarkaart_intro_extra: "Nationaal Park Weerribben-Wieden is het grootste laagveenmoeras van Noordwest-Europa. Het watergebied bestaat uit meren, sloten en vaarten die vroeger door turfwinning ontstonden. Tegenwoordig is het een paradijs voor booters, met rustige routes, rietkragen, moerassen en weidse uitzichten. Vanuit Nijenhuis Botenverhuur in Wanneperveen vaar je direct het netwerk in. Hieronder vind je de interactieve kaart, populaire routes en belangrijke vaarregels.",
      vaarkaart_route_giethoorn_desc: "De route voert door smalle sloten en bredere vaarten naar het centrum van Giethoorn. Onderweg zie je rietgedekte boerderijen, bruggetjes en typische punters. In Giethoorn kun je aanleggen om te wandelen of te lunchen. Plan minstens 2–3 uur voor een ontspannen heen-en-terug tocht.",
      vaarkaart_route_weerribben_desc: "Deze route voert dieper het park in, langs moerassen, rietvelden en open water. Je kunt ijsvogels, reigers, libellen en diverse watervogels spotten. Neem een picknick mee en zoek een rustig plekje aan de oever. Een electrosloep of kano is ideaal voor deze route.",
      vaarkaart_route_wanneperveen_desc: "Een ideale route voor een eerste kennismaking met het gebied of als je weinig tijd hebt. Je vaart rond Wanneperveen en geniet van het dorpsgezicht en de omliggende wateren. Geschikt voor alle boottypes, inclusief kajaks en kano's.",
      vaarkaart_interactive_map_map_title: "Weerribben natuurgebied - interactieve vaarkaart",
      vaarkaart_interactive_map_attribution_source: "Bron:",
      vaarkaart_interactive_map_attribution_source_link: "Waterkaart.net",
      vaarkaart_interactive_map_attribution_suffix: "– Professionele vaarkaarten voor Nederlandse wateren",
      vaarkaart_interactive_map_placeholder_title: "Interactieve vaarkaart",
      vaarkaart_interactive_map_placeholder_description: "Voor de meest actuele en gedetailleerde vaarkaarten van het Weerribben gebied, bezoek de professionele waterkaart van Nederland.",
      vaarkaart_interactive_map_placeholder_button: "Open waterkaart.net",
      vaarkaart_interactive_map_placeholder_button: "Open waterkaart.net",
      vaarkaart_interactive_map_footer_description: "Deze interactieve vaarkaart wordt verzorgd door Waterkaart.net. Voor de meest actuele informatie en gedetailleerde vaarkaarten, bezoek hun website.",
      vaarkaart_expand_map: "Vergroot kaart",
      vaarkaart_close_fullscreen: "Sluiten",
      vaarkaart_view_osm: "OpenStreetMap",
      vaarkaart_disclaimer_title: "Disclaimer:",
      vaarkaart_disclaimer_text: "Wij nemen geen verantwoordelijkheid voor de inhoud en juistheid van deze kaart. Lokale wetten, regels en borden langs het water dienen altijd als eerste te worden gevolgd.",
      vaarkaart_footer_source: "Voor gedetailleerde vaarkaarten en actuele vaarinformatie, bezoek <a href='https://waterkaart.net/' target='_blank' rel='noopener noreferrer'>Waterkaart.net</a>.",

      /* Giethoorn, Belt-schutsloot & Wanneperveen page H1s */
      giethoorn_title: "Giethoorn bezoeken - boot huren in het Venetië van het Noorden",
      belt_schutsloot_title: "Belt-schutsloot - verborgen parel nabij Giethoorn en Weerribben",
      wanneperveen_title: "Wanneperveen - rustig varen in de Weerribben",
      wanneperveen_description: "Ontdek de mooiste vaarwegen van de Weerribben vanuit Wanneperveen",

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

      vaarkaart_navigation_rules_title: "Vaarregels & veiligheid",
      vaarkaart_navigation_rules_description: "Belangrijke informatie voor veilig varen",

      vaarkaart_navigation_rules_general_rules_title: "Algemene regels",
      vaarkaart_navigation_rules_general_rules_max_speed: "Maximum snelheid: 6 km/u",
      vaarkaart_navigation_rules_general_rules_lifejackets: "Zwemvesten verplicht",
      vaarkaart_navigation_rules_general_rules_alcohol: "Geen alcohol tijdens het varen",
      vaarkaart_navigation_rules_general_rules_respect_nature: "Respektiere die Natur",
      vaarkaart_navigation_rules_general_rules_distance_from_other_boats: "Halte Abstand zu anderen Booten",

      /* New Vaarkaart Labels */
      vaarkaart_label_start: "Start",
      vaarkaart_label_duration: "Dauer",
      vaarkaart_label_distance: "Distanz",
      vaarkaart_label_difficulty: "Schwierigkeitsgrad",
      vaarkaart_label_highlights: "Highlights",
      vaarkaart_difficulty_easy: "Einfach",
      vaarkaart_difficulty_medium: "Mittel",
      vaarkaart_highlight_giethoorn: "Dorfansicht Giethoorn",
      vaarkaart_highlight_biotope: "Wilde Tiere, Vögel",
      vaarkaart_highlight_wanneperveen: "Dorfansicht Wanneperveen",
      unit_hours: "Std",
      unit_km: "km",

      vaarkaart_navigation_rules_safety_tips_title: "Veiligheidstips",
      vaarkaart_navigation_rules_safety_tips_check_weather: "Controleer het weer voor vertrek",
      vaarkaart_navigation_rules_safety_tips_bring_water: "Neem voldoende water mee",
      vaarkaart_navigation_rules_safety_tips_charge_phone: "Zorg voor een opgeladen telefoon",
      vaarkaart_navigation_rules_safety_tips_know_rules: "Ken de vaarregels",
      vaarkaart_navigation_rules_safety_tips_stay_on_navigable_routes: "Blijf op de bevaarbare routes",

      /* New Vaarkaart Labels */
      vaarkaart_label_start: "Start",
      vaarkaart_label_duration: "Duur",
      vaarkaart_label_distance: "Afstand",
      vaarkaart_label_difficulty: "Moeilijkheidsgraad",
      vaarkaart_label_highlights: "Hoogtepunten",
      vaarkaart_difficulty_easy: "Gemakkelijk",
      vaarkaart_difficulty_medium: "Gemiddeld",
      vaarkaart_highlight_giethoorn: "Dorpsgezicht Giethoorn",
      vaarkaart_highlight_biotope: "Wilde dieren, vogels",
      vaarkaart_highlight_wanneperveen: "Dorpsgezicht Wanneperveen",
      unit_hours: "uur",
      unit_km: "km",

      vaarkaart_navigation_rules_emergency_numbers_title: "Noodnummers",
      vaarkaart_navigation_rules_emergency_numbers_general_alarm: "Algemeen alarmnummer: 112",
      vaarkaart_navigation_rules_emergency_numbers_nijenhuis: "Nijenhuis Botenverhuur: 0522 281 528",
      vaarkaart_navigation_rules_emergency_numbers_water_police: "Waterpolitie: 0900-8844",
      vaarkaart_navigation_rules_emergency_numbers_weather_report: "Weerbericht: 0900-9722",
      vaarkaart_navigation_rules_emergency_numbers_rescue_brigade: "Reddingsbrigade: 0900-0112",

      /* contact page */
      contact_title: "Contact en routebeschrijving - Nijenhuis Wanneperveen",
      contact_p: "Neem contact met ons op voor vragen, reserveringen of meer informatie",

      contact_h2: "Contact & Route",
      contact_h2_p: "Neem contact op met Nijenhuis Botenverhuur in Wanneperveen. Bekijk hier onze contactgegevens en routebeschrijving.",
      contact_intro_extra: "Nijenhuis Botenverhuur ligt aan de Veneweg 199 in Wanneperveen, aan de rand van Nationaal Park Weerribben-Wieden. Wij zijn gespecialiseerd in bootverhuur – van electrosloepen en zeilboten tot kano's en SUP-boards – en bieden daarnaast seizoenscamping. Voor reserveringen, vragen over prijzen of beschikbaarheid kun je ons bellen of langskomen tijdens openingstijden. Er is gratis parkeergelegenheid bij onze locatie.",
      contact_route_h2: "Routebeschrijving",
      contact_route_p1: "Wanneperveen ligt in de Kop van Overijssel, tussen Meppel en Steenwijk. Kom je met de auto? Volg de borden naar Wanneperveen en zoek de Veneweg – wij zitten op nummer 199, direct aan het water. Vanuit Giethoorn is het circa 15 minuten rijden. Er is gratis parkeergelegenheid bij onze locatie. Openbaar vervoer: buslijn 77 stopt in de buurt van Wanneperveen; voor de exacte haltes raadpleeg de dienstregeling.",
      contact_route_p2: "Tijdens het seizoen (1 april – 31 oktober) zijn wij dagelijks geopend van 09:00 tot 18:00 uur. Voor boten en kano's raden we aan vooraf te reserveren, vooral in het weekend en in de zomermaanden. Bij aankomst kun je direct bij ons terecht voor de sleutel, instructie en routekaart.",

      contact_h3: "Contact informatie",

      contact_address_title: "Adres",
      contact_address: "Veneweg 199",
      contact_zip: "7946 LP Wanneperveen",
      contact_country: "Nederland",

      contact_phone_title: "Telefoon",
      contact_phone: "0522 281 528",

      contact_opening_title: "Openingstijden",
      contact_opening_p: "Dagelijks: 09:00 - 18:00 uur",
      contact_season_p: "Seizoen: 1 april - 31 oktober",

      contact_business_title: "Bedrijfsgegevens",
      contact_kvk: "Kvk: 6769 7097",
      contact_btw: "Btw nr: NL857 1361 48 B01",

      contact_call_title: "Direct contact",
      contact_call_p: "Voor vragen, reserveringen of meer informatie, bel ons direct:",
      contact_call_button: "Bel nu",
      contact_call_info_p: "Beschikbaar: Dagelijks van 09:00 - 18:00 uur",
      contact_call_info_p2: "Seizoen: 1 april - 31 oktober",

      contact_map_title: "Waar vind je ons?",
      contact_map_p: "Bekijk onze locatie op de kaart",
      /* payment pages */
      payment_success_title: "Betaling geslaagd!",
      payment_success_title_pay_on_arrival: "Reservering bevestigd!",
      payment_success_subtitle_pay_on_arrival:
        "Je reservering staat vast. We hebben je niet-restitueerbare reserveringsbijdrage ontvangen. Het resterende bedrag betaal je bij aankomst (zie overzicht). Je ontvangt binnenkort een bevestigingsmail.",
      payment_success_subtitle: "Je bootreservering is bevestigd. Je ontvangt binnenkort een bevestigingsmail.",
      payment_success_processing: "Je betaling wordt verwerkt...",
      payment_success_back: "Terug naar home",
      payment_success_contact: "Contact",
      payment_success_booking_id: "Reserverings-ID:",
      payment_success_date: "Datum:",
      payment_success_duration: "Duur:",
      payment_success_boat_type: "Boot type:",
      payment_success_customer: "Klant:",
      payment_success_status: "Status:",
      payment_success_price: "Prijs:",
      payment_success_breakdown_rental: "Huur",
      payment_success_breakdown_fee: "Administratiekosten",
      payment_success_breakdown_total: "Totaal betaald",
      payment_success_breakdown_total_due: "Totaal (bij aankomst)",
      payment_success_breakdown_reservation_fee: "Reserveringsbijdrage (betaald, niet-restitueerbaar)",
      payment_success_breakdown_reservation_rental_portion: "Daarvan: huurdeel reservering",
      payment_success_breakdown_reservation_admin_slice: "Daarvan: administratie op dat deel",
      payment_success_breakdown_balance_arrival: "Nog te betalen bij aankomst",
      payment_success_reference_label: "Reserveringsnummer:",
      payment_success_arrival_title: "Aankomst",
      payment_success_arrival_location_label: "Locatie",
      payment_success_arrival_time_label: "Aankomsttijd",
      payment_success_arrival_bring_label: "Meenemen",
      payment_success_arrival_bring_text:
        "Zonbescherming en comfortabele kleding. Borg betaal je contant bij aankomst volgens je reservering.",
      payment_success_price_via_mollie: "(via Mollie)",
      payment_success_price_pay_on_arrival: "(betaling bij aankomst)",
      payment_success_download_pdf: "Download als PDF",
      payment_success_pdf_heading: "Reserveringsbewijs - Nijenhuis Botenverhuur",
      payment_success_pdf_date_generated: "Aangemaakt op:",
      payment_success_pdf_unavailable: "PDF-download is niet beschikbaar. Vernieuw de pagina of neem contact op.",
      payment_success_pdf_deposit_heading: "Borg bij aankomst",
      payment_success_pdf_wordmark: "NIJENHUIS",
      payment_success_pdf_wordmark_sub: "Botenverhuur",
      payment_success_pdf_hero_date_label: "Datum van vaart",
      payment_success_pdf_hero_total_label: "Totaalbedrag",
      payment_success_pdf_hero_total_note: "Huur inclusief BTW, betaald via Mollie",
      payment_success_pdf_hero_total_note_poa:
        "Totaal huur en administratie bij aankomst. Reserveringsdeel is online betaald (niet-restitueerbaar).",
      payment_success_pdf_price_breakdown_title: "Prijsoverzicht",
      payment_success_pdf_poa_paid_at_reservation: "Betaald bij reservering",
      payment_success_pdf_poa_huurdeel: "Huurdeel",
      payment_success_pdf_poa_total_paid_nonrefund: "Totaal betaald (niet-restitueerbaar)",
      payment_success_pdf_poa_total_arrival: "Totaal bij aankomst",
      payment_success_pdf_poa_including_deposit: "Inclusief borg",
      payment_success_pdf_total_cash_arrival: "Totaal contant bij aankomst (huur + borg)",
      payment_success_pdf_col_cancellation: "Annuleringsbeleid",
      payment_success_pdf_col_bring: "Wat mee te nemen",
      payment_success_pdf_col_practical: "Contact & locatie",
      payment_success_pdf_checkin_label: "Aankomst / check-in",
      payment_success_pdf_footer_wish:
        "Wij wensen u een behouden vaart en een fijne dag op het water in de Weerribben!",
      payment_failure_title: "Betaling mislukt",
      payment_failure_subtitle: "Helaas kon je betaling niet worden verwerkt. Probeer het opnieuw of neem contact met ons op.",
      payment_failure_try_again: "Opnieuw proberen",
      payment_failure_back: "Terug naar home",
      payment_failure_help_title: "Hulp nodig?",
      payment_failure_help_intro: "Als je problemen blijft ondervinden met de betaling:",
      payment_failure_help_1: "Controleer of je betaalgegevens correct zijn",
      payment_failure_help_2: "Zorg dat je voldoende saldo hebt",
      payment_failure_help_3: "Probeer een andere betaalmethode",
      payment_failure_help_4: "Neem direct contact met ons op",
      /* checkout page */
      checkout_title: "Afrekenen",
      checkout_empty_cart_title: "Je winkelwagen is leeg",
      checkout_empty_cart_desc: "Voeg boten toe aan je winkelwagen om te kunnen afrekenen.",
      checkout_empty_cart_btn: "Naar botenverhuur",
      checkout_reservations_title: "Je reserveringen",
      checkout_subtotal: "Huur (subtotaal)",
      checkout_admin_fee_label: "Administratiekosten ({percent}%)",
      checkout_poa_rental_label: "Huurprijs",
      checkout_poa_admin_slice_label:
        "Administratiekosten ({admin_percent}% op {res_percent}% reserveringsdeel)",
      checkout_total: "Totaal te betalen:",
      checkout_total_trip: "Totaal boeking (huur + administratie):",
      checkout_poa_row_pay_online: "Nu online betalen",
      checkout_poa_row_on_arrival: "Bij aankomst betalen",
      checkout_deposit_note: "Let op: Voor de gehuurde boot(en) dient een borg van €{amount} contant te worden voldaan bij aankomst.",
      checkout_your_details: "Jouw gegevens",
      checkout_name_label: "Volledige naam *",
      checkout_email_label: "E-mailadres *",
      checkout_phone_label: "Telefoonnummer *",
      checkout_address_label: "Adres (optioneel)",
      checkout_notes_label: "Opmerkingen (optioneel)",
      checkout_notes_placeholder: "Eventuele speciale wensen...",
      checkout_back_btn: "Terug",
      checkout_pay_btn: "Betalen",
      checkout_loading: "Je betaling wordt voorbereid...",
      checkout_error_fields: "Vul alle verplichte velden in.",
      checkout_error_email: "Voer een geldig e-mailadres in.",
      checkout_error_general: "Er is een fout opgetreden. Probeer het opnieuw.",
      checkout_day: "dag",
      checkout_days: "dagen",

      /* botenverhuur page – SEO blocks (missing keys) */
      boats_header_p: "Boot en sloep huren in de Weerribben bij Giethoorn — ontdek Nationaal Park Weerribben-Wieden vanaf het water.",
      boats_bluf_summary: "Bootje huren in de Weerribben? Bij Nijenhuis Botenverhuur in Wanneperveen ervaar je de rust en ruimte van Nationaal Park Weerribben-Wieden vanaf het water. Al meer dan 50 jaar het familiebedrijf voor gezinnen, stellen en vriendengroepen.",
      boats_intro_title: "Ontdek de Weerribben op jouw manier",
      boats_intro_text: "<p>Bootje huren in de Weerribben? Bij Nijenhuis Botenverhuur in Wanneperveen ervaar je de rust en ruimte van Nationaal Park Weerribben-Wieden vanaf het water. Al meer dan 50 jaar zijn wij het familiebedrijf waar gezinnen, stellen en vriendengroepen terugkomen voor een onvergetelijke dag op het water.</p><p>Onze locatie aan de Veneweg 199 in Wanneperveen ligt direct aan het water — slechts 10 kilometer van Giethoorn. Dat betekent: geen drukte bij het vertrek, gratis parkeren recht voor de deur, en directe toegang tot de mooiste vaarroutes door het grootste laagveenmoeras van Noordwest-Europa.</p><p>Of je nu kiest voor een fluisterstille electrosloep voor het hele gezin, een sportieve kano voor twee, of een traditionele zeilpunter — bij ons vind je de perfecte boot voor elke gelegenheid. En het mooiste: je hebt géén vaarbewijs nodig. We geven je voor vertrek een duidelijke uitleg én een gedetailleerde routekaart mee.</p>",
      boats_fleet_title: "Onze vloot: 25+ boten voor elk gezelschap",
      boats_fleet_electric_badge: "Meest populair",
      boats_fleet_electric_title: "Luxe Electrosloepen (Fluisterboten)",
      boats_fleet_electric_desc: "Onze electrosloepen zijn de absolute favoriet bij onze gasten. Deze fluisterboten zijn uitgerust met een stille elektromotor die je geruisloos door de Weerribben voert. Geen motorgeronk, geen uitlaatgassen — alleen het geluid van kabbelend water, zingende vogels en ruisend riet. Alle sloepen zijn voorzien van comfortabele kussens, een stuurwiel (geen pinnetje!), en een actieradius van 8 tot 10 uur.",
      boats_fleet_sail_badge: "Traditioneel en sportief",
      boats_fleet_sail_title: "Zeilboten & Punters",
      boats_fleet_sail_desc: "De punter is onlosmakelijk verbonden met de Weerribben. Beleef de historie zelf en huur een traditionele zeilpunter die alleen op wind en zeil vaart. Voor wie meer stabiliteit en comfort zoekt, hebben we de Randmeer zeilboten. Geen wind? Geen probleem — de zeilboten kunnen optioneel worden uitgerust met een buitenboordmotor.",
      boats_fleet_active_badge: "Actief op het water",
      boats_fleet_active_title: "Kano, Kajak & SUP",
      boats_fleet_active_desc: "Wil je de Weerribben van dichtbij meemaken? Met een kano of kajak kom je op plekken waar geen motorboot kan komen. Peddel door smalle slootjes, ontdek verborgen petgaten en spot bijzondere vogels vanuit het water.",
      boats_card_reserve: "Reserveer",
      boats_price_from: "Vanaf",
      boats_price_per_day: "/ dag",
      boats_card_title_classic_tender_720: "Classic Tender 720",
      boats_card_specs_classic_tender_720: "<li>Geschikt voor maximaal 12 personen</li><li>Ruime indeling met zithoek en tafel</li><li>Actieradius: 8–10 uur op één lading</li>",
      boats_card_title_classic_tender_570: "Classic Tender 570",
      boats_card_specs_classic_tender_570: "<li>Geschikt voor maximaal 8 personen</li><li>Ideaal voor gezinnen en kleine groepen</li><li>Actieradius: 8–10 uur op één lading</li>",
      boats_card_title_electrosloop_10: "Electrosloep 10-persoons",
      boats_card_specs_electrosloop_10: "<li>Extra ruimte voor grotere groepen</li><li>Voorzien van zwemtrap</li>",
      boats_card_title_electrosloop_8: "Electrosloep 8-persoons",
      boats_card_specs_electrosloop_8: "<li>Compact en eenvoudig te besturen</li><li>Perfect voor een dagje uit met vrienden</li>",
      boats_card_title_electroboat_5: "Electroboot 5-persoons",
      boats_card_specs_electroboat_5: "<li>Onze kleinste elektrische boot</li><li>Ideaal voor stellen of een klein gezin</li>",
      boats_card_title_sailboat_4_5: "Zeilboot 't Waar (4–5 personen)",
      boats_card_specs_sailboat_4_5: "<li>Stabiele Randmeer zeilboot</li><li>Optioneel met buitenboordmotor (€85/dag)</li>",
      boats_card_title_sailpunter_3_4: "Zeilpunter (3–4 personen)",
      boats_card_specs_sailpunter_3_4: "<li>Traditioneel houten vaartuig</li><li>Alleen op wind en zeil — de authentieke ervaring</li>",
      boats_card_title_canoe_3: "Kano (3 personen)",
      boats_card_specs_canoe_3: "<li>Stabiele Canadese kano</li><li>Inclusief peddels en vaarkaart</li>",
      boats_card_title_kayak_2: "Kajak 2-persoons",
      boats_card_specs_kayak_2: "<li>Sportief en wendbaar</li><li>Inclusief peddels</li>",
      boats_card_title_kayak_1: "Kajak 1-persoons",
      boats_card_specs_kayak_1: "<li>Solo avontuur op het water</li><li>Inclusief peddel</li>",
      boats_card_title_sup_board: "SUP Board",
      boats_card_specs_sup_board: "<li>Stand-up paddleboarden in de natuur</li><li>Inclusief peddel</li>",
      boats_why_title: "Waarom kiezen voor Nijenhuis Botenverhuur?",
      boats_why_water_title: "Direct aan het water",
      boats_why_water_desc: "Onze locatie ligt letterlijk aan het water. Je stapt uit de auto, loopt naar de steiger en vaart weg. Geen gedoe met trailers, geen wachtrijen.",
      boats_why_parking_title: "Gratis parkeren",
      boats_why_parking_desc: "Bij ons parkeer je altijd gratis, direct bij de verhuurlocatie. In Giethoorn zelf betaal je al snel €10–15 voor parkeren.",
      boats_why_quiet_title: "Rust in plaats van drukte",
      boats_why_quiet_desc: "Giethoorn is prachtig, maar in het hoogseizoen ook druk. Door vanuit Wanneperveen te vertrekken mis je de drukte bij het instappen en geniet je direct van de rust op het water. Na 15–20 minuten varen ben je in Giethoorn.",
      boats_why_service_title: "Persoonlijke service",
      boats_why_service_desc: "Als familiebedrijf kennen we elke boot en elke vaarroute. We nemen de tijd voor een uitgebreide uitleg en geven je tips voor de mooiste plekjes die niet in de reisgids staan.",
      boats_why_flexible_title: "Flexibel huren",
      boats_why_flexible_desc: "<ul class=\"anchor-list\"><li><strong>Per dag:</strong> reserveer online of telefonisch</li><li><strong>Per uur:</strong> kan alleen ter plaatse, voor spontane bezoekers</li><li><strong>Contant en pin</strong> geaccepteerd</li></ul>",
      boats_routes_title: "Populaire vaarroutes vanaf Wanneperveen",
      boats_route_1_title: "Route 1: Naar Giethoorn",
      boats_route_1_meta: "8–10 km, 1,5–2 uur enkele reis",
      boats_route_1_desc: "Vaar via de kanalen richting het beroemde Giethoorn. Bewonder de rietgedekte boerderijen, karakteristieke bruggetjes en het schilderachtige dorpscentrum. Ideaal als dagtrip.",
      boats_route_2_title: "Route 2: Belt-Schutsloot",
      boats_route_2_meta: "6–8 km, 1–1,5 uur",
      boats_route_2_desc: "Het \"verborgen Giethoorn\" — dezelfde charme, maar zonder de toeristische drukte. Authentieke bruggetjes, historische boerderijen en een rustieke sfeer.",
      boats_route_3_title: "Route 3: Weerribben Natuur",
      boats_route_3_meta: "15 km, 3–4 uur",
      boats_route_3_desc: "Diep het Nationaal Park in. Peddel of vaar door smalle slootjes, ontdek petgaten en spot bijzondere flora en fauna. Ideaal per kano of kajak.",
      boats_route_4_title: "Route 4: Beulakerwijde",
      boats_route_4_meta: "10 km, 2–3 uur",
      boats_route_4_desc: "Het grote meer ten zuiden van Wanneperveen. Open water, prachtige vergezichten en perfecte plek voor zeilen.",
      boats_routes_map_link: "Bekijk onze interactieve vaarkaart voor gedetailleerde routes →",
      boats_route_cta: "Bekijk op vaarkaart →",
      boats_faq_q1: "Heb ik een vaarbewijs nodig?",
      boats_faq_a1: "Nee, voor geen van onze boten is een vaarbewijs nodig. Alle boten zijn kleiner dan 15 meter en varen langzamer dan 20 km/u. Je krijgt voor vertrek een persoonlijke instructie.",
      boats_faq_q2: "Hoe ver kan ik varen met een elektrische boot?",
      boats_faq_a2: "De accu's gaan een hele dag mee bij normaal gebruik. De route naar Giethoorn en terug (±20 km) is geen probleem. Bij aankomst controleren we altijd of de accu volledig is opgeladen.",
      boats_faq_q3: "Wat kost bootje huren bij Nijenhuis?",
      boats_faq_a3: "Prijzen starten vanaf €20 per dag voor een kano of kajak. Electrosloepen zijn er vanaf €95 per halve dag. Bekijk de volledige prijslijst op onze boekingspagina.",
      boats_faq_q4: "Kan ik een boot huren voor 12 personen?",
      boats_faq_a4: "Ja, onze Classic Tender 720 is geschikt voor maximaal 12 personen. Voor optimaal comfort raden we 10 personen aan. Bij grotere groepen kun je ook twee boten naast elkaar boeken.",
      boats_faq_q5: "Mag ik mijn hond meenemen?",
      boats_faq_a5: "Huisdieren zijn toegestaan op de electrosloepen, kano's, de zeilpunter en de electroboot. Op de Classic Tenders zijn huisdieren niet toegestaan.",
      boats_faq_q6: "Wat als het slecht weer is?",
      boats_faq_a6: "Bij lichte regen kun je gewoon varen — neem regenkleding mee. Bij onweer of storm adviseren we om niet het water op te gaan. Bij extreme omstandigheden kun je gratis omboeken naar een andere datum.",
      boats_faq_q7: "Hoe laat kan ik vertrekken?",
      boats_faq_a7: "Je kunt vanaf 9:00 uur 's ochtends vertrekken. De laatste verhuurtijden zijn afhankelijk van het seizoen. In de zomer kun je tot 18:00 uur een boot ophalen voor een avondvaart.",
      boats_faq_q8: "Is er parkeergelegenheid?",
      boats_faq_a8: "Ja, bij Nijenhuis Botenverhuur in Wanneperveen is parkeren helemaal gratis. Je parkeert direct naast de steiger, zodat je meteen het water op kunt.",
      boats_faq_all_link: "Bekijk alle veelgestelde vragen →",
      boats_fishing_title: "Vissen vanaf onze boten in de Weerribben",
      boats_fishing_p1: "Ontdek waarom de Weerribben een visparadijs in Overijssel is – perfect voor visliefhebbers. Hoewel we in Nederland geen gespecialiseerde visboten met tenten of karperboten te huur aanbieden, zijn onze ruime elektrische sloepen en kano's ideaal voor een dagje vissen.",
      boats_fishing_p2: "De stille elektromotoren storen de karpers en roofbleien niet, en met een kajakverhuur in de Weerribben kunt u de rustigste visplekken bereiken waar motorboten niet kunnen komen. Neem uw hengel mee voor een visvakantie in Wanneperveen – <a href=\"/vaarkaart\">bekijk onze vaarkaart</a> voor de beste visplekken in Belterwiede!",
      boats_cta_h2: "Boek nu jouw boot",
      boats_cta_p: "Klaar om de Weerribben te ontdekken? Reserveer vandaag nog je boot en geniet van een onvergetelijke dag op het water bij Giethoorn.",
      boats_cta_details: "<ul class=\"boats-cta-list anchor-list\"><li><strong>Online boeken:</strong> gebruik het reserveringsformulier bovenaan deze pagina</li><li><strong>Bellen:</strong> <a href=\"tel:0522281528\">0522 281 528</a></li><li><strong>Bezoek ons:</strong> Veneweg 199, 7946 LP Wanneperveen</li></ul>",
      boats_cta_hours: "Open van 1 april t/m 31 oktober, dagelijks 09:00–18:00. Geen vaarbewijs nodig. Contant en pin geaccepteerd.",
      boats_cta_btn: "Nu boeken",
      boats_cta_phone: "📞 Bel ons",

      /* booking page – missing keys */
      booking_options_title: "Extra opties",
      booking_option_motor: "Motor erbij huren?",

      /* checkout page – missing keys */
      checkout_home_btn: "🏠 Naar website",
      checkout_policy_title: "Belangrijke informatie",
      checkout_policy_cancellation:
        "Bij annulering wordt een annuleringsvergoeding van 10% van het totaalbedrag in rekening gebracht.",
      checkout_policy_cancellation_poa:
        "De online betaalde reserveringsbijdrage is niet-restitueerbaar.",
      checkout_policy_contact: "Voor wijzigingen neem je telefonisch contact met ons op via +31 522 281 528.",
      checkout_policy_location: "Onze locatie: Veneweg 199, 7946 LP Wanneperveen",
      checkout_secure_title: "Veilig afrekenen",
      checkout_step_details: "Gegevens",
      checkout_step_payment: "Betaling",
      checkout_step_confirm: "Bevestiging",
      checkout_booking_summary: "Reserveringsoverzicht",
      checkout_payment_info_title: "Betaalmethode",
      checkout_payment_info_body:
        "Kies hieronder hoe je wilt betalen. Daarna ga je door naar de beveiligde betaalpagina van Mollie om de betaling af te ronden.",
      checkout_method_ideal: "iDEAL",
      checkout_method_bancontact: "Bancontact",
      checkout_method_applepay: "Apple Pay",
      checkout_method_googlepay: "Google Pay",
      checkout_wallet_divider: "Of betaal met",
      checkout_method_pay_on_arrival: "Betalen bij aankomst",
      checkout_pay_on_arrival_inline: "Laatste ophaaltijd 11:00 uur.",
      checkout_poa_fee_explain:
        "Overzicht: volledige huur; administratie = alleen {admin_percent}% over het {percent}%-reserveringsdeel (dus niet {admin_percent}% over de hele huur). Online betaal je dat reserveringsdeel plus die administratie; bij aankomst het overige deel van de huur.",
      checkout_poa_fee_explain_no_admin_fee:
        "Je betaalt online {percent}% van de huur als reservering; het restant betaal je bij aankomst.",
      checkout_poa_pay_now_line:
        "Nu online te betalen: €{reservation}\nNog te voldoen bij aankomst: €{balance}\n(Reserveringsdeel: {percent}% van de huursom; administratie alleen op dat deel.)",
      checkout_error_pay_on_arrival_time:
        "Betalen bij aankomst is alleen mogelijk met een aankomsttijd tot 11:00. Kies een eerdere tijd of een andere betaalmethode.",
      checkout_trust_secure: "Veilige betaling via Mollie",
      checkout_trust_support: "Hulp nodig? Bel 0522 281 528",
      checkout_trust_policy: "Zie annuleringsvoorwaarden in het blok hiernaast",
      checkout_qty_label: "Aantal:",
      checkout_arrival_time_label: "Aankomsttijd *",
      checkout_city_label: "Woonplaats *",

      /* global – cart sidebar */
      cart_title: "🛒 Winkelwagen",
      cart_close_aria: "Sluiten",
      admin_fee_disclosure_note:
        "Bij online betalen komt er {percent}% administratiekosten bij de huursom.",
      cart_empty: "Je winkelwagen is leeg",
      cart_total_label: "Totaal:",
      cart_checkout_btn: "Afrekenen",
      cart_clear_btn: "Wissen",
      compare_max_pins: "Je kunt maximaal 3 boten vergelijken.",

      /* booking modal */
      booking_modal_checking_availability: "Beschikbaarheid controleren...",
      booking_modal_end_date_label: "Einddatum (optioneel)",
      booking_modal_engine_option: "Met buitenboordmotor (+ meerprijs)",
      booking_modal_direct_checkout_btn: "💳 Direct afrekenen",
      booking_modal_confirm_btn: "Reservering bevestigen",
      booking_modal_cancel_btn: "Annuleren",
      booking_modal_success_title: "Reservering geslaagd!",
      booking_modal_success_text: "Je reservering is bevestigd. Je ontvangt binnenkort een bevestigingsmail.",
      booking_modal_booking_id_label: "Reserverings-ID:",
      booking_modal_error_title: "Fout",
      booking_modal_error_default: "Er is een fout opgetreden bij het verwerken van je reservering.",
      booking_modal_retry_btn: "Opnieuw proberen",

      /* home page – about block */
      home_about_title: "Over Nijenhuis Botenverhuur",
      home_about_tagline: "Al meer dan 50 jaar jouw vertrouwde partner voor watersport in de Weerribben",

      /* camping page */
      camping_overview_cta_text: "Neem contact met ons op voor de mogelijkheden en beschikbaarheid.",
      season_status_open: "Nu open",
      season_status_closed_until: "Gesloten tot 1 april",

      /* vakantiehuis page */
      house_visit_website_btn: "🌐 Bezoek Waterpark Belterwiede",

      /* contact page */
      contact_success_title: "✅ Bericht succesvol verzonden!",
      contact_success_message: "Bedankt voor je bericht. We nemen zo snel mogelijk contact met je op via het opgegeven e-mailadres.",
      contact_success_sent_to: "Je bericht is verzonden naar: info@nijenhuis-botenverhuur.nl",

      /* checkout (inline JS strings) */
      checkout_confirm_remove_item: "Weet je zeker dat je deze reservering wilt verwijderen?",
      checkout_notification_removed: "Reservering verwijderd",
      checkout_notification_remove_error: "Fout bij verwijderen",
      checkout_error_unavailable_boats:
        "Helaas zijn de volgende boot(en) inmiddels niet meer beschikbaar: {boats}. Verwijder deze uit je winkelwagen en probeer het opnieuw.",
      checkout_error_payment_method: "Kies een geldige betaalmethode.",
      checkout_error_pay_on_arrival_time:
        "Betalen bij aankomst is alleen mogelijk met een aankomsttijd tot 11:00. Kies een eerdere tijd of een andere betaalmethode.",

      /* payment failure (inline JS strings) */
      payment_failure_status_failed: "Betaling status: {status}. De betaling is niet gelukt.",
      payment_failure_status_pending:
        "Betaling status: {status}. Je betaling wordt nog verwerkt. Check je e-mail voor updates.",

      /* FAQ page */
      faq_header_h1: "Veelgestelde vragen over boot huren in de Weerribben",
      faq_header_p: "Alles wat je moet weten over boot huren bij Nijenhuis",
      faq_intro_expanded: "Op deze pagina vind je antwoorden op de meest gestelde vragen over boot huren bij Nijenhuis Botenverhuur in de Weerribben. Onderwerpen die aan bod komen: prijzen per boottype, of je een vaarbewijs nodig hebt, openingstijden en reserveren, wat er bij de huur inbegrepen is, of je naar Giethoorn mag varen, en praktische zaken zoals betaling en huisdieren. Staat je vraag er niet bij? Neem gerust contact met ons op via het telefoonnummer of het contactformulier – we helpen je graag verder.",
      faq_contact_cta_p: "Staat je vraag er niet bij? Neem gerust contact met ons op.",
      faq_contact_cta_form: "Contactformulier",
      faq_fleet_title: "Bekijk onze vloot",
      faq_fleet_subtitle: "Kies het boottype dat het beste bij jouw groep en wensen past:",
      faq_fleet_cta: "Beschikbaarheid bekijken →",
      faq_page_html:
        "<div class='faq-intro' style='max-width: 800px; margin: 0 auto 2rem;'><p style='font-size: 1.1rem; line-height: 1.7; color: var(--text-secondary);'>Hier vind je antwoorden op de meest gestelde vragen over boot huren bij Nijenhuis Botenverhuur. Staat je vraag er niet bij? Neem gerust <a href='/contact'>contact</a> met ons op of bel <a href='tel:0522281528'>0522 281 528</a>.</p></div><div class='faq-list' style='max-width: 800px; margin: 0 auto;'><h2 style='margin-top: 2rem; color: var(--secondary-color);'>💰 Prijzen &amp; betaling</h2><div class='faq-item' style='margin-bottom: 1.5rem; padding: 1.5rem; background: #f8fafc; border-radius: 12px;'><h3 style='margin: 0 0 0.75rem; font-size: 1.15rem; color: var(--secondary-color);'>Wat kost het om een boot te huren bij Nijenhuis?</h3><div class='faq-answer' style='line-height: 1.7;'><p>De huurprijzen variëren per boottype:</p><ul id='faq-price-list' style='margin: 0.5rem 0; padding-left: 1.5rem;'></ul><p style='margin-top: 0.75rem;'>Bij meerdaagse verhuur krijg je korting. <a href='/botenverhuur'>Bekijk alle prijzen →</a></p></div></div><div class='faq-item' style='margin-bottom: 1.5rem; padding: 1.5rem; background: #f8fafc; border-radius: 12px;'><h3 style='margin: 0 0 0.75rem; font-size: 1.15rem; color: var(--secondary-color);'>Hoe betaal ik?</h3><div class='faq-answer' style='line-height: 1.7;'><p>Je kunt betalen met:</p><ul style='margin: 0.5rem 0; padding-left: 1.5rem;'><li>Contant geld</li><li>Pinpas</li><li>Online via iDEAL bij het reserveren</li></ul><p>De borg (<span data-faq-dynamic='deposit-range'></span>) betaal je ter plaatse <strong>contant</strong> en krijg je terug bij onbeschadigde retour.</p></div></div><div class='faq-item' style='margin-bottom: 1.5rem; padding: 1.5rem; background: #f8fafc; border-radius: 12px;'><h3 style='margin: 0 0 0.75rem; font-size: 1.15rem; color: var(--secondary-color);'>Moet ik borg betalen?</h3><div class='faq-answer' style='line-height: 1.7;'><p>Ja, voor de sloepen vragen wij een borg van <span data-faq-dynamic='deposit-sloep'></span> die <strong>contant</strong> betaald moet worden bij aankomst. Voor de zeilboot is een borg van <span data-faq-dynamic='deposit-zeilboot'></span> <strong>contant</strong> vereist. Voor kano's en SUPs is meestal geen borg vereist, maar vragen we wel een geldig legitimatiebewijs achter te laten.</p></div></div><h2 style='margin-top: 2.5rem; color: var(--secondary-color);'>📋 Praktische informatie</h2><div class='faq-item' style='margin-bottom: 1.5rem; padding: 1.5rem; background: #f8fafc; border-radius: 12px;'><h3 style='margin: 0 0 0.75rem; font-size: 1.15rem; color: var(--secondary-color);'>Heb ik een vaarbewijs nodig?</h3><div class='faq-answer' style='line-height: 1.7;'><p><strong>Nee</strong>, voor alle boten bij Nijenhuis Botenverhuur is geen vaarbewijs vereist. Onze elektrische sloepen en boten varen langzaam (maximaal 6 km/u) en zijn eenvoudig te bedienen.</p><p style='margin-top: 0.5rem;'>Voor vertrek krijg je een korte instructie over de bediening en de vaarregels in het Weerribbengebied.</p></div></div><div class='faq-item' style='margin-bottom: 1.5rem; padding: 1.5rem; background: #f8fafc; border-radius: 12px;'><h3 style='margin: 0 0 0.75rem; font-size: 1.15rem; color: var(--secondary-color);'>Moet ik vooraf reserveren?</h3><div class='faq-answer' style='line-height: 1.7;'><p><strong>Ja, reserveren is aan te raden</strong>, vooral in het hoogseizoen en in het weekend.</p><p>Zonder reservering is beschikbaarheid niet gegarandeerd. <a href='/booking'>Reserveer online →</a></p></div></div><h2 style='margin-top: 2.5rem; color: var(--secondary-color);'>🌧️ Weer &amp; veiligheid</h2><div class='faq-item' style='margin-bottom: 1.5rem; padding: 1.5rem; background: #f8fafc; border-radius: 12px;'><h3 style='margin: 0 0 0.75rem; font-size: 1.15rem; color: var(--secondary-color);'>Wat gebeurt er bij slecht weer?</h3><div class='faq-answer' style='line-height: 1.7;'><p>Bij extreme weersomstandigheden (storm, onweer) kunnen we besluiten de boten niet uit te laten varen voor jouw veiligheid.</p><p>In dat geval kun je je reservering <strong>kosteloos verzetten</strong> naar een andere datum. Bij lichte regen kun je gewoon varen – een regenjas meenemen is dan aan te raden.</p></div></div><h2 style='margin-top: 2.5rem; color: var(--secondary-color);'>🚤 Boot, sloep &amp; SUP</h2><div class='faq-item' style='margin-bottom: 1.5rem; padding: 1.5rem; background: #f8fafc; border-radius: 12px;'><h3 style='margin: 0 0 0.75rem; font-size: 1.15rem; color: var(--secondary-color);'>Wat is een fluisterboot?</h3><div class='faq-answer' style='line-height: 1.7;'><p>Een fluisterboot is een elektrische sloep (electrosloep) die fluisterstil vaart dankzij de elektromotor. Bij Nijenhuis kun je fluisterbootjes huren om naar Giethoorn te varen. Ze zijn ideaal voor gezinnen en groepen.</p></div></div><div class='faq-item' style='margin-bottom: 1.5rem; padding: 1.5rem; background: #f8fafc; border-radius: 12px;'><h3 style='margin: 0 0 0.75rem; font-size: 1.15rem; color: var(--secondary-color);'>Kan ik vissen vanuit een gehuurde boot?</h3><div class='faq-answer' style='line-height: 1.7;'><p>Ja, de Weerribben biedt uitstekende vismogelijkheden. Hoewel wij geen gespecialiseerde visboten of karperbootjes met tent verhuren, zijn onze electrosloepen en kano's uitstekend geschikt voor een dag vissen. De stille fluisterboot-motor stoort de vissen niet.</p></div></div><div class='faq-item' style='margin-bottom: 1.5rem; padding: 1.5rem; background: #f8fafc; border-radius: 12px;'><h3 style='margin: 0 0 0.75rem; font-size: 1.15rem; color: var(--secondary-color);'>Kan ik een bootje huren voor een paar uur?</h3><div class='faq-answer' style='line-height: 1.7;'><p>Ja, voor alle boten is ook uurverhuur mogelijk. Uurverhuur kan alleen direct ter plaatse bij onze bootverhuur worden geboekt, niet online of telefonisch. Kom langs voor beschikbaarheid.</p></div></div><div class='faq-item' style='margin-bottom: 1.5rem; padding: 1.5rem; background: #f8fafc; border-radius: 12px;'><h3 style='margin: 0 0 0.75rem; font-size: 1.15rem; color: var(--secondary-color);'>Bieden jullie SUP boards aan bij Giethoorn?</h3><div class='faq-answer' style='line-height: 1.7;'><p>Ja, wij verhuren SUP boards. Je kunt stand-up paddelen door de Weerribben naar Giethoorn. SUP in Giethoorn is een actieve en unieke manier om het gebied te verkennen.</p></div></div><div class='faq-item' style='margin-bottom: 1.5rem; padding: 1.5rem; background: #f8fafc; border-radius: 12px;'><h3 style='margin: 0 0 0.75rem; font-size: 1.15rem; color: var(--secondary-color);'>Kan ik een luxe sloep huren voor Giethoorn?</h3><div class='faq-answer' style='line-height: 1.7;'><p>Ja, bij Nijenhuis kun je luxe sloepen (electrosloepen/fluisterboten) huren om naar Giethoorn te varen. Onze sloepen zijn geschikt voor 8 tot 12 personen. Sloepverhuur Giethoorn – reserveren wordt aanbevolen.</p></div></div><div class='faq-item' style='margin-bottom: 1.5rem; padding: 1.5rem; background: #f8fafc; border-radius: 12px;'><h3 style='margin: 0 0 0.75rem; font-size: 1.15rem; color: var(--secondary-color);'>Kan ik een vakantieboot huren?</h3><div class='faq-answer' style='line-height: 1.7;'><p>Ja, op vakantie in de Weerribben? Combineer uw verblijf met het huren van een boot. Combineer met ons vakantiehuis of onze camping voor de perfecte vakantie boot huren ervaring.</p></div></div><div class='faq-item' style='margin-bottom: 1.5rem; padding: 1.5rem; background: #f8fafc; border-radius: 12px;'><h3 style='margin: 0 0 0.75rem; font-size: 1.15rem; color: var(--secondary-color);'>Bent u op zoek naar bootverhuur vanuit de Randstad, zoals Alphen aan den Rijn?</h3><div class='faq-answer' style='line-height: 1.7;'><p>Overweeg een dagje naar de Weerribben bij Giethoorn voor een unieke natuurervaring. Vanuit Wanneperveen vaar je door Nationaal Park Weerribben-Wieden naar het Venetië van het Noorden. Ongeveer 1,5 uur rijden vanuit Alphen aan den Rijn.</p></div></div></div>"
    },

    /* ---------- German – informal (“du”) ------------------- */
    de: {
      nav_opening: "Öffnungszeiten: 9:00 – 18:00 Uhr",
      nav_boats: "Bootsverleih",
      nav_house: "Ferienhaus",
      nav_forsale: "Zu verkaufen",
      nav_camping: "Camping",
      nav_chart: "Seekarte",
      nav_blog: "Blog",
      nav_faq: "Häufige Fragen",
      nav_more: "Mehr",
      nav_contact: "Kontakt",
      /* Boat Modal & Dynamic JS */
      boat_modal_description_title: "Beschreibung",
      boat_modal_features_title: "Eigenschaften",
      boat_modal_rates_title: "Preise",
      boat_modal_capacity: "{n} Personen Kapazität",
      capacity_short: "{n} Pers.",
      feature_electric_motor: "Elektromotor",
      feature_silent_eco: "Leise und umweltfreundlich",
      feature_sailing: "Segeln",
      feature_traditional: "Traditionell",
      feature_paddling: "Paddeln",
      feature_sporty: "Sportlich",
      feature_sup: "Stand-up Paddleboard",
      feature_unique: "Einzigartige Erfahrung",
      price_per_day: "€{price} pro Tag",
      price_per_day_without_motor: "€{price} / Tag (ohne Motor)",
      price_per_day_with_motor: "€{price} / Tag (mit Motor)",
      price_deposit: "Kaution: €{price}",
      price_deposit_none: "Keine Kaution erforderlich",
      status_available: "Verfügbar",
      status_occupied: "Besetzt",
      btn_more_info: "ℹ️ Mehr Info",
      btn_reserve: "📅 Reservieren",
      btn_close: "Schließen",
      rate_duration: "Dauer",
      rate_price: "Preis",
      duration_day_1: "1 Tag",
      duration_day_other: "{n} Tage",
      duration_week_1: "1 Woche",

      /* index.html */
      hero_book_h2: "Direkt buchen",
      hero_book_p: "Buche einfach dein Boot für einen Tag auf dem Wasser",
      hero_book_date: "Datum",
      hero_book_boat_type: "Boot type",
      hero_book_boat_type_select: "Boot type wählen",
      hero_book_boat_type_classic_tender_720: "Classic tender 720 10/12 pers",
      hero_book_boat_type_classic_tender_570: "Classic tender 570 8 pers",
      hero_book_boat_type_electrosloop_10: "Electrosloep für 10 pers",
      hero_book_boat_type_electrosloop_8: "Electrosloep für 8 pers",
      hero_book_boat_type_electroboat_5: "Electrosloep für 5 pers",
      hero_book_boat_type_sailboat_4_5: "Segelboot",
      hero_book_boat_type_sailpunter_3_4: "Segelpunter 3/4 pers",
      hero_book_boat_type_canoe_3: "Kanadisches Kanu 3 pers",
      hero_book_boat_type_kayak_2: "Kajak 2 pers",
      hero_book_boat_type_kayak_1: "Kajak 1 pers",
      hero_book_boat_type_sup_board: "SUP board 1 pers",
      /* Boat descriptions and features */
      boat_classic_tender_720_name: "Classic tender 720",
      boat_classic_tender_720_description: "Ein luxuriöses elektrisches Tender für größere Gruppen. Perfekt für komfortable Bootsfahrten durch das Naturschutzgebiet.",
      boat_classic_tender_720_features: "10-12 Personen Kapazität, Elektromotor, Luxuriös und komfortabel, Leise und umweltfreundlich, Perfekt für größere Gruppen, Haustiere erlaubt",

      boat_classic_tender_570_name: "Classic tender 570",
      boat_classic_tender_570_description: "Ein elegantes elektrisches Tender für mittlere Gruppen. Ideal für entspannte Bootsfahrten.",
      boat_classic_tender_570_features: "8 Personen Kapazität, Elektromotor, Elegant und komfortabel, Leise und umweltfreundlich, Perfekt für Familien, Haustiere erlaubt",

      boat_electrosloop_10_name: "Electrosloep für 10 pers",
      boat_electrosloop_10_description: "Ein geräumiges elektrisches Boot für größere Gruppen. Perfekt für gesellige Bootsfahrten.",
      boat_electrosloop_10_features: "10 Personen Kapazität, Elektromotor, Geräumig und komfortabel, Leise und umweltfreundlich, Perfekt für Gruppen, Haustiere erlaubt",

      boat_electrosloop_8_name: "Electrosloep für 8 pers",
      boat_electrosloop_8_description: "Ein komfortables elektrisches Boot für Familien und Freundesgruppen.",
      boat_electrosloop_8_features: "8 Personen Kapazität, Elektromotor, Komfortabel und stabil, Leise und umweltfreundlich, Perfekt für Familien, Haustiere erlaubt",

      boat_electroboat_5_name: "Electrosloep für 5 pers",
      boat_electroboat_5_description: "Ein kompaktes elektrisches Boot für kleine Gruppen. Ideal für ruhige Bootsfahrten.",
      boat_electroboat_5_features: "5 Personen Kapazität, Elektromotor, Kompakt und wendig, Leise und umweltfreundlich, Perfekt für kleine Gruppen, Haustiere erlaubt",

      boat_sailboat_name: "Segelboot",
      boat_sailboat_description: "Ein traditionelles Segelboot verfügbar mit oder ohne Motor. Ohne Motor für erfahrene Segler, mit Motor für mehr Flexibilität.",
      boat_sailboat_features: "4-5 Personen Kapazität, Segeln ohne Motor: €70, Segeln mit Motor: €85, Flexibel, Für alle Niveaus, Erfahrener Segler erforderlich, Haustiere nicht erlaubt",

      boat_sailpunter_name: "Segelpunter",
      boat_sailpunter_description: "Ein traditioneller Segelpunter für den erfahrenen Segler. Genieße Wind und Natur.",
      boat_sailpunter_features: "3-4 Personen Kapazität, Segeln, Traditionell, Sportlich, Für erfahrene Segler, Erfahrener Segler erforderlich, Haustiere nicht erlaubt",

      boat_canoe_name: "Kanadisches Kanu",
      boat_canoe_description: "Ein stabiles kanadisches Kanu für sportliche Aktivitäten und das Erkunden kleinerer Wasserwege.",
      boat_canoe_features: "3 Personen Kapazität, Paddeln, Sportlich, Stabil, Für alle Niveaus, Haustiere erlaubt",

      boat_kayak_2_name: "Kajak 2 pers",
      boat_kayak_2_description: "Ein Tandem-Kajak für zwei Personen. Perfekt für sportliche Aktivitäten.",
      boat_kayak_2_features: "2 Personen Kapazität, Paddeln, Sportlich, Wendig, Für alle Niveaus, Haustiere erlaubt",

      boat_kayak_1_name: "Kajak 1 pers",
      boat_kayak_1_description: "Ein Solo-Kajak für individuelle Bootsfahrten. Ideal für sportliche Aktivitäten.",
      boat_kayak_1_features: "1 Person Kapazität, Paddeln, Sportlich, Wendig, Für alle Niveaus, Haustiere erlaubt",

      boat_sup_name: "SUP board",
      boat_sup_description: "Ein Stand-up-Paddleboard für eine einzigartige Art, das Wasser zu erleben.",
      boat_sup_features: "1 Person Kapazität, Paddeln, Einzigartig, Balancieren, Für alle Niveaus, Haustiere erlaubt",

      hero_book_btn: "Jetzt buchen",
      btn_outline: "📞 Jetzt anrufen!",
      btn_add_to_cart: "🛒 Zum Warenkorb hinzufügen",
      hero_book_badge: "100% sicher &amp; unverbindlich",
      hero_h1: "Bootsverleih Weerribben | Nijenhuis",
      hero_h1_p: "Erlebe die Schönheit des Weerribben-Gebiets mit unserem Premium-Bootsverleih. Kano mieten, SUP Verleih Overijssel – perfekt für Familien, Freunde und Naturliebhaber.",
      hero_btn: "Verfügbarkeit prüfen",
      intro_h2: "Entfliehe dem Alltag mit dem Bootsverleih der Weerribben",
      intro_h2_p: "In unserer hektischen Welt sehnt sich jeder nach Ruhe. Lass Staus, Stress und die tägliche Routine hinter dir – entdecke den Nationalpark Weerribben-Wieden vom Wasser aus bei Nijenhuis Bootsverleih in Wanneperveen, dem Bootsverleih der Weerribben.",
      intro_h2_p2: "Boot mieten für Quality Time mit Familie oder Freunden. Kano mieten, SUP Verleih Overijssel – unsere Flüsterboote und Elektro-Sloopen gleiten leise durch die schmalsten Gräben. Schaffe unvergessliche Momente.",
      deposit_notice_cash: "<strong>Bitte beachten:</strong> Die Kaution muss bei Ankunft bar bezahlt werden.",
      intro_h3: "Warum Nijenhuis wählen?",
      intro_h3_li1: "📍 Gelegen im Herzen des Weerribben-Naturschutzgebietes",
      intro_h3_li2: "🚤 Große Auswahl an Booten für alle Vorlieben",
      intro_h3_li3: "🌿 Verfügbarkeit milieubewusster elektrischer Booten",
      intro_h3_li4: "👨‍👩‍👧‍👦 Perfekt für Familien und Gruppen",
      intro_h3_li5: "💰 Konkurrenzstarke Preise für alle Budgets",
      intro_h3_li6: "📞 Persönliche Service und Unterstützung",
      intro_cta_p: "Für mehr Informationen, ruf uns an 0522 - 281 528",
      intro_cta_p2: "Barzahlung und Kartenzahlung akzeptiert",

      /* About Grid */
      about_location_title: "Standort",
      about_location_desc: "Wanneperveen, Overijssel<br><span class='fact-sub'>10 km von Giethoorn</span>",
      about_season_title: "Saison",
      about_season_desc: "1. April – 31. Oktober<br><span class='fact-sub'>Täglich 09:00–18:00</span>",
      about_fleet_title: "Boote",
      about_fleet_desc: "25+ Boote<br><span class='fact-sub'>1 bis 12 Personen</span>",
      about_prices_title: "Preise",
      about_prices_desc: "Ab €20/Tag<br><span class='fact-sub'>Kein Führerschein erforderlich</span>",

      index_season_title: "Saisoncamping",
      index_season_dates: "Geöffnet vom 1. April bis 31. Oktober",
      index_season_status: "Jetzt für Reservierungen geöffnet",

      index_camping_title: "Saisoncamping in den Weerribben",
      index_camping_description: "Genieße eine einzigartige Campingerfahrung mitten im wunderschönen Naturschutzgebiet Weerribben. Unser Saisoncamping ist vom 1. April bis 31. Oktober geöffnet und bietet eine ruhige, gemütliche Umgebung für deinen Wohnwagen.",
      index_camping_feature_1_title: "Saisoncamping",
      index_camping_feature_1_desc: "Geöffnet vom 1. April bis 31. Oktober",
      index_camping_feature_2_title: "Wohnwagen das ganze Jahr",
      index_camping_feature_2_desc: "Wohnwagen können das ganze Jahr stehen bleiben",
      index_camping_feature_3_title: "Moderne Einrichtungen",
      index_camping_feature_3_desc: "Wasser, Strom, Sanitäranlagen und eigener Bootsanleger",
      index_camping_feature_4_title: "Bootsverleih Saison",
      index_camping_feature_4_desc: "Bootsverleih nur während der Saison verfügbar",
      index_camping_cta_text: "Für weitere Informationen über unser Saisoncamping",
      services_h2: "Unsere Dienstleistungen",
      services_h3_1: "Bootsverleih",
      services_p_1: "Bootsverleih Weerribben, Kano mieten, SUP Verleih Overijssel. Elektrische Boote, Kanus und Kajaks für alle Altersgruppen.",
      services_btn_1: "Mehr erfahren",
      services_h3_2: "Ferienhaus",
      services_p_2: "Komfortable Ferienunterkünfte für Familien und Gruppen.",
      services_btn_2: "Mehr erfahren",
      services_h3_3: "Camping",
      services_p_3: "Saisoncamping vom 1. April bis 31. Oktober. Prachtige Campingplätze im Weerribben-Gebiet mit modernen Anlagen und atemberaubenden Aussichten.",
      services_btn_3: "Mehr erfahren",
      map_h2: "Unser Standort",
      footer_p: "Hier beginnt dein Abenteuer in den wunderschönen Weerribben!",
      footer_bottom: "&copy; 2026 <a href=\"https://ultimAItech.com\" target=\"_blank\" rel=\"noopener noreferrer\">ultimAItech</a>. Alle Rechte vorbehalten.",
      footer_company_name: "Nijenhuis Bootsverleih",
      footer_company_location: "Camping",
      footer_company_address: "Veneweg 199",
      footer_company_postal: "7946 LP Wanneperveen",
      footer_company_phone: "Tel: 0522 281 528",
      footer_company_kvk: "Kvk: 6769 7097",
      footer_company_btw: "Btw nr: NL857 1361 48 B01",
      footer_rights: "&copy; 2026 <a href=\"https://ultimAItech.com\" target=\"_blank\" rel=\"noopener noreferrer\">ultimAItech</a>. Alle Rechte vorbehalten.",
      alt_electrosloop: "Electrosloep mieten Giethoorn Weerribben",
      alt_zeilpunter: "Zeilpunter mieten Weerribben",
      alt_kano: "Kano mieten Weerribben Overijssel",
      alt_sup: "SUP mieten Giethoorn",
      alt_kajak: "Kajak mieten Weerribben",
      alt_camping_banner: "Saisoncamping Nijenhuis am Wasser im Nationalpark Weerribben-Wieden bei Giethoorn",
      alt_house_interior: "Ferienhaus Belterwiede Innenansicht - Ferienwohnung bei Giethoorn",
      alt_logo: "Nijenhuis Bootsverleih",
      /* boats page */
      boats_header_h1: "Boot und Sloep mieten in den Weerribben bei Giethoorn",
      boats_header_p: "Steig ein und entdecke das Weerribben-Naturschutzgebiet. Bootsverleih Weerribben, Kano mieten, SUP Verleih Overijssel.",
      boats_h2: "Unsere Boote",
      boats_intro: "Wir bieten eine große Auswahl an Booten für alle Vorlieben und Erfahrungsstufen",
      fleet_h2: "Unsere Flotte",
      fleet_p: "Wähle aus unserem großen Angebot an elektrischen Schaluppen, Segelbooten und Kanus",
      fleet_hourly_note: "ℹ️ Hinweis: Für alle Boote ist es auch möglich, stundenweise statt tageweise zu mieten. Stundenmiete kann nur direkt vor Ort bei der Bootsvermietung gebucht werden, nicht online oder telefonisch. Besuche unsere Vermietungsstelle für Verfügbarkeit und direkte Buchung.",
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
      prices_intro_2: "Kautionen sind abhängig vom Bootstyp und müssen bar bezahlt werden.",
      prices_table_title: "Tagespreise Bootsverleih",
      prices_season: "Saison: 1. April – 31. Oktober 2026",
      prices_th_type: "Bootstyp",
      prices_th_capacity: "Kapazität",
      prices_th_price: "Preis pro Tag",
      prices_th_deposit: "Kaution",
      multi_day_note: "Boote können auch für mehrere Tage gemietet werden. Kontaktiere uns für Infos und Tarife.",
      boats_cta_h2: "Buche jetzt dein Boot",
      boats_cta_p: "Bereit, die Weerribben zu entdecken? Reserviere noch heute dein Boot und genieße einen unvergesslichen Tag auf dem Wasser bei Giethoorn.",
      boats_cta_btn: "Jetzt buchen",
      boats_cta_phone: "📞 Ruf uns an",
      btn_call: "Jetzt anrufen",
      rentinfo_h2: "Mietinfo",
      rentinfo_intro: "Alles, was du über das Mieten wissen musst",
      rentinfo_book_title: "📅 Reservierungen",
      rentinfo_book_desc: "Reservierungen per Telefon oder online möglich. Wir empfehlen Vorab-Buchung, besonders in der Hochsaison.",
      rentinfo_open_title: "⏰ Öffnungszeiten",
      rentinfo_open_desc: "Täglich 09:00–18:00 Uhr während der Saison (1. April – 31. Oktober).",
      rentinfo_pay_title: "💰 Zahlung",
      rentinfo_pay_desc: "Barzahlung und Kartenzahlung möglich. Kaution €50–€100 je nach Bootstyp. Sieh Preistabelle für Details.",
      /* booking page */
      booking_title: "Boot buchen - Reservierung bei Nijenhuis Bootsverleih",
      booking_subtitle: "Dein Boot ist verfügbar! Fülle deine Daten aus, um deine Reservierung zu bestätigen.",
      booking_details_title: "Buchungsdetails",
      booking_date_label: "Datum wählen *",
      booking_days_label: "Anzahl Tage *",
      booking_select_duration: "-- Dauer wählen --",
      booking_end_date_label: "Enddatum *",
      booking_1_day: "1 Tag",
      booking_2_days: "2 Tage",
      booking_3_days: "3 Tage",
      booking_4_days: "4 Tage",
      booking_5_days: "5 Tage",
      booking_6_days: "6 Tage",
      booking_7_days: "7 Tage",
      booking_boat_label: "Boot wählen *",
      booking_quantity_label: "Anzahl Boote *",
      booking_select_boat: "-- Boot wählen --",
      booking_total_price: "Gesamtpreis:",
      booking_summary_title: "Buchungsübersicht",
      booking_summary_date: "Datum:",
      booking_summary_boat: "Bootstyp:",
      booking_summary_duration: "Dauer:",
      booking_summary_price: "Gesamtpreis:",
      booking_summary_status: "Status:",
      booking_status_select: "Bitte wähle die obigen Optionen",
      booking_status_available: "Verfügbar ✓",
      booking_your_info: "Deine Daten",
      booking_name_label: "Vollständiger Name *",
      booking_email_label: "E-Mail-Adresse *",
      booking_phone_label: "Telefonnummer *",
      booking_address_label: "Adresse (optional)",
      booking_notes_label: "Besondere Wünsche oder Anmerkungen",
      booking_notes_placeholder: "Besondere Anforderungen...",
      booking_confirm_btn: "Buchung bestätigen",
      booking_back_btn: "Zurück zur Startseite",
      booking_processing: "Deine Buchung wird bearbeitet...",
      /* Ferierhaus page */
      house_header_h1: "Ferienhaus Belterwiede bei Giethoorn und Weerribben",
      house_header_p1: "Erlebt einen herrlichen Aufenthalt in unserem Ferienhaus inmitten des wunderschönen Naturschutzgebietes Weerribben.",
      house_overview_h2: "Ferienhaus Belterwiede bei Giethoorn",
      house_overview_p1: "<strong>DAS GANZE JAHR GEÖFFNET</strong> – Unser Ferienhaus Belterwiede liegt bei Giethoorn im Herzen der Weerribben.",
      house_overview_h3: "Perfekte Basis in der Kop van Overijssel",
      house_overview_p2: "Möchtest du einen Wochenende, Mittwoch, eine Woche oder eine ganze Ferien in einem wunderschönen Natur- oder Wassersportgebiet verbringen? Dann komm nach der Kop van Overijssel, wo du Booten, Angeln, Schwimmen, Fahrradfahren, Wandern und Besuchen anderer Dörfer in der Umgebung genießen kannst. Das Haus ist direkt am Belterwijde-Meer gelegen.",
      house_overview_p3: "<strong>Untergeschoss:</strong> <span>Du hast 1 Schlafzimmer, ein Dusche, ein Toilette und eine Waschmaschine. Entspanne dich in der großen Wohnzimmer mit TV und Radio. Die Kammer hat eine offene Küche mit verschiedenen Haushaltsgeräten (Ofen, Mikrowelle, Kühlschrank). Es gibt eine große Halle und das Haus ist vollständig mit Zentralheizung ausgestattet.</span>",
      house_overview_p4: "<strong>Obergeschoss:</strong> <span>Du hast vier Schlafzimmer, von denen zwei ein Waschbecken haben. Es gibt auch eine Dusche und ein Toilette auf der zweiten Etage.</span>",
      house_overview_li1: "Babybett, Spielbett und hochbett verfügbar",
      house_overview_li2: "Kuscheln und Decken verfügbar",
      house_overview_li3: "Bitte bringe dein eigenes Bettbezug",
      house_overview_li4: "Bettbezug kann auch gemietet werden (bitte vorab melden)",
      house_overview_li5: "Für weitere Fragen kontaktiere uns",
      house_overview_h4: "Was bieten wir?",
      house_amenities_h1: "Einrichtungen",
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
      house_why_title: "Warum dieses Ferienhaus wählen?",
      house_why_p1: "Das Ferienhaus Belterwiede vereint das Beste aus zwei Welten: die Ruhe des Nationalparks Weerribben-Wieden und die Lebendigkeit von Giethoorn in der Nähe. Da das Haus direkt am Belterwijde-See liegt, steigst du buchstäblich aus dem Garten in dein Boot oder Kanu. Ideal für Familien, die segeln, angeln oder schwimmen möchten, ohne ständig ein- und ausladen zu müssen.",
      house_why_p2: "Das Haus ist das ganze Jahr über geöffnet, sodass du auch im Herbst und Winter Spaziergänge, Radtouren und die einzigartige Atmosphäre der Weerribben genießen kannst. Im Sommer ist es der perfekte Ausgangspunkt für Tagesausflüge nach Giethoorn, Belt-Schutsloot oder andere Dörfer in der Umgebung. Waterpark Belterwiede übernimmt die Reservierungen und Verwaltung des Ferienhauses.",
      house_surroundings_title: "Umgebung & Aktivitäten",
      house_surroundings_p1: "Vom Ferienhaus Belterwiede aus hast du direkten Zugang zum ausgedehnten Wassernetz der Weerribben. Segeln, Kanufahren oder Angeln – alles ist von deinem eigenen Steg aus möglich. Fahrrad- und Wanderwege führen durch die Region und verbinden dich mit malerischen Dörfern wie Giethoorn, Wanneperveen und Blokzijl.",
      house_surroundings_p2: "In der Umgebung findest du Restaurants, Geschäfte und Attraktionen. Giethoorn liegt etwa 15 Fahrminuten entfernt und ist für seine Grachten und Reetdachhäuser bekannt. Für Familien gibt es Spielplätze und Strände am Wasser. Das Ferienhaus bietet Platz für bis zu zwölf Personen und genug Raum für einen entspannten Aufenthalt.",
      house_contact_h2: "Kontakt & Reservierungen",
      house_contact_p1: "Für mehr Informationen und Reservierungen",
      house_contact_h3: "Waterpark Belterwiede",
      house_contact_p2: "E-mail: info@parkbelterwiede.nl",
      house_contact_p3: "Telefon: 0522-281828",
      /* te-koop page */
      te_koop_h1: "Chalets und Stellwagen zu verkaufen in den Weerribben",
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
      te_koop_intro_h2: "Chalets und Mobilheime in den Weerribben",
      te_koop_intro_p1: "Bei Camping Nijenhuis stehen regelmäßig Chalets und Mobilheime zum Verkauf auf einem festen Stellplatz im Nationalpark Weerribben-Wieden. Ein eigenes Chalet oder Mobilheim auf unserem Campingplatz bedeutet einen festen Platz am Wasser, direkten Zugang zu den Wasserwegen nach Giethoorn und den Weerribben sowie eine ruhige Umgebung, in der du das ganze Jahr über die Natur genießen kannst.",
      te_koop_intro_p2: "Käufer erhalten einen Saisonplatz mit allen Annehmlichkeiten: Wasser, Strom, Kanalisation und eigenem Bootsanleger. Wohnwagen und Chalets dürfen das ganze Jahr über auf dem Platz stehen bleiben. Aufgrund der überschaubaren Größe des Campingplatzes ist das Angebot begrenzt – neue Angebote werden auf dieser Seite veröffentlicht, sobald sie verfügbar sind.",
      te_koop_why_h2: "Warum bei Nijenhuis kaufen?",
      te_koop_why_p1: "Camping Nijenhuis ist ein Familienunternehmen mit über 50 Jahren Erfahrung in den Weerribben. Unser Campingplatz bietet eine einzigartige Lage direkt am Wasser mit eigenen Bootsanlegern und allen modernen Einrichtungen. Chalets und Mobilheime, die hier zum Verkauf stehen, haben einen bewährten Stellplatz in einem begehrten Naturgebiet. Interessiert? Nimm Kontakt auf für Verfügbarkeit, Preise und die Möglichkeit, dich für zukünftige Angebote auf die Warteliste setzen zu lassen.",

      /* camping page */

      camping_title: "Saisoncamping in den Weerribben bei Giethoorn",
      camping_description: "Kommt ganz zur Ruhe beim Zelten mitten im wunderschönen Naturschutzgebiet De Weerribben.",

      camping_season_title: "Saisoncamping",
      camping_season_dates: "Geöffnet vom 1. April bis 31. Oktober",
      camping_season_status: "Jetzt für Reservierungen geöffnet",

      camping_overview_title: "Unser Campingplatz",
      camping_overview_description: "Ein ruhiger und gemütlicher Campingplatz mitten in der Natur",
      camping_overview_seasonal_title: "Seit über 50 Jahren ein Familienbegriff",
      camping_overview_seasonal_description: "Seit mehr als einem halben Jahrhundert ist Camping Nijenhuis ein verborgenes Juwel im Herzen der Weerribben. Was vor mehr als fünfzig Jahren aus Leidenschaft für Gastfreundschaft und Natur begann, hat sich zu einem einzigartigen Familiencampingplatz entwickelt, auf dem sich Generationen von Gästen zu Hause fühlen. Immer noch in Familienhand pflegen wir die persönliche Atmosphäre und die Ruhe, die unseren Campingplatz so besonders machen. <br><br> Unser Saisoncamping ist klein angelegt, sodass du maximale Privatsphäre und Platz genießen kannst. Es ist der perfekte Ort, um dem Alltag zu entfliehen. Einzigartig an unserem Campingplatz ist, dass jeder Stellplatz über einen eigenen Bootsanleger verfügt, sodass du direkt von deinem Wohnwagen aus aufs Wasser kannst, um die wunderschönen Wasserwege von Giethoorn und den Weerribben zu erkunden.",
      camping_overview_seasonal_list_item_1: "Saisoncamping (1. April - 31. Oktober)",
      camping_overview_seasonal_list_item_2: "Wohnwagen können das ganze Jahr stehen bleiben",
      camping_overview_seasonal_list_item_3: "Wasseranschluss",
      camping_overview_seasonal_list_item_4: "Stromzähler",
      camping_overview_seasonal_list_item_5: "Zentralantenne",
      camping_overview_seasonal_list_item_6: "Kanalanschluss",
      camping_overview_seasonal_list_item_7: "Eigener Bootsanleger",
      camping_overview_seasonal_list_item_8: "Duschen und Toiletten verfügbar",
      camping_overview_seasonal_list_item_9: "Kleine, aber gemütliche Campingplatz",
      camping_overview_seasonal_list_item_10: "Wasmachine und Trockner verfügbar",
      camping_overview_cta_strong: "Interesse an einem Saisonplatz?",
      camping_overview_cta_button: "JETZT ANRUFEN",
      camping_area_title: "Umgebung & Freizeit",
      camping_area_p1: "Camping Nijenhuis liegt mitten im Nationalpark Weerribben-Wieden, einem der schönsten Naturgebiete der Niederlande. Von deinem Stellplatz aus kannst du direkt aufs Wasser – kein Gedränge mit Anhängern oder Schleppen. Die Wasserwege verbinden dich mit Giethoorn, Wanneperveen, Belt-Schutsloot und zahlreichen ruhigen Plätzen, an denen du nur die Vögel hörst.",
      camping_area_p2: "Neben Bootfahren kannst du Rad fahren, wandern, angeln und schwimmen. Es gibt ausgeschilderte Routen für jede Entfernung. In der Umgebung findest du Restaurants, Museen und Bootsverleihe. Viele Gäste kombinieren ihren Aufenthalt mit einem Boot oder Kanu von Nijenhuis Bootsverleih – frag bei der Buchung nach den Möglichkeiten.",
      camping_tips_title: "Praktische Informationen zur Saisoncamping",
      camping_tips_p1: "Der Campingplatz ist vom 1. April bis 31. Oktober geöffnet. Wohnwagen dürfen das ganze Jahr über auf dem Platz stehen bleiben, sodass du in der Vor- und Nachsaison stressfrei kommen und gehen kannst. Jeder Stellplatz hat Wasser, Strom (mit eigenem Zähler), Kanalanschluss und einen eigenen Bootsanleger. Sanitäranlagen mit Duschen und Toiletten sind vorhanden, ebenso eine Waschmaschine und ein Trockner.",
      camping_tips_p2: "Aufgrund der überschaubaren Größe und der beliebten Lage empfehlen wir, rechtzeitig zu buchen. Ruf uns für Verfügbarkeit und Preise an. Hunde sind willkommen, an der Leine auf dem Campingplatz. Die Atmosphäre ist ruhig und geeignet für Familien und Naturliebhaber, die Einfachheit und direkten Kontakt mit dem Wasser schätzen.",

      facilities_title: "Ausstattung",
      facilities_description: "Alle Einrichtungen für Saisonplätze",
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
      /* vaarkaart page */
      vaarkaart_title: "Wasserkarte Weerribben-Wieden - Routen und Fahrinformationen",
      vaarkaart_description: "Navigationsinformationen und Routen für das Naturschutzgebiet Weerribben",

      vaarkaart_interactive_map_title: "Weerribben-Wieden Bootskarte",
      vaarkaart_interactive_map_description: "Entdecke die schönsten Routen durch den Nationalpark Weerribben-Wieden. Diese Bootskarte zeigt alle Fahrtrouten im Gebiet.",
      vaarkaart_intro_extra: "Der Nationalpark Weerribben-Wieden ist das größte Niedermoorgebiet Nordwesteuropas. Das Gewässer besteht aus Seen, Gräben und Kanälen, die einst durch den Torfabbau entstanden. Heute ist es ein Paradies für Bootsfahrer mit ruhigen Routen, Schilfgürteln, Mooren und weiten Ausblicken. Von Nijenhuis Bootsverleih in Wanneperveen aus fährst du direkt ins Netz. Unten findest du die interaktive Karte, beliebte Routen und wichtige Fahrregeln.",
      vaarkaart_route_giethoorn_desc: "Die Route führt durch enge Gräben und breitere Kanäle zum Zentrum von Giethoorn. Unterwegs siehst du Reetdachhäuser, Brücken und typische Punter. In Giethoorn kannst du anlegen zum Spazierengehen oder Mittagessen. Plane mindestens 2–3 Stunden für eine entspannte Hin- und Rückfahrt ein.",
      vaarkaart_route_weerribben_desc: "Diese Route führt tiefer in den Park, vorbei an Mooren, Schilffeldern und offenem Wasser. Du kannst Eisvögel, Reiher, Libellen und verschiedene Wasservögel beobachten. Nimm ein Picknick mit und such einen ruhigen Platz am Ufer. Ein Elektrosloop oder Kanu ist ideal für diese Route.",
      vaarkaart_route_wanneperveen_desc: "Eine ideale Route für eine erste Bekanntschaft mit dem Gebiet oder wenn du wenig Zeit hast. Du fährst rund um Wanneperveen und genießt den Dorfblick und die umliegenden Gewässer. Geeignet für alle Bootstypen, einschließlich Kajaks und Kanus.",
      vaarkaart_interactive_map_map_title: "Naturschutzgebiet Weerribben - Interaktive Wasserkarte",
      vaarkaart_interactive_map_attribution_source: "Quelle:",
      vaarkaart_interactive_map_attribution_source_link: "Waterkaart.net",
      vaarkaart_interactive_map_attribution_suffix: "– Professionelle Bootskarten für niederländische Gewässer",
      vaarkaart_interactive_map_placeholder_title: "Interaktive Wasserkarte",
      vaarkaart_interactive_map_placeholder_description: "Für die aktuellsten und detailliertesten Wasserkarte des Weerribben-Gebiets besuche die professionelle Waterkaart der Niederlande.",
      vaarkaart_interactive_map_placeholder_button: "Öffne waterkaart.net",
      vaarkaart_interactive_map_footer_description: "Diese interaktive Wasserkarte wird von Waterkaart.net bereitgestellt. Für die aktuellsten Informationen und detaillierten Karten besuche deren Website.",
      vaarkaart_expand_map: "Karte vergrößern",
      vaarkaart_close_fullscreen: "Schließen",
      vaarkaart_view_osm: "OpenStreetMap",
      vaarkaart_disclaimer_title: "Haftungsausschluss:",
      vaarkaart_disclaimer_text: "Wir übernehmen keine Verantwortung für den Inhalt und die Richtigkeit dieser Karte. Lokale Gesetze, Regeln und Schilder entlang des Wassers müssen immer zuerst befolgt werden.",
      vaarkaart_footer_source: "Für detaillierte Wasserkarten und aktuelle Fahrinformationen besuche <a href='https://waterkaart.net/' target='_blank' rel='noopener noreferrer'>Waterkaart.net</a>.",

      giethoorn_title: "Giethoorn besuchen - Boot mieten im Venedig des Nordens",
      belt_schutsloot_title: "Belt-schutsloot - verborgenes Juwel bei Giethoorn und Weerribben",
      wanneperveen_title: "Wanneperveen - ruhiges Bootfahren in den Weerribben",
      wanneperveen_description: "Entdecke die schönsten Wasserstraßen der Weerribben ab Wanneperveen",

      vaarkaart_popular_routes_title: "Beliebte Routen",
      vaarkaart_popular_routes_description: "Entdecke die schönsten Bootsfahrten in der Umgebung",

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
      vaarkaart_navigation_rules_general_rules_respect_nature: "Respektiere die Natur",
      vaarkaart_navigation_rules_general_rules_distance_from_other_boats: "Halte Abstand von anderen Booten",

      vaarkaart_navigation_rules_safety_tips_title: "Sicherheitstipps",
      vaarkaart_navigation_rules_safety_tips_check_weather: "Überprüfe das Wetter vor der Abfahrt",
      vaarkaart_navigation_rules_safety_tips_bring_water: "Nimm ausreichend Wasser mit",
      vaarkaart_navigation_rules_safety_tips_charge_phone: "Stelle sicher, dass dein Telefon aufgeladen ist",
      vaarkaart_navigation_rules_safety_tips_know_rules: "Kenne die Fahrregeln",
      vaarkaart_navigation_rules_safety_tips_stay_on_navigable_routes: "Bleib auf schiffbaren Routen",

      vaarkaart_navigation_rules_emergency_numbers_title: "Notrufnummern",
      vaarkaart_navigation_rules_emergency_numbers_general_alarm: "Allgemeiner Notruf: 112",
      vaarkaart_navigation_rules_emergency_numbers_nijenhuis: "Nijenhuis Bootsverleih: 0522 281 528",
      vaarkaart_navigation_rules_emergency_numbers_water_police: "Wasserschutzpolizei: 0900-8844",
      vaarkaart_navigation_rules_emergency_numbers_weather_report: "Wetterbericht: 0900-9722",
      vaarkaart_navigation_rules_emergency_numbers_rescue_brigade: "Rettungsbrigade: 0900-0112",
      /* contact page */
      contact_title: "Kontakt und Anfahrt - Nijenhuis Wanneperveen",
      contact_p: "Kontaktiere uns für Fragen, Reservierungen oder weitere Informationen",

      contact_h2: "Kontakt & Route",
      contact_h2_p: "Kontaktiere Nijenhuis Bootsverleih in Wanneperveen. Hier findest du unsere Kontaktdaten und Wegbeschreibung.",
      contact_intro_extra: "Nijenhuis Bootsverleih liegt an der Veneweg 199 in Wanneperveen, am Rand des Nationalparks Weerribben-Wieden. Wir sind spezialisiert auf Bootsverleih – von Elektroslopen und Segelbooten bis zu Kajaks und SUP-Boards – und bieten außerdem Saisoncamping. Für Reservierungen, Fragen zu Preisen oder Verfügbarkeit kannst du uns anrufen oder während der Öffnungszeiten vorbeikommen. Es gibt kostenlose Parkmöglichkeiten vor Ort.",
      contact_route_h2: "Wegbeschreibung",
      contact_route_p1: "Wanneperveen liegt in der Kop van Overijssel, zwischen Meppel und Steenwijk. Kommst du mit dem Auto? Folge den Schildern nach Wanneperveen und such die Veneweg – wir sind unter Nummer 199, direkt am Wasser. Von Giethoorn aus sind es etwa 15 Fahrminuten. Es gibt kostenlose Parkplätze vor Ort. Öffentliche Verkehrsmittel: Buslinie 77 hält in der Nähe von Wanneperveen; für genaue Haltestellen konsultiere den Fahrplan.",
      contact_route_p2: "Während der Saison (1. April – 31. Oktober) sind wir täglich von 09:00 bis 18:00 Uhr geöffnet. Für Boote und Kanus empfehlen wir eine Vorausbuchung, besonders am Wochenende und in den Sommermonaten. Bei Ankunft kannst du direkt zu uns kommen für den Schlüssel, die Einweisung und die Routenkarte.",

      contact_h3: "Kontaktinformationen",

      contact_address_title: "Adresse",
      contact_address: "Veneweg 199",
      contact_zip: "7946 LP Wanneperveen",
      contact_country: "Niederlande",

      contact_phone_title: "Telefon",
      contact_phone: "0522 281 528",

      contact_opening_title: "Öffnungszeiten",
      contact_opening_p: "Täglich: 09:00 - 18:00 Uhr",
      contact_season_p: "Saison: 1. April - 31. Oktober",

      contact_business_title: "Firmendaten",
      contact_kvk: "Handelsregister: 6769 7097",
      contact_btw: "USt.-Nr.: NL857 1361 48 B01",

      contact_call_title: "Direkter Kontakt",
      contact_call_p: "Für Fragen, Reservierungen oder weitere Informationen ruf uns direkt an:",
      contact_call_button: "Jetzt Anrufen",
      contact_call_info_p: "Verfügbar: Täglich von 09:00 - 18:00 Uhr",
      contact_call_info_p2: "Saison: 1. April - 31. Oktober",

      contact_map_title: "Wo findest du uns?",
      contact_map_p: "Sieh unseren Standort auf der Karte",
      /* payment pages */
      payment_success_title: "Zahlung Erfolgreich!",
      payment_success_title_pay_on_arrival: "Reservierung bestätigt!",
      payment_success_subtitle_pay_on_arrival:
        "Deine Reservierung ist fixiert. Wir haben deine nicht erstattbare Reservierungsgebühr erhalten. Den Restbetrag zahlst du bei Ankunft (siehe Übersicht). Du erhältst in Kürze eine Bestätigungs-E-Mail.",
      payment_success_subtitle: "Deine Bootsbuchung wurde bestätigt. Du erhältst in Kürze eine Bestätigungs-E-Mail.",
      payment_success_processing: "Deine Zahlung wird bearbeitet...",
      payment_success_back: "Zurück zur Startseite",
      payment_success_contact: "Kontakt",
      payment_success_booking_id: "Buchungs-ID:",
      payment_success_date: "Datum:",
      payment_success_duration: "Dauer:",
      payment_success_boat_type: "Bootstyp:",
      payment_success_customer: "Kunde:",
      payment_success_status: "Status:",
      payment_success_price: "Preis:",
      payment_success_breakdown_rental: "Miete",
      payment_success_breakdown_fee: "Bearbeitungsgebühr",
      payment_success_breakdown_total: "Gesamt bezahlt",
      payment_success_breakdown_total_due: "Gesamt (bei Ankunft)",
      payment_success_breakdown_reservation_fee: "Reservierungsgebühr (bezahlt, nicht erstattbar)",
      payment_success_breakdown_reservation_rental_portion: "Davon: Mietanteil Reservierung",
      payment_success_breakdown_reservation_admin_slice: "Davon: Bearbeitungsgebühr auf diesen Anteil",
      payment_success_breakdown_balance_arrival: "Bei Ankunft fällig",
      payment_success_reference_label: "Buchungsnummer:",
      payment_success_arrival_title: "Ankunft",
      payment_success_arrival_location_label: "Ort",
      payment_success_arrival_time_label: "Ankunftszeit",
      payment_success_arrival_bring_label: "Mitbringen",
      payment_success_arrival_bring_text:
        "Sonnenschutz und bequeme Kleidung. Kaution bar bei Ankunft laut Buchung.",
      payment_success_price_via_mollie: "(über Mollie)",
      payment_success_price_pay_on_arrival: "(Zahlung bei Ankunft)",
      payment_success_download_pdf: "Als PDF herunterladen",
      payment_success_pdf_heading: "Buchungsbestätigung - Nijenhuis Bootsverleih",
      payment_success_pdf_date_generated: "Erstellt am:",
      payment_success_pdf_unavailable: "PDF-Download nicht verfügbar. Seite neu laden oder Kontakt aufnehmen.",
      payment_success_pdf_deposit_heading: "Kaution bei Ankunft",
      payment_success_pdf_wordmark: "NIJENHUIS",
      payment_success_pdf_wordmark_sub: "Bootsverleih",
      payment_success_pdf_hero_date_label: "Datum der Fahrt",
      payment_success_pdf_hero_total_label: "Gesamtbetrag",
      payment_success_pdf_hero_total_note: "Miete inkl. MwSt., bezahlt via Mollie",
      payment_success_pdf_hero_total_note_poa:
        "Gesamtmiete und Bearbeitungsgebühr bei Ankunft. Reservierungsanteil online bezahlt (nicht erstattbar).",
      payment_success_pdf_price_breakdown_title: "Preisübersicht",
      payment_success_pdf_poa_paid_at_reservation: "Bei Reservierung bezahlt",
      payment_success_pdf_poa_huurdeel: "Mietanteil",
      payment_success_pdf_poa_total_paid_nonrefund: "Gesamt bezahlt (nicht erstattbar)",
      payment_success_pdf_poa_total_arrival: "Gesamt bei Ankunft",
      payment_success_pdf_poa_including_deposit: "Inkl. Kaution",
      payment_success_pdf_total_cash_arrival: "Gesamt bar bei Ankunft (Miete + Kaution)",
      payment_success_pdf_col_cancellation: "Stornierung",
      payment_success_pdf_col_bring: "Was mitbringen",
      payment_success_pdf_col_practical: "Kontakt & Anfahrt",
      payment_success_pdf_checkin_label: "Ankunft / Check-in",
      payment_success_pdf_footer_wish:
        "Wir wunschen Ihnen eine gute Fahrt und einen schonen Tag auf dem Wasser in den Weerribben!",
      payment_failure_title: "Zahlung Fehlgeschlagen",
      payment_failure_subtitle: "Leider konnte deine Zahlung nicht verarbeitet werden. Bitte versuche es erneut oder kontaktiere uns.",
      payment_failure_try_again: "Erneut versuchen",
      payment_failure_back: "Zurück zur Startseite",
      payment_failure_help_title: "Benötigst du Hilfe?",
      payment_failure_help_intro: "Wenn du weiterhin Probleme mit der Zahlung hast:",
      payment_failure_help_1: "Überprüfe, ob deine Zahlungsdaten korrekt sind",
      payment_failure_help_2: "Stelle sicher, dass du ausreichend Guthaben hast",
      payment_failure_help_3: "Versuche eine andere Zahlungsart",
      payment_failure_help_4: "Kontaktiere uns direkt",
      /* checkout page */
      checkout_title: "Bezahlen",
      checkout_empty_cart_title: "Dein Warenkorb ist leer",
      checkout_empty_cart_desc: "Füge Boote zu deinem Warenkorb hinzu, um zur Kasse zu gehen.",
      checkout_empty_cart_btn: "Zum Bootsverleih",
      checkout_reservations_title: "Deine Reservierungen",
      checkout_subtotal: "Miete (Zwischensumme)",
      checkout_admin_fee_label: "Bearbeitungsgebühr ({percent}%)",
      checkout_poa_rental_label: "Mietpreis",
      checkout_poa_admin_slice_label:
        "Bearbeitungsgebühr ({admin_percent}% auf {res_percent}% Reservierungsanteil)",
      checkout_total: "Gesamtbetrag:",
      checkout_total_trip: "Gesamt Buchung (Miete + Bearbeitungsgebühr):",
      checkout_poa_row_pay_online: "Jetzt online zahlen",
      checkout_poa_row_on_arrival: "Bei Ankunft zahlen",
      checkout_deposit_note: "Hinweis: Für die gemieteten Boote ist eine Kaution von €{amount} bei Ankunft in bar zu hinterlegen.",
      checkout_your_details: "Deine Daten",
      checkout_name_label: "Vollständiger Name *",
      checkout_email_label: "E-Mail-Adresse *",
      checkout_phone_label: "Telefonnummer *",
      checkout_address_label: "Adresse (optional)",
      checkout_notes_label: "Anmerkungen (optional)",
      checkout_notes_placeholder: "Besondere Wünsche...",
      checkout_back_btn: "Zurück",
      checkout_pay_btn: "Bezahlen",
      checkout_loading: "Deine Zahlung wird vorbereitet...",
      checkout_error_fields: "Bitte fülle alle Pflichtfelder aus.",
      checkout_error_email: "Bitte gib eine gültige E-Mail-Adresse ein.",
      checkout_error_general: "Ein Fehler ist aufgetreten. Bitte versuche es erneut.",
      checkout_day: "Tag",
      checkout_days: "Tage",

      /* botenverhuur page – SEO blocks (missing keys) */
      boats_header_p: "Boot und Sloep mieten in den Weerribben bei Giethoorn — entdecke den Nationalpark Weerribben-Wieden vom Wasser aus.",
      boats_bluf_summary: "Boot mieten in den Weerribben? Bei Nijenhuis Bootsverleih in Wanneperveen erlebst du die Ruhe und Weite des Nationalparks Weerribben-Wieden vom Wasser aus. Seit über 50 Jahren das Familienunternehmen für Familien, Paare und Freundesgruppen.",
      boats_intro_title: "Entdecke die Weerribben auf deine Art",
      boats_intro_text: "<p>Boot mieten in den Weerribben? Bei Nijenhuis Bootsverleih in Wanneperveen erlebst du die Ruhe und Weite des Nationalparks Weerribben-Wieden vom Wasser aus. Seit über 50 Jahren sind wir das Familienunternehmen, zu dem Familien, Paare und Freundesgruppen für einen unvergesslichen Tag auf dem Wasser zurückkehren.</p><p>Unser Standort an der Veneweg 199 in Wanneperveen liegt direkt am Wasser — nur 10 Kilometer von Giethoorn entfernt. Das bedeutet: keine Menschenmassen beim Ablegen, kostenloses Parken direkt vor der Tür und direkter Zugang zu den schönsten Routen durch das größte Niedermoor Nordwesteuropas.</p><p>Ob du eine flüsterleise Elektro-Sloep für die ganze Familie, ein sportliches Kanu für zwei oder eine traditionelle Segelpunter wählst — bei uns findest du das perfekte Boot für jeden Anlass. Das Beste: du brauchst keinen Bootsführerschein. Vor der Abfahrt geben wir dir eine klare Einweisung und eine detaillierte Routenkarte.</p>",
      boats_fleet_title: "Unsere Flotte: 25+ Boote für jede Gruppe",
      boats_fleet_electric_badge: "Am beliebtesten",
      boats_fleet_electric_title: "Luxus-Elektro-Sloopen (Flüsterboote)",
      boats_fleet_electric_desc: "Unsere Elektro-Sloopen sind absolute Favoriten bei unseren Gästen. Diese Flüsterboote sind mit einem leisen Elektromotor ausgestattet, der dich geräuschlos durch die Weerribben führt. Kein Motorenlärm, keine Abgase — nur plätscherndes Wasser, singende Vögel und raschelnde Schilf. Alle Sloopen haben bequeme Kissen, ein Steuerrad (kein Pinne!) und eine Reichweite von 8 bis 10 Stunden.",
      boats_fleet_sail_badge: "Traditionell und sportlich",
      boats_fleet_sail_title: "Segelboote & Punters",
      boats_fleet_sail_desc: "Die Punter ist untrennbar mit den Weerribben verbunden. Erlebe die Geschichte selbst und miete eine traditionelle Segelpunter, die nur mit Wind und Segel fährt. Für mehr Stabilität und Komfort haben wir Randmeer-Segelboote. Kein Wind? Kein Problem — Segelboote können optional mit einem Außenbordmotor ausgerüstet werden.",
      boats_fleet_active_badge: "Aktiv auf dem Wasser",
      boats_fleet_active_title: "Kanu, Kajak & SUP",
      boats_fleet_active_desc: "Willst du die Weerribben aus nächster Nähe erleben? Mit einem Kanu oder Kajak kommst du an Orte, wo kein Motorboot hinkommt. Paddel durch enge Gräben, entdecke verborgene Wasserlöcher und beobachte besondere Vögel vom Wasser aus.",
      boats_card_reserve: "Reservieren",
      boats_price_from: "Ab",
      boats_price_per_day: "/ Tag",
      boats_card_title_classic_tender_720: "Classic Tender 720",
      boats_card_specs_classic_tender_720: "<li>Geeignet für bis zu 12 Personen</li><li>Geräumige Aufteilung mit Sitzecke und Tisch</li><li>Reichweite: 8–10 Stunden mit einer Ladung</li>",
      boats_card_title_classic_tender_570: "Classic Tender 570",
      boats_card_specs_classic_tender_570: "<li>Geeignet für bis zu 8 Personen</li><li>Ideal für Familien und kleine Gruppen</li><li>Reichweite: 8–10 Stunden mit einer Ladung</li>",
      boats_card_title_electrosloop_10: "Elektro-Sloep 10-Personen",
      boats_card_specs_electrosloop_10: "<li>Extra Platz für größere Gruppen</li><li>Mit Badeleiter ausgestattet</li>",
      boats_card_title_electrosloop_8: "Elektro-Sloep 8-Personen",
      boats_card_specs_electrosloop_8: "<li>Kompakt und einfach zu steuern</li><li>Perfekt für einen Tagesausflug mit Freunden</li>",
      boats_card_title_electroboat_5: "Elektroboot 5-Personen",
      boats_card_specs_electroboat_5: "<li>Unser kleinstes Elektroboot</li><li>Ideal für Paare oder eine kleine Familie</li>",
      boats_card_title_sailboat_4_5: "Segelboot 't Waar (4–5 Personen)",
      boats_card_specs_sailboat_4_5: "<li>Stabiles Randmeer-Segelboot</li><li>Optional mit Außenbordmotor (€85/Tag)</li>",
      boats_card_title_sailpunter_3_4: "Segelpunter (3–4 Personen)",
      boats_card_specs_sailpunter_3_4: "<li>Traditionelles Holzboot</li><li>Nur Wind und Segel — das authentische Erlebnis</li>",
      boats_card_title_canoe_3: "Kanu (3 Personen)",
      boats_card_specs_canoe_3: "<li>Stabiles Kanadisches Kanu</li><li>Inklusive Paddel und Fahrkarte</li>",
      boats_card_title_kayak_2: "Kajak 2-Personen",
      boats_card_specs_kayak_2: "<li>Sportlich und wendig</li><li>Inklusive Paddel</li>",
      boats_card_title_kayak_1: "Kajak 1-Person",
      boats_card_specs_kayak_1: "<li>Solo-Abenteuer auf dem Wasser</li><li>Inklusive Paddel</li>",
      boats_card_title_sup_board: "SUP-Board",
      boats_card_specs_sup_board: "<li>Stand-up-Paddeln in der Natur</li><li>Inklusive Paddel</li>",
      boats_why_title: "Warum Nijenhuis Bootsverleih wählen?",
      boats_why_water_title: "Direkt am Wasser",
      boats_why_water_desc: "Unser Standort liegt buchstäblich am Wasser. Steig aus dem Auto, geh zum Steg und fahre los. Kein Trailer-Gefummel, keine Warteschlangen.",
      boats_why_parking_title: "Kostenloses Parken",
      boats_why_parking_desc: "Bei uns parkst du immer kostenlos, direkt am Verleih. In Giethoorn selbst zahlst du schnell €10–15 fürs Parken.",
      boats_why_quiet_title: "Ruhe statt Trubel",
      boats_why_quiet_desc: "Giethoorn ist wunderschön, aber in der Hochsaison auch voll. Wenn du von Wanneperveen ablegst, vermeidest du Andrang beim Einsteigen und genießt sofort die Ruhe auf dem Wasser. Nach 15–20 Minuten Fahrt bist du in Giethoorn.",
      boats_why_service_title: "Persönlicher Service",
      boats_why_service_desc: "Als Familienbetrieb kennen wir jedes Boot und jede Route. Wir nehmen uns Zeit für eine ausführliche Einweisung und geben Tipps für die schönsten Orte, die nicht im Reiseführer stehen.",
      boats_why_flexible_title: "Flexibel mieten",
      boats_why_flexible_desc: "<ul class=\"anchor-list\"><li><strong>Pro Tag:</strong> online oder telefonisch reservieren</li><li><strong>Pro Stunde:</strong> nur vor Ort, für spontane Besucher</li><li><strong>Bar und Karte</strong> werden akzeptiert</li></ul>",
      boats_routes_title: "Beliebte Routen ab Wanneperveen",
      boats_route_1_title: "Route 1: Nach Giethoorn",
      boats_route_1_meta: "8–10 km, 1,5–2 Stunden einfache Fahrt",
      boats_route_1_desc: "Fahre durch die Kanäle zum berühmten Giethoorn. Bewundere Reetdachhöfe, charakteristische Brücken und das malerische Dorfzentrum. Ideal als Tagesausflug.",
      boats_route_2_title: "Route 2: Belt-Schutsloot",
      boats_route_2_meta: "6–8 km, 1–1,5 Stunden",
      boats_route_2_desc: "Das \"verborgene Giethoorn\" — der gleiche Charme ohne touristischen Trubel. Authentische Brücken, historische Höfe und rustikale Atmosphäre.",
      boats_route_3_title: "Route 3: Weerribben Natur",
      boats_route_3_meta: "15 km, 3–4 Stunden",
      boats_route_3_desc: "Tief in den Nationalpark. Paddel oder fahre durch enge Gräben, entdecke Wasserlöcher und beobachte besondere Flora und Fauna. Ideal mit Kanu oder Kajak.",
      boats_route_4_title: "Route 4: Beulakerwijde",
      boats_route_4_meta: "10 km, 2–3 Stunden",
      boats_route_4_desc: "Der große See südlich von Wanneperveen. Offenes Wasser, wunderschöne Ausblicke und perfekt zum Segeln.",
      boats_routes_map_link: "Sieh dir unsere interaktive Fahrkarte für detaillierte Routen an →",
      boats_route_cta: "Auf Fahrkarte ansehen →",
      boats_faq_q1: "Brauche ich einen Bootsführerschein?",
      boats_faq_a1: "Nein, für keines unserer Boote brauchst du einen Führerschein. Alle Boote sind kürzer als 15 Meter und fahren langsamer als 20 km/h. Vor der Abfahrt erhältst du eine persönliche Einweisung.",
      boats_faq_q2: "Wie weit kann ich mit einem Elektroboot fahren?",
      boats_faq_a2: "Die Akkus halten bei normalem Gebrauch einen ganzen Tag. Die Route nach Giethoorn und zurück (±20 km) ist kein Problem. Bei der Ankunft prüfen wir immer, ob der Akku vollständig geladen ist.",
      boats_faq_q3: "Was kostet Boot mieten bei Nijenhuis?",
      boats_faq_a3: "Preise ab €20 pro Tag für ein Kanu oder Kajak. Elektro-Sloopen gibt es ab €95 pro halben Tag. Die vollständige Preisliste findest du auf unserer Buchungsseite.",
      boats_faq_q4: "Kann ich ein Boot für 12 Personen mieten?",
      boats_faq_a4: "Ja, unsere Classic Tender 720 ist für maximal 12 Personen geeignet. Für optimalen Komfort empfehlen wir 10 Personen. Bei größeren Gruppen kannst du auch zwei Boote nebeneinander buchen.",
      boats_faq_q5: "Darf ich meinen Hund mitbringen?",
      boats_faq_a5: "Haustiere sind auf den Elektro-Sloopen, Kanus, dem Segelpunter und dem Elektroboot erlaubt. Auf den Classic Tenders sind Haustiere nicht erlaubt.",
      boats_faq_q6: "Was ist bei schlechtem Wetter?",
      boats_faq_a6: "Bei leichtem Regen kannst du normal fahren — nimm Regenkleidung mit. Bei Gewitter oder Sturm raten wir, nicht aufs Wasser zu gehen. Bei extremen Bedingungen kannst du kostenlos auf ein anderes Datum umbuchen.",
      boats_faq_q7: "Wann kann ich abfahren?",
      boats_faq_a7: "Du kannst ab 9:00 Uhr morgens abfahren. Die letzten Mietzeiten hängen von der Saison ab. Im Sommer kannst du bis 18:00 Uhr ein Boot für eine Abendfahrt abholen.",
      boats_faq_q8: "Gibt es Parkplätze?",
      boats_faq_a8: "Ja, beim Nijenhuis Botenverhuur in Wanneperveen ist das Parken völlig kostenlos. Du parkst direkt neben dem Steg und kannst sofort aufs Wasser.",
      boats_faq_all_link: "Alle häufig gestellten Fragen ansehen →",
      boats_fishing_title: "Angeln von unseren Booten in den Weerribben",
      boats_fishing_p1: "Entdecke, warum die Weerribben ein Anglerparadies in Overijssel sind. Obwohl wir keine spezialisierten Angelboote mit Zelten oder Karpfenboote vermieten, eignen sich unsere geräumigen Elektro-Sloopen und Kanus ideal für einen Angeltag.",
      boats_fishing_p2: "Die leisen Elektromotoren stören Karpfen und Raubfische nicht, und mit einem Kajakverleih in den Weerribben erreichst du die ruhigsten Angelplätze, wo Motorbooten nicht hinkommen. Nimm deine Angel mit für einen Angelurlaub in Wanneperveen – <a href=\"/vaarkaart\">sieh dir unsere Fahrkarte an</a> für die besten Angelplätze auf der Belterwiede!",
      boats_cta_h2: "Buche jetzt dein Boot",
      boats_cta_p: "Bereit, die Weerribben zu entdecken? Reserviere noch heute dein Boot und genieße einen unvergesslichen Tag auf dem Wasser bei Giethoorn.",
      boats_cta_details: "<ul class=\"boats-cta-list anchor-list\"><li><strong>Online buchen:</strong> nutze das Reservierungsformular oben auf dieser Seite</li><li><strong>Anrufen:</strong> <a href=\"tel:0522281528\">0522 281 528</a></li><li><strong>Besuche uns:</strong> Veneweg 199, 7946 LP Wanneperveen</li></ul>",
      boats_cta_hours: "Geöffnet vom 1. April bis 31. Oktober, täglich 09:00–18:00 Uhr. Kein Bootsführerschein nötig. Bar und Karte werden akzeptiert.",
      boats_cta_btn: "Jetzt buchen",
      boats_cta_phone: "📞 Ruf uns an",

      /* booking page – missing keys */
      booking_options_title: "Zusatzoptionen",
      booking_option_motor: "Motor dazumieten?",

      /* checkout page – missing keys */
      checkout_home_btn: "🏠 Zur Website",
      checkout_policy_title: "Wichtige Informationen",
      checkout_policy_cancellation:
        "Bei Stornierung wird eine Stornogebühr von 10% des Gesamtbetrags berechnet.",
      checkout_policy_cancellation_poa:
        "Der online bezahlte Reservierungsanteil ist nicht erstattbar.",
      checkout_policy_contact: "Für Änderungen kontaktiere uns bitte telefonisch unter +31 522 281 528.",
      checkout_policy_location: "Unser Standort: Veneweg 199, 7946 LP Wanneperveen",
      checkout_secure_title: "Sicher bezahlen",
      checkout_step_details: "Daten",
      checkout_step_payment: "Zahlung",
      checkout_step_confirm: "Bestätigung",
      checkout_booking_summary: "Buchungsübersicht",
      checkout_payment_info_title: "Zahlungsmethode",
      checkout_payment_info_body:
        "Wähle unten, wie du zahlen möchtest. Anschließend geht es weiter zur sicheren Zahlungsseite von Mollie.",
      checkout_method_ideal: "iDEAL",
      checkout_method_bancontact: "Bancontact",
      checkout_method_applepay: "Apple Pay",
      checkout_method_googlepay: "Google Pay",
      checkout_wallet_divider: "Oder bezahlen mit",
      checkout_method_pay_on_arrival: "Zahlung bei Ankunft",
      checkout_pay_on_arrival_inline: "Späteste Abholung 11:00 Uhr.",
      checkout_poa_fee_explain:
        "Übersicht: voller Mietpreis; Bearbeitungsgebühr = nur {admin_percent}% auf den {percent}%-Reservierungsanteil (nicht {admin_percent}% auf die gesamte Miete). Online zahlst du diesen Reservierungsanteil plus diese Gebühr; bei Ankunft den Rest der Miete.",
      checkout_poa_fee_explain_no_admin_fee:
        "Du zahlst online {percent}% der Miete als Reservierung; den Rest bei Ankunft.",
      checkout_poa_pay_now_line:
        "Jetzt online zahlen: €{reservation}\nBei Ankunft fällig: €{balance}\n(Reservierungsanteil: {percent}% der Miete; Bearbeitungsgebühr nur auf diesen Anteil.)",
      checkout_error_pay_on_arrival_time:
        "Zahlung bei Ankunft ist nur mit Ankunftszeit bis 11:00 Uhr möglich. Wähle eine frühere Zeit oder eine andere Zahlungsmethode.",
      checkout_trust_secure: "Sichere Zahlung über Mollie",
      checkout_trust_support: "Hilfe? Ruf uns an: +31 522 281 528",
      checkout_trust_policy: "Stornobedingungen siehe Kasten nebenan",
      checkout_qty_label: "Anzahl:",
      checkout_arrival_time_label: "Ankunftszeit *",
      checkout_city_label: "Wohnort *",

      /* global – cart sidebar */
      cart_title: "🛒 Warenkorb",
      cart_close_aria: "Schließen",
      admin_fee_disclosure_note:
        "Bei Online-Zahlung wird eine Bearbeitungsgebühr von {percent}% auf den Mietbetrag erhoben.",
      cart_empty: "Dein Warenkorb ist leer",
      cart_total_label: "Gesamt:",
      cart_checkout_btn: "Zur Kasse",
      cart_clear_btn: "Leeren",
      compare_max_pins: "Du kannst maximal 3 Boote vergleichen.",

      /* booking modal */
      booking_modal_checking_availability: "Verfügbarkeit wird geprüft...",
      booking_modal_end_date_label: "Enddatum (optional)",
      booking_modal_engine_option: "Mit Außenbordmotor (+ Aufpreis)",
      booking_modal_direct_checkout_btn: "💳 Direkt bezahlen",
      booking_modal_confirm_btn: "Buchung bestätigen",
      booking_modal_cancel_btn: "Abbrechen",
      booking_modal_success_title: "Buchung erfolgreich!",
      booking_modal_success_text: "Deine Buchung wurde bestätigt. Du erhältst in Kürze eine Bestätigungs-E-Mail.",
      booking_modal_booking_id_label: "Buchungs-ID:",
      booking_modal_error_title: "Fehler",
      booking_modal_error_default: "Beim Verarbeiten deiner Buchung ist ein Fehler aufgetreten.",
      booking_modal_retry_btn: "Erneut versuchen",

      /* home page – about block */
      home_about_title: "Über Nijenhuis Bootsverleih",
      home_about_tagline: "Seit über 50 Jahren dein zuverlässiger Partner für Wassersport in den Weerribben",

      /* camping page */
      camping_overview_cta_text: "Kontaktiere uns für Möglichkeiten und Verfügbarkeit.",
      season_status_open: "Jetzt geöffnet",
      season_status_closed_until: "Geschlossen bis 1. April",

      /* vakantiehuis page */
      house_visit_website_btn: "🌐 Waterpark Belterwiede besuchen",

      /* contact page */
      contact_success_title: "✅ Nachricht erfolgreich gesendet!",
      contact_success_message: "Danke für deine Nachricht. Wir melden uns so schnell wie möglich per E-Mail bei dir.",
      contact_success_sent_to: "Deine Nachricht wurde gesendet an: info@nijenhuis-botenverhuur.nl",

      /* checkout (inline JS strings) */
      checkout_confirm_remove_item: "Möchtest du diese Reservierung wirklich entfernen?",
      checkout_notification_removed: "Reservierung entfernt",
      checkout_notification_remove_error: "Fehler beim Entfernen",
      checkout_error_unavailable_boats:
        "Leider sind die folgenden Boote inzwischen nicht mehr verfügbar: {boats}. Entferne sie aus deinem Warenkorb und versuche es erneut.",
      checkout_error_payment_method: "Bitte wähle eine gültige Zahlungsmethode.",
      checkout_error_pay_on_arrival_time:
        "Zahlung bei Ankunft ist nur mit Ankunftszeit bis 11:00 Uhr möglich. Wähle eine frühere Zeit oder eine andere Zahlungsmethode.",

      /* payment failure (inline JS strings) */
      payment_failure_status_failed: "Zahlungsstatus: {status}. Die Zahlung ist fehlgeschlagen.",
      payment_failure_status_pending:
        "Zahlungsstatus: {status}. Deine Zahlung wird noch verarbeitet. Prüfe deine E-Mail für Updates.",

      /* FAQ page */
      faq_header_h1: "Häufige Fragen zum Boot mieten in den Weerribben",
      faq_header_p: "Alles, was du über das Boot-Mieten bei Nijenhuis wissen musst",
      faq_intro_expanded: "Auf dieser Seite findest du Antworten auf die häufigsten Fragen zum Bootsverleih bei Nijenhuis Bootsverleih in den Weerribben. Themen: Preise pro Bootstyp, ob du einen Bootsführerschein brauchst, Öffnungszeiten und Reservierung, was im Mietpreis enthalten ist, ob du nach Giethoorn fahren darfst sowie praktische Dinge wie Zahlung und Haustiere. Ist deine Frage nicht dabei? Nimm gerne Kontakt mit uns auf – wir helfen dir weiter.",
      faq_contact_cta_p: "Ist deine Frage nicht dabei? Nimm gerne Kontakt mit uns auf.",
      faq_contact_cta_form: "Kontaktformular",
      faq_fleet_title: "Sieh dir unsere Flotte an",
      faq_fleet_subtitle: "Wähle den Bootstyp, der am besten zu deiner Gruppe und deinen Wünschen passt:",
      faq_fleet_cta: "Verfügbarkeit prüfen →",
      faq_page_html:
        "<div class='faq-intro' style='max-width: 800px; margin: 0 auto 2rem;'><p style='font-size: 1.1rem; line-height: 1.7; color: var(--text-secondary);'>Hier findest du Antworten auf die häufigsten Fragen rund um den Bootsverleih bei Nijenhuis. Ist deine Frage nicht dabei? Nimm gerne <a href='/contact'>Kontakt</a> auf oder ruf uns an unter <a href='tel:0522281528'>0522 281 528</a>.</p></div><div class='faq-list' style='max-width: 800px; margin: 0 auto;'><h2 style='margin-top: 2rem; color: var(--secondary-color);'>💰 Preise &amp; Zahlung</h2><div class='faq-item' style='margin-bottom: 1.5rem; padding: 1.5rem; background: #f8fafc; border-radius: 12px;'><h3 style='margin: 0 0 0.75rem; font-size: 1.15rem; color: var(--secondary-color);'>Was kostet es, ein Boot zu mieten?</h3><div class='faq-answer' style='line-height: 1.7;'><p>Die Preise variieren je nach Bootstyp:</p><ul id='faq-price-list' style='margin: 0.5rem 0; padding-left: 1.5rem;'></ul><p style='margin-top: 0.75rem;'>Bei mehreren Tagen bekommst du Rabatt. <a href='/botenverhuur'>Alle Preise ansehen →</a></p></div></div><h2 style='margin-top: 2.5rem; color: var(--secondary-color);'>📋 Praktische Infos</h2><div class='faq-item' style='margin-bottom: 1.5rem; padding: 1.5rem; background: #f8fafc; border-radius: 12px;'><h3 style='margin: 0 0 0.75rem; font-size: 1.15rem; color: var(--secondary-color);'>Brauche ich einen Führerschein?</h3><div class='faq-answer' style='line-height: 1.7;'><p><strong>Nein</strong>, ein Führerschein ist nicht erforderlich. Vor der Abfahrt bekommst du eine kurze Einweisung.</p></div></div></div>"
    },


    /* ---------- English ------------------------------------ */
    en: {
      nav_opening: "Opening hours: 9:00 am – 6:00 pm",
      nav_boats: "Boat Rental",
      nav_house: "Holiday Home",
      nav_forsale: "For Sale",
      nav_camping: "Camping",
      nav_chart: "Water Map",
      nav_blog: "Blog",
      nav_faq: "FAQ",
      nav_more: "More",
      nav_contact: "Contact",
      /* Boat Modal & Dynamic JS */
      boat_modal_description_title: "Description",
      boat_modal_features_title: "Features",
      boat_modal_rates_title: "Rates",
      boat_modal_capacity: "{n} persons capacity",
      capacity_short: "{n} pers.",
      feature_electric_motor: "Electric motor",
      feature_silent_eco: "Quiet and eco-friendly",
      feature_sailing: "Sailing",
      feature_traditional: "Traditional",
      feature_paddling: "Paddling",
      feature_sporty: "Sporty",
      feature_sup: "Stand-up paddleboard",
      feature_unique: "Unique experience",
      price_per_day: "€{price} per day",
      price_per_day_without_motor: "€{price} / day (without motor)",
      price_per_day_with_motor: "€{price} / day (with motor)",
      price_deposit: "Deposit: €{price}",
      price_deposit_none: "No deposit required",
      status_available: "Available",
      status_occupied: "Occupied",
      btn_more_info: "ℹ️ More Info",
      btn_reserve: "📅 Book Now",
      btn_close: "Close",
      rate_duration: "Duration",
      rate_price: "Price",
      duration_day_1: "1 day",
      duration_day_other: "{n} days",
      duration_week_1: "1 week",

      /* index.html */
      hero_book_h2: "Book directly",
      hero_book_p: "Simply book your boat for a day on the water",
      hero_book_date: "Datum",
      hero_book_boat_type: "Boot type",
      hero_book_boat_type_select: "Choose a boat",
      hero_book_boat_type_classic_tender_720: "Classic tender 720 10/12 pers",
      hero_book_boat_type_classic_tender_570: "Classic tender 570 8 pers",
      hero_book_boat_type_electrosloop_10: "Electrosloep for 10 pers",
      hero_book_boat_type_electrosloop_8: "Electrosloep for 8 pers",
      hero_book_boat_type_electroboat_5: "Electrosloep for 5 pers",
      hero_book_boat_type_sailboat_4_5: "Sailboat",
      hero_book_boat_type_sailpunter_3_4: "Sailpunter 3/4 pers",
      hero_book_boat_type_canoe_3: "Canadian canoe 3 pers",
      hero_book_boat_type_kayak_2: "Kayak 2 pers",
      hero_book_boat_type_kayak_1: "Kayak 1 pers",
      hero_book_boat_type_sup_board: "SUP board 1 pers",
      /* Boat descriptions and features */
      boat_classic_tender_720_name: "Classic tender 720",
      boat_classic_tender_720_description: "A luxurious electric tender for larger groups. Perfect for comfortable boat trips through the nature reserve.",
      boat_classic_tender_720_features: "10-12 person capacity, Electric motor, Luxurious and comfortable, Quiet and eco-friendly, Perfect for larger groups, Pets not allowed",

      boat_classic_tender_570_name: "Classic tender 570",
      boat_classic_tender_570_description: "An elegant electric tender for medium-sized groups. Ideal for relaxed boat trips.",
      boat_classic_tender_570_features: "8 person capacity, Electric motor, Elegant and comfortable, Quiet and eco-friendly, Perfect for families, Pets not allowed",

      boat_electrosloop_10_name: "Electrosloep for 10 pers",
      boat_electrosloop_10_description: "A spacious electric boat for larger groups. Perfect for sociable boat trips.",
      boat_electrosloop_10_features: "10 person capacity, Electric motor, Spacious and comfortable, Quiet and eco-friendly, Perfect for groups, Pets allowed",

      boat_electrosloop_8_name: "Electrosloep for 8 pers",
      boat_electrosloop_8_description: "A comfortable electric boat for families and groups of friends.",
      boat_electrosloop_8_features: "8 person capacity, Electric motor, Comfortable and stable, Quiet and eco-friendly, Perfect for families, Pets allowed",

      boat_electroboat_5_name: "Electrosloep for 5 pers",
      boat_electroboat_5_description: "A compact electric boat for small groups. Ideal for quiet boat trips.",
      boat_electroboat_5_features: "5 person capacity, Electric motor, Compact and manoeuvrable, Quiet and eco-friendly, Perfect for small groups, Pets allowed",

      boat_sailboat_name: "Sailboat",
      boat_sailboat_description: "A traditional sailboat available with or without motor. Without motor for experienced sailors, with motor for more flexibility.",
      boat_sailboat_features: "4-5 person capacity, Sailing without motor: €70, Sailing with motor: €85, Flexible, For all levels, Experienced sailor required, Pets not allowed",

      boat_sailpunter_name: "Sailpunter",
      boat_sailpunter_description: "A traditional sailpunter for the experienced sailor. Enjoy wind and nature.",
      boat_sailpunter_features: "3-4 person capacity, Sailing, Traditional, Sporty, For experienced sailors, Experienced sailor required, Pets allowed",

      boat_canoe_name: "Canadian canoe",
      boat_canoe_description: "A stable Canadian canoe for sporting activities and exploring smaller waterways.",
      boat_canoe_features: "3 person capacity, Paddling, Sporty, Stable, For all levels, Pets allowed",

      boat_kayak_2_name: "Kayak 2 pers",
      boat_kayak_2_description: "A tandem kayak for two people. Perfect for sporting activities.",
      boat_kayak_2_features: "2 person capacity, Paddling, Sporty, Manoeuvrable, For all levels, Pets not allowed",

      boat_kayak_1_name: "Kayak 1 pers",
      boat_kayak_1_description: "A solo kayak for individual boat trips. Ideal for sporting activities.",
      boat_kayak_1_features: "1 person capacity, Paddling, Sporty, Manoeuvrable, For all levels, Pets not allowed",

      boat_sup_name: "SUP board",
      boat_sup_description: "A stand-up paddleboard for a unique way to experience the water.",
      boat_sup_features: "1 person capacity, Paddling, Unique, Balancing, For all levels, Pets not allowed",

      hero_book_btn: "Book now",
      btn_outline: "📞 Call now!",
      btn_add_to_cart: "🛒 Add to Shopping cart",
      hero_book_badge: "100% safe &amp; free",
      hero_h1: "Boat Rental Weerribben | Nijenhuis",
      hero_h1_p: "Boat rental Weerribben: rent a boat, canoe hire Netherlands, electric sloop or SUP rental Overijssel. Boat hire from €20/day. No boating license required. Perfect for families, friends and nature lovers.",
      hero_btn: "Check availability",
      intro_h2: "Escape the daily grind with boat rental Weerribben",
      intro_h2_p: "In our busy world, everyone craves peace. Leave traffic, stress and the daily routine behind – discover National Park Weerribben-Wieden from the water at Nijenhuis Boat Rental in Wanneperveen, the Weerribben's boat rental.",
      intro_h2_p2: "Rent a boat for quality time with family or friends. Canoe hire Netherlands, SUP rental Overijssel – our whisper boats and electric sloops glide quietly through the narrowest canals, away from the crowds. Create unforgettable moments.",
      deposit_notice_cash: "<strong>Please note:</strong> The deposit must be paid in cash upon arrival.",
      intro_h3: "Why choose Nijenhuis?",
      intro_h3_li1: "📍 Located in the heart of the Weerribben nature reserve",
      intro_h3_li2: "🚤 Wide range of boats for all preferences",
      intro_h3_li3: "🌿 Availability of eco-friendly electric boats",
      intro_h3_li4: "👨‍👩‍👧‍👦 Perfect for families and groups",
      intro_h3_li5: "💰 Competitive prices for all budgets",
      intro_h3_li6: "📞 Personal service and support",
      intro_cta_p: "For more information, call 0522 - 281 528",
      intro_cta_p2: "Cash and pin payments accepted",

      /* About Grid */
      about_location_title: "Location",
      about_location_desc: "Wanneperveen, Overijssel<br><span class='fact-sub'>10 km from Giethoorn</span>",
      about_season_title: "Season",
      about_season_desc: "April 1 – October 31<br><span class='fact-sub'>Daily 09:00-18:00</span>",
      about_fleet_title: "Boats",
      about_fleet_desc: "25+ boats<br><span class='fact-sub'>1 to 12 persons</span>",
      about_prices_title: "Prices",
      about_prices_desc: "From €20/day<br><span class='fact-sub'>No boating license required</span>",

      index_season_title: "Seasonal Camping",
      index_season_dates: "Open from April 1st to October 31st",
      index_season_status: "Now open for reservations",

      index_camping_title: "Seasonal Camping in the Weerribben",
      index_camping_description: "Enjoy a unique camping experience in the heart of the beautiful Weerribben nature reserve. Our seasonal camping is open from April 1st to October 31st and offers a quiet, cozy environment for your caravan.",
      index_camping_feature_1_title: "Seasonal Camping",
      index_camping_feature_1_desc: "Open from April 1st to October 31st",
      index_camping_feature_2_title: "Caravans All Year",
      index_camping_feature_2_desc: "Caravans can stay all year round",
      index_camping_feature_3_title: "Modern Facilities",
      index_camping_feature_3_desc: "Water, electricity, sanitary facilities and own mooring",
      index_camping_feature_4_title: "Boat Rental Season",
      index_camping_feature_4_desc: "Boat rental only available during the season",
      index_camping_cta_text: "For more information about our seasonal camping",
      services_h2: "Our Services",
      services_h3_1: "Boat Rental",
      services_p_1: "Boat rental Weerribben, canoe hire Netherlands, SUP rental Overijssel. Electric sloops, canoes, kayaks for all ages.",
      services_btn_1: "Learn More",
      services_h3_2: "Holiday Home",
      services_p_2: "Comfortable holiday accommodation perfect for families and groups.",
      services_btn_2: "Learn More",
      services_h3_3: "Camping",
      services_p_3: "Seasonal camping from April 1st to October 31st. Beautiful camping sites in the Weerribben area with modern facilities and breathtaking views.",
      services_btn_3: "Learn More",
      map_h2: "Our Location",
      footer_p: "Your adventure in the beautiful Weerribben starts here!",
      footer_bottom: "&copy; 2026 <a href=\"https://ultimAItech.com\" target=\"_blank\" rel=\"noopener noreferrer\">ultimAItech</a>. All rights reserved.",
      footer_company_name: "Nijenhuis Boat Rental",
      footer_company_location: "Camping",
      footer_company_address: "Veneweg 199",
      footer_company_postal: "7946 LP Wanneperveen",
      footer_company_phone: "Tel: 0522 281 528",
      footer_company_kvk: "Kvk: 6769 7097",
      footer_company_btw: "Btw nr: NL857 1361 48 B01",
      footer_rights: "&copy; 2026 <a href=\"https://ultimAItech.com\" target=\"_blank\" rel=\"noopener noreferrer\">ultimAItech</a>. All rights reserved.",
      alt_electrosloop: "Electric sloop hire Giethoorn Weerribben",
      alt_zeilpunter: "Sail punter hire Weerribben",
      alt_kano: "Canoe hire Weerribben Overijssel",
      alt_sup: "SUP rental Giethoorn",
      alt_kajak: "Kayak hire Weerribben",
      alt_camping_banner: "Seasonal camping Nijenhuis by the water in National Park Weerribben-Wieden near Giethoorn",
      alt_house_interior: "Holiday house Belterwiede interior - holiday rental near Giethoorn",
      alt_logo: "Nijenhuis Boat Rental",
      /* boats page */
      boats_header_h1: "Boat and sloop hire in the Weerribben near Giethoorn",
      boats_header_p: "Hop on board and explore the stunning Weerribben area. Boat rental Weerribben, canoe hire Netherlands, SUP rental Overijssel.",
      boats_h2: "Our Boats",
      boats_intro: "We offer a wide range of boats for all preferences and experience levels",
      fleet_h2: "Our Fleet",
      fleet_p: "Choose from our wide range of electric sloops, sailboats and canoes",
      fleet_hourly_note: "ℹ️ Note: For all boats it is also possible to rent them on an hourly basis instead of daily. Hourly rental can only be booked directly at the boat rental location, not online or by phone. Visit our rental location for availability and direct booking.",
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
      prices_intro_2: "Deposits are dependent on the boat type and must be paid in cash.",
      prices_table_title: "Daily Boat-Rental Prices",
      prices_season: "Season: 1 April – 31 October 2026",
      prices_th_type: "Boat type",
      prices_th_capacity: "Capacity",
      prices_th_price: "Price per day",
      prices_th_deposit: "Deposit",
      multi_day_note: "Boats can be rented for multiple days. Contact us for more information and rates.",
      boats_cta_h2: "Book your boat now",
      boats_cta_p: "Ready to discover the Weerribben? Book your boat today and enjoy an unforgettable day on the water near Giethoorn.",
      boats_cta_btn: "Book now",
      boats_cta_phone: "📞 Call us",
      btn_call: "Call now",
      rentinfo_h2: "Rental Information",
      rentinfo_intro: "Everything you need to know about renting a boat",
      rentinfo_book_title: "📅 Reservations",
      rentinfo_book_desc: "Reservations can be made by phone or online. We recommend booking in advance, especially during high season.",
      rentinfo_open_title: "⏰ Opening hours",
      rentinfo_open_desc: "Open daily 09:00-18:00 during the season (1 April – 31 October).",
      rentinfo_pay_title: "💰 Payment",
      rentinfo_pay_desc: "Cash and card payments accepted. A €50–€100 deposit is required depending on boat type. See price table for specific deposits.",
      /* booking page */
      booking_title: "Book your boat - reservation at Nijenhuis Boat Rental",
      booking_subtitle: "Your boat is available! Please fill in your details to confirm your reservation.",
      booking_details_title: "Booking Details",
      booking_date_label: "Select Date *",
      booking_days_label: "Number of Days *",
      booking_select_duration: "-- Select duration --",
      booking_end_date_label: "End Date *",
      booking_1_day: "1 Day",
      booking_2_days: "2 Days",
      booking_3_days: "3 Days",
      booking_4_days: "4 Days",
      booking_5_days: "5 Days",
      booking_6_days: "6 Days",
      booking_7_days: "7 Days",
      booking_boat_label: "Select Boat *",
      booking_quantity_label: "Number of boats *",
      booking_select_boat: "-- Select a boat --",
      booking_total_price: "Total Price:",
      booking_summary_title: "Booking Summary",
      booking_summary_date: "Date:",
      booking_summary_boat: "Boat Type:",
      booking_summary_duration: "Duration:",
      booking_summary_price: "Total Price:",
      booking_summary_status: "Status:",
      booking_status_select: "Please select options above",
      booking_status_available: "Available ✓",
      booking_your_info: "Your Information",
      booking_name_label: "Full Name *",
      booking_email_label: "Email Address *",
      booking_phone_label: "Phone Number *",
      booking_address_label: "Address (Optional)",
      booking_notes_label: "Special Requests or Notes",
      booking_notes_placeholder: "Any special requirements...",
      booking_confirm_btn: "Confirm Booking",
      booking_back_btn: "Back to Home",
      booking_processing: "Processing your booking...",
      /* house page */
      house_header_h1: "Holiday home Belterwiede near Giethoorn and Weerribben",
      house_header_p1: "Enjoy a wonderful stay in our holiday home, right in the beautiful nature reserve De Weerribben.",
      house_overview_h2: "Holiday home Belterwiede near Giethoorn",
      house_overview_p1: "<strong>OPEN ALL YEAR ROUND</strong> – Our holiday home Belterwiede is located near Giethoorn in the heart of the Weerribben.",
      house_overview_h3: "Perfect base in the Kop van Overijssel",
      house_overview_p2: "Would you like to spend a weekend, midweek, a week, or a whole holiday in a beautiful nature or watersports area? Then come to the Kop van Overijssel, where you can enjoy boating, fishing, swimming, cycling, hiking, and visiting other villages nearby. The house is located directly on Lake Belterwijde.",
      house_overview_p3: "<strong>Downstairs:</strong> <span>You have 1 bedroom, a shower, a toilet, and a washing machine. Relax in the spacious living room with TV and radio. The room has an open kitchen with various household appliances (oven, microwave, fridge). There is a large hallway, and the house is fully equipped with central heating.</span>",
      house_overview_p4: "<strong>Upstairs:</strong> <span>You have four bedrooms, two of which have a washbasin. There is also a shower and toilet on the second floor.</span>",
      house_overview_li1: "Baby cot, playpen, and high chair available",
      house_overview_li2: "Pillows and duvets provided",
      house_overview_li3: "Please bring your own bed linen",
      house_overview_li4: "Bed linen can also be rented from us (please notify in advance)",
      house_overview_li5: "For further questions, you can contact us",
      house_overview_h4: "What do we offer?",
      house_amenities_h1: "Facilities",
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
      house_why_title: "Why choose this holiday home?",
      house_why_p1: "The holiday home Belterwiede offers the best of both worlds: the peace of National Park Weerribben-Wieden and the liveliness of Giethoorn nearby. Because the house is located directly on Lake Belterwijde, you literally step from the garden into your boat or canoe. Ideal for families who want to sail, fish, or swim without constantly loading and unloading.",
      house_why_p2: "The house is open year-round, so you can enjoy walks, cycling trips, and the unique atmosphere of the Weerribben in autumn and winter as well. In summer it's a perfect base for day trips to Giethoorn, Belt-Schutsloot, or other villages in the area. Waterpark Belterwiede handles reservations and management of the holiday home.",
      house_surroundings_title: "Surroundings & activities",
      house_surroundings_p1: "From the holiday home Belterwiede you have direct access to the extensive water network of the Weerribben. Sailing, canoeing, or fishing – it's all possible from your own jetty. Cycling and walking routes run through the area and connect you with picturesque villages like Giethoorn, Wanneperveen, and Blokzijl.",
      house_surroundings_p2: "In the surroundings you'll find restaurants, shops, and attractions. Giethoorn is about 15 minutes by car and is known for its canals and thatched houses. For families there are playgrounds and beaches by the water. The holiday home accommodates up to twelve people and offers plenty of space for a relaxed stay.",
      house_contact_h2: "Contact & Reservations",
      house_contact_p1: "For more information and bookings",
      house_contact_h3: "Waterpark Belterwiede",
      house_contact_p2: "Email: info@parkbelterwiede.nl",
      house_contact_p3: "Phone: 0522-281828",
      /* te-koop page */
      te_koop_h1: "Chalets and mobile homes for sale in the Weerribben",
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
      te_koop_intro_h2: "Chalets and mobile homes in the Weerribben",
      te_koop_intro_p1: "At Camping Nijenhuis, chalets and mobile homes regularly become available for sale on a fixed pitch in National Park Weerribben-Wieden. Owning a chalet or mobile home at our campsite means a permanent spot by the water, direct access to the navigation routes to Giethoorn and the Weerribben, and a quiet environment where you can enjoy nature all year round.",
      te_koop_intro_p2: "Buyers receive a seasonal pitch with all amenities: water, electricity, sewerage, and a private mooring. Caravans and chalets may stay on the pitch year-round. Due to the campsite's small scale, availability is limited – new listings are posted on this page as soon as they become available.",
      te_koop_why_h2: "Why buy at Nijenhuis?",
      te_koop_why_p1: "Camping Nijenhuis is a family business with over 50 years of experience in the Weerribben. Our campsite offers a unique location right by the water, with private moorings and all modern facilities. Chalets and mobile homes for sale here have a proven pitch in a sought-after nature area. Interested? Contact us for availability, prices, and the option to join the waiting list for future offerings.",

      /* camping page */
      camping_title: "Seasonal camping in the Weerribben near Giethoorn",
      camping_description: "Completely relax while camping in the beautiful nature reserve De Weerribben.",

      camping_season_title: "Seasonal Camping",
      camping_season_dates: "Open from April 1st to October 31st",
      camping_season_status: "Now open for reservations",

      camping_overview_title: "Our Campsite",
      camping_overview_description: "A quiet and cozy campsite in the heart of nature",
      camping_overview_permanent_description: "Our campsite offers only permanent annual pitches. It’s not a large site but very friendly. Each pitch has its own mooring.",
      camping_overview_seasonal_title: "A family tradition for over 50 years",
      camping_overview_seasonal_description: "For over half a century, Camping Nijenhuis has been a hidden gem in the heart of the Weerribben. What started more than fifty years ago as a passion for hospitality and nature has grown into a unique family campsite where generations of guests feel at home. Still family-owned, we cherish the personal atmosphere and tranquility that make our campsite so special.<br><br>Our seasonal campsite is small-scale, allowing you to enjoy maximum privacy and space. It is the perfect place to escape the daily hustle and bustle. Unique to our campsite is that every pitch has its own private mooring, so you can head straight onto the water from your caravan to explore the beautiful waterways of Giethoorn and the Weerribben.",
      camping_overview_seasonal_list_item_1: "Seasonal camping (April 1st - October 31st)",
      camping_overview_seasonal_list_item_2: "Caravans can stay all year round",
      camping_overview_seasonal_list_item_3: "Water connection",
      camping_overview_seasonal_list_item_4: "Electricity meter",
      camping_overview_seasonal_list_item_5: "Central antenna",
      camping_overview_seasonal_list_item_6: "Sewer connection",
      camping_overview_seasonal_list_item_7: "Own mooring",
      camping_overview_seasonal_list_item_8: "Showers and toilets available",
      camping_overview_seasonal_list_item_9: "Small, but cozy campsite",
      camping_overview_seasonal_list_item_10: "Washing machine and dryer available",
      camping_overview_cta_strong: "Interested in a seasonal pitch?",
      camping_overview_cta_button: "CALL NOW",
      camping_area_title: "Surroundings & recreation",
      camping_area_p1: "Camping Nijenhuis is located in the heart of National Park Weerribben-Wieden, one of the most beautiful nature reserves in the Netherlands. From your pitch you can head straight onto the water – no hassle with trailers or towing. The waterways connect you with Giethoorn, Wanneperveen, Belt-Schutsloot, and countless quiet spots where you'll only hear the birds.",
      camping_area_p2: "Besides boating you can cycle, walk, fish, and swim. There are marked routes for every distance. In the area you'll find restaurants, museums, and boat rental. Many guests combine their stay with a boat or canoe from Nijenhuis Boat Rental – ask about options when booking.",
      camping_tips_title: "Practical information for seasonal camping",
      camping_tips_p1: "The campsite is open from 1 April to 31 October. Caravans may remain on the pitch year-round, so you can come and go stress-free in the shoulder seasons. Each pitch has water, electricity (with its own meter), sewer connection, and a private mooring. Sanitary facilities with showers and toilets are available, as well as a washing machine and dryer.",
      camping_tips_p2: "Due to the small scale and popular location, we recommend booking in advance. Call us for availability and prices. Dogs are welcome, on a lead on the campsite. The atmosphere is quiet and suitable for families and nature lovers who enjoy simplicity and direct contact with the water.",

      facilities_title: "Facilities",
      facilities_description: "All facilities for seasonal pitches",
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
      /* vaarkaart page */
      vaarkaart_title: "Weerribben-Wieden water map - routes and navigation info",
      vaarkaart_description: "Navigation info and routes for the Weerribben nature reserve",

      vaarkaart_interactive_map_title: "Weerribben-Wieden Boat Map",
      vaarkaart_interactive_map_description: "Discover the most beautiful routes through National Park Weerribben-Wieden. This boat map shows all navigation routes in the area.",
      vaarkaart_intro_extra: "National Park Weerribben-Wieden is the largest lowland peat bog in Northwestern Europe. The water area consists of lakes, ditches, and canals that originated from peat extraction in the past. Today it's a paradise for boaters, with quiet routes, reed beds, marshes, and wide views. From Nijenhuis Boat Rental in Wanneperveen you sail straight into the network. Below you'll find the interactive map, popular routes, and important navigation rules.",
      vaarkaart_route_giethoorn_desc: "The route leads through narrow ditches and wider canals to the centre of Giethoorn. Along the way you'll see thatched farmhouses, bridges, and typical punters. In Giethoorn you can moor to walk or have lunch. Allow at least 2–3 hours for a relaxed round trip.",
      vaarkaart_route_weerribben_desc: "This route takes you deeper into the park, past marshes, reed beds, and open water. You can spot kingfishers, herons, dragonflies, and various waterbirds. Bring a picnic and find a quiet spot on the shore. An electric sloop or canoe is ideal for this route.",
      vaarkaart_route_wanneperveen_desc: "An ideal route for a first introduction to the area or if you have limited time. You sail around Wanneperveen and enjoy the village views and surrounding waters. Suitable for all boat types, including kayaks and canoes.",
      vaarkaart_interactive_map_map_title: "Weerribben Nature Reserve - Interactive Water Map",
      vaarkaart_interactive_map_attribution_source: "Source:",
      vaarkaart_interactive_map_attribution_source_link: "Waterkaart.net",
      vaarkaart_interactive_map_attribution_suffix: "– Professional boat maps for Dutch waters",
      vaarkaart_interactive_map_placeholder_title: "Interactive Water Map",
      vaarkaart_interactive_map_placeholder_description: "For the most current and detailed water maps of the Weerribben area, visit the professional Waterkaart of the Netherlands.",
      vaarkaart_interactive_map_placeholder_button: "Open waterkaart.net",
      vaarkaart_interactive_map_footer_description: "This interactive water map is provided by Waterkaart.net. For the latest info and detailed maps, visit their website.",
      vaarkaart_expand_map: "Expand map",
      vaarkaart_close_fullscreen: "Close",
      vaarkaart_view_osm: "OpenStreetMap",
      vaarkaart_disclaimer_title: "Disclaimer:",
      vaarkaart_disclaimer_text: "We take no responsibility for the content and accuracy of this map. Local laws, rules, and signs along the water must always be followed first.",
      vaarkaart_footer_source: "For detailed water maps and current navigation information, visit <a href='https://waterkaart.net/' target='_blank' rel='noopener noreferrer'>Waterkaart.net</a>.",

      giethoorn_title: "Visit Giethoorn - rent a boat in the Venice of the North",
      belt_schutsloot_title: "Belt-schutsloot - hidden gem near Giethoorn and Weerribben",
      wanneperveen_title: "Wanneperveen - peaceful boating in the Weerribben",
      wanneperveen_description: "Discover the most beautiful waterways of the Weerribben from Wanneperveen",

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
      contact_title: "Contact and directions - Nijenhuis Wanneperveen",
      contact_p: "Contact us for questions, reservations or more information",

      contact_h2: "Contact & Route",
      contact_h2_p: "Contact Nijenhuis Boat Rental in Wanneperveen. View our contact details and route directions here.",
      contact_intro_extra: "Nijenhuis Boat Rental is located at Veneweg 199 in Wanneperveen, on the edge of National Park Weerribben-Wieden. We specialise in boat rental – from electric sloops and sailboats to kayaks and SUP boards – and also offer seasonal camping. For reservations, questions about prices or availability, you can call us or drop by during opening hours. Free parking is available on site.",
      contact_route_h2: "Directions",
      contact_route_p1: "Wanneperveen is located in the Kop van Overijssel region, between Meppel and Steenwijk. Coming by car? Follow the signs to Wanneperveen and look for Veneweg – we're at number 199, right by the water. From Giethoorn it's about 15 minutes by car. Free parking is available on site. Public transport: bus line 77 stops near Wanneperveen; check the timetable for exact stops.",
      contact_route_p2: "During the season (1 April – 31 October) we're open daily from 09:00 to 18:00. For boats and canoes we recommend booking in advance, especially at weekends and in the summer months. On arrival you can come straight to us for the key, briefing, and route map.",

      contact_h3: "Contact Information",

      contact_address_title: "Address",
      contact_address: "Veneweg 199",
      contact_zip: "7946 LP Wanneperveen",
      contact_country: "Netherlands",

      contact_phone_title: "Phone",
      contact_phone: "0522 281 528",

      contact_opening_title: "Opening Hours",
      contact_opening_p: "Daily: 09:00 AM - 06:00 PM",
      contact_season_p: "Season: April 1 - October 31",

      contact_business_title: "Company Details",
      contact_kvk: "Chamber of Commerce: 6769 7097",
      contact_btw: "VAT No.: NL857 1361 48 B01",

      contact_call_title: "Direct Contact",
      contact_call_p: "For questions, reservations or more information, call us directly:",
      contact_call_button: "Call Now",
      contact_call_info_p: "Available: Daily from 09:00 AM - 06:00 PM",
      contact_call_info_p2: "Season: April 1 - October 31",

      contact_map_title: "Where to find us?",
      contact_map_p: "View our location on the map",
      /* payment pages */
      payment_success_title: "Payment Successful!",
      payment_success_title_pay_on_arrival: "Reservation confirmed!",
      payment_success_subtitle_pay_on_arrival:
        "Your reservation is confirmed. We have received your non-refundable reservation fee. The remaining balance is due on arrival (see summary). You will receive a confirmation email shortly.",
      payment_success_subtitle: "Your boat rental has been confirmed. You will receive a confirmation email shortly.",
      payment_success_processing: "Processing your payment...",
      payment_success_back: "Back to Home",
      payment_success_contact: "Contact Us",
      payment_success_booking_id: "Booking ID:",
      payment_success_date: "Date:",
      payment_success_duration: "Duration:",
      payment_success_boat_type: "Boat Type:",
      payment_success_customer: "Customer:",
      payment_success_status: "Status:",
      payment_success_price: "Price:",
      payment_success_breakdown_rental: "Rental",
      payment_success_breakdown_fee: "Administrative fee",
      payment_success_breakdown_total: "Total paid",
      payment_success_breakdown_total_due: "Total (on arrival)",
      payment_success_breakdown_reservation_fee: "Reservation fee (paid, non-refundable)",
      payment_success_breakdown_reservation_rental_portion: "Of which: rental portion of reservation",
      payment_success_breakdown_reservation_admin_slice: "Of which: admin fee on that portion only",
      payment_success_breakdown_balance_arrival: "Due on arrival",
      payment_success_reference_label: "Booking reference:",
      payment_success_arrival_title: "Arrival",
      payment_success_arrival_location_label: "Location",
      payment_success_arrival_time_label: "Arrival time",
      payment_success_arrival_bring_label: "What to bring",
      payment_success_arrival_bring_text:
        "Sun protection and comfortable clothing. Deposit is paid in cash on arrival as per your booking.",
      payment_success_price_via_mollie: "(via Mollie)",
      payment_success_price_pay_on_arrival: "(pay on arrival)",
      payment_success_download_pdf: "Download as PDF",
      payment_success_pdf_heading: "Booking confirmation - Nijenhuis Boat Rental",
      payment_success_pdf_date_generated: "Generated:",
      payment_success_pdf_unavailable: "PDF download is unavailable. Refresh the page or contact us.",
      payment_success_pdf_deposit_heading: "Deposit on arrival",
      payment_success_pdf_wordmark: "NIJENHUIS",
      payment_success_pdf_wordmark_sub: "Boat rental",
      payment_success_pdf_hero_date_label: "Trip date",
      payment_success_pdf_hero_total_label: "Total paid",
      payment_success_pdf_hero_total_note: "Rental incl. VAT, paid via Mollie",
      payment_success_pdf_hero_total_note_poa:
        "Total rental and admin due on arrival. Reservation portion paid online (non-refundable).",
      payment_success_pdf_price_breakdown_title: "Price breakdown",
      payment_success_pdf_poa_paid_at_reservation: "Paid with reservation",
      payment_success_pdf_poa_huurdeel: "Rental portion",
      payment_success_pdf_poa_total_paid_nonrefund: "Total paid (non-refundable)",
      payment_success_pdf_poa_total_arrival: "Total on arrival",
      payment_success_pdf_poa_including_deposit: "Including deposit",
      payment_success_pdf_total_cash_arrival: "Total cash on arrival (rental balance + deposit)",
      payment_success_pdf_col_cancellation: "Cancellation policy",
      payment_success_pdf_col_bring: "What to bring",
      payment_success_pdf_col_practical: "Contact & location",
      payment_success_pdf_checkin_label: "Arrival / check-in",
      payment_success_pdf_footer_wish:
        "We wish you safe passage and a wonderful day on the water in the Weerribben!",
      payment_failure_title: "Payment Failed",
      payment_failure_subtitle: "We're sorry, but your payment could not be processed. Please try again or contact us for assistance.",
      payment_failure_try_again: "Try Again",
      payment_failure_back: "Back to Home",
      payment_failure_help_title: "Need Help?",
      payment_failure_help_intro: "If you continue to experience issues with payment, please:",
      payment_failure_help_1: "Check that your payment details are correct",
      payment_failure_help_2: "Ensure you have sufficient funds in your account",
      payment_failure_help_3: "Try a different payment method",
      payment_failure_help_4: "Contact us directly",
      /* checkout page */
      checkout_title: "Checkout",
      checkout_empty_cart_title: "Your cart is empty",
      checkout_empty_cart_desc: "Add boats to your cart to proceed to checkout.",
      checkout_empty_cart_btn: "Go to Boat Rental",
      checkout_reservations_title: "Your Reservations",
      checkout_subtotal: "Rental (subtotal)",
      checkout_admin_fee_label: "Administrative fee ({percent}%)",
      checkout_poa_rental_label: "Rental price",
      checkout_poa_admin_slice_label:
        "Administrative fee ({admin_percent}% on the {res_percent}% reservation slice)",
      checkout_total: "Total to pay:",
      checkout_total_trip: "Trip total (rental + administrative fee):",
      checkout_poa_row_pay_online: "Pay online now",
      checkout_poa_row_on_arrival: "Pay on arrival",
      checkout_deposit_note: "Note: A deposit of €{amount} must be paid in cash upon arrival for the rented boat(s).",
      checkout_your_details: "Your Details",
      checkout_name_label: "Full Name *",
      checkout_email_label: "Email Address *",
      checkout_phone_label: "Phone Number *",
      checkout_address_label: "Address (optional)",
      checkout_notes_label: "Notes (optional)",
      checkout_notes_placeholder: "Any special requests...",
      checkout_back_btn: "Back",
      checkout_pay_btn: "Pay",
      checkout_loading: "Preparing your payment...",
      checkout_error_fields: "Please fill in all required fields.",
      checkout_error_email: "Please enter a valid email address.",
      checkout_error_general: "An error occurred. Please try again.",
      checkout_day: "day",
      checkout_days: "days",

      /* botenverhuur page – SEO blocks (missing keys) */
      boats_header_p: "Rent boats and sloops in the Weerribben near Giethoorn — discover National Park Weerribben-Wieden from the water.",
      boats_bluf_summary: "Rent a boat in the Weerribben? At Nijenhuis Boat Rental in Wanneperveen you experience the peace and space of National Park Weerribben-Wieden from the water. A family business for over 50 years, loved by families, couples and groups of friends.",
      boats_intro_title: "Discover the Weerribben your way",
      boats_intro_text: "<p>Want to rent a boat in the Weerribben? At Nijenhuis Boat Rental in Wanneperveen you experience the peace and space of National Park Weerribben-Wieden from the water. For over 50 years we have been the family business where families, couples and groups of friends return for an unforgettable day on the water.</p><p>Our location at Veneweg 199 in Wanneperveen is right on the water — just 10 kilometres from Giethoorn. That means: no crowds when you depart, free parking right at the door, and direct access to the most beautiful routes through the largest lowland peat bog in north-western Europe.</p><p>Whether you choose a whisper-quiet electric sloop for the whole family, a sporty canoe for two, or a traditional sail punter — we have the perfect boat for every occasion. Best of all: you do not need a boating licence. Before departure we give you clear instructions and a detailed route map.</p>",
      boats_fleet_title: "Our fleet: 25+ boats for every group",
      boats_fleet_electric_badge: "Most popular",
      boats_fleet_electric_title: "Luxury electric sloops (whisper boats)",
      boats_fleet_electric_desc: "Our electric sloops are absolute favourites with our guests. These whisper boats have a silent electric motor that glides you quietly through the Weerribben. No engine noise, no exhaust fumes — only rippling water, singing birds and rustling reeds. All sloops have comfortable cushions, a steering wheel (not a tiller!), and a range of 8 to 10 hours.",
      boats_fleet_sail_badge: "Traditional and sporty",
      boats_fleet_sail_title: "Sailboats & punters",
      boats_fleet_sail_desc: "The punter is inseparable from the Weerribben. Experience history yourself and rent a traditional sail punter that sails on wind alone. For more stability and comfort we have Randmeer sailboats. No wind? No problem — sailboats can optionally be fitted with an outboard motor.",
      boats_fleet_active_badge: "Active on the water",
      boats_fleet_active_title: "Canoe, kayak & SUP",
      boats_fleet_active_desc: "Want to experience the Weerribben up close? With a canoe or kayak you reach places motorboats cannot. Paddle through narrow canals, discover hidden pools and spot special birds from the water.",
      boats_card_reserve: "Book now",
      boats_price_from: "From",
      boats_price_per_day: "/ day",
      boats_card_title_classic_tender_720: "Classic Tender 720",
      boats_card_specs_classic_tender_720: "<li>Suitable for up to 12 people</li><li>Spacious layout with seating area and table</li><li>Range: 8–10 hours on one charge</li>",
      boats_card_title_classic_tender_570: "Classic Tender 570",
      boats_card_specs_classic_tender_570: "<li>Suitable for up to 8 people</li><li>Ideal for families and small groups</li><li>Range: 8–10 hours on one charge</li>",
      boats_card_title_electrosloop_10: "Electric sloop 10-person",
      boats_card_specs_electrosloop_10: "<li>Extra space for larger groups</li><li>Equipped with swim ladder</li>",
      boats_card_title_electrosloop_8: "Electric sloop 8-person",
      boats_card_specs_electrosloop_8: "<li>Compact and easy to handle</li><li>Perfect for a day out with friends</li>",
      boats_card_title_electroboat_5: "Electric boat 5-person",
      boats_card_specs_electroboat_5: "<li>Our smallest electric boat</li><li>Ideal for couples or a small family</li>",
      boats_card_title_sailboat_4_5: "Sailboat 't Waar (4–5 people)",
      boats_card_specs_sailboat_4_5: "<li>Stable Randmeer sailboat</li><li>Optionally with outboard motor (€85/day)</li>",
      boats_card_title_sailpunter_3_4: "Sail punter (3–4 people)",
      boats_card_specs_sailpunter_3_4: "<li>Traditional wooden vessel</li><li>Wind and sail only — the authentic experience</li>",
      boats_card_title_canoe_3: "Canoe (3 people)",
      boats_card_specs_canoe_3: "<li>Stable Canadian canoe</li><li>Including paddles and route map</li>",
      boats_card_title_kayak_2: "Kayak 2-person",
      boats_card_specs_kayak_2: "<li>Sporty and manoeuvrable</li><li>Including paddles</li>",
      boats_card_title_kayak_1: "Kayak 1-person",
      boats_card_specs_kayak_1: "<li>Solo adventure on the water</li><li>Including paddle</li>",
      boats_card_title_sup_board: "SUP board",
      boats_card_specs_sup_board: "<li>Stand-up paddleboarding in nature</li><li>Including paddle</li>",
      boats_why_title: "Why choose Nijenhuis Boat Rental?",
      boats_why_water_title: "Right on the water",
      boats_why_water_desc: "Our location is literally on the water. Step out of the car, walk to the jetty and sail away. No trailer hassle, no queues.",
      boats_why_parking_title: "Free parking",
      boats_why_parking_desc: "You always park free of charge, directly at the rental location. In Giethoorn itself you quickly pay €10–15 for parking.",
      boats_why_quiet_title: "Peace instead of crowds",
      boats_why_quiet_desc: "Giethoorn is beautiful, but busy in high season. Departing from Wanneperveen you avoid boarding crowds and enjoy the peace on the water immediately. After 15–20 minutes of sailing you reach Giethoorn.",
      boats_why_service_title: "Personal service",
      boats_why_service_desc: "As a family business we know every boat and every route. We take time for thorough instructions and share tips for the most beautiful spots not in the guidebooks.",
      boats_why_flexible_title: "Flexible rental",
      boats_why_flexible_desc: "<ul class=\"anchor-list\"><li><strong>Per day:</strong> book online or by phone</li><li><strong>Per hour:</strong> on-site only, for spontaneous visitors</li><li><strong>Cash and card</strong> accepted</li></ul>",
      boats_routes_title: "Popular routes from Wanneperveen",
      boats_route_1_title: "Route 1: To Giethoorn",
      boats_route_1_meta: "8–10 km, 1.5–2 hours one way",
      boats_route_1_desc: "Sail through the canals towards famous Giethoorn. Admire thatched farmhouses, characteristic bridges and the picturesque village centre. Ideal as a day trip.",
      boats_route_2_title: "Route 2: Belt-Schutsloot",
      boats_route_2_meta: "6–8 km, 1–1.5 hours",
      boats_route_2_desc: "The \"hidden Giethoorn\" — the same charm without the tourist crowds. Authentic bridges, historic farmhouses and a rustic atmosphere.",
      boats_route_3_title: "Route 3: Weerribben nature",
      boats_route_3_meta: "15 km, 3–4 hours",
      boats_route_3_desc: "Deep into the National Park. Paddle or sail through narrow canals, discover pools and spot special flora and fauna. Ideal by canoe or kayak.",
      boats_route_4_title: "Route 4: Beulakerwijde",
      boats_route_4_meta: "10 km, 2–3 hours",
      boats_route_4_desc: "The large lake south of Wanneperveen. Open water, beautiful views and a perfect spot for sailing.",
      boats_routes_map_link: "View our interactive route map for detailed routes →",
      boats_route_cta: "View on route map →",
      boats_faq_q1: "Do I need a boating licence?",
      boats_faq_a1: "No, you do not need a licence for any of our boats. All boats are under 15 metres and travel slower than 20 km/h. You receive personal instructions before departure.",
      boats_faq_q2: "How far can I sail with an electric boat?",
      boats_faq_a2: "The batteries last a full day under normal use. The route to Giethoorn and back (±20 km) is no problem. On arrival we always check that the battery is fully charged.",
      boats_faq_q3: "How much does boat hire cost at Nijenhuis?",
      boats_faq_a3: "Prices start from €20 per day for a canoe or kayak. Electric sloops are available from €95 per half day. See the full price list on our booking page.",
      boats_faq_q4: "Can I hire a boat for 12 people?",
      boats_faq_a4: "Yes, our Classic Tender 720 is suitable for up to 12 people. For optimal comfort we recommend 10 people. For larger groups you can also book two boats side by side.",
      boats_faq_q5: "Can I bring my dog?",
      boats_faq_a5: "Pets are allowed on the electric sloops, canoes, sail punter and electroboat. Pets are not allowed on the Classic Tenders.",
      boats_faq_q6: "What if the weather is bad?",
      boats_faq_a6: "In light rain you can still sail — bring rain gear. In thunderstorms or storms we advise against going on the water. In extreme conditions you can rebook free of charge to another date.",
      boats_faq_q7: "What time can I depart?",
      boats_faq_a7: "You can depart from 9:00 in the morning. Last rental times depend on the season. In summer you can pick up a boat until 18:00 for an evening cruise.",
      boats_faq_q8: "Is there parking?",
      boats_faq_a8: "Yes, parking at Nijenhuis Botenverhuur in Wanneperveen is completely free. You park right next to the jetty so you can get on the water straight away.",
      boats_faq_all_link: "View all frequently asked questions →",
      boats_fishing_title: "Fishing from our boats in the Weerribben",
      boats_fishing_p1: "Discover why the Weerribben is an angler's paradise in Overijssel. Although we do not rent specialised fishing boats with tents or carp boats, our spacious electric sloops and canoes are ideal for a day of fishing.",
      boats_fishing_p2: "The quiet electric motors do not disturb carp and predatory fish, and with a kayak rental in the Weerribben you reach the quietest fishing spots where motorboats cannot go. Bring your rod for a fishing holiday in Wanneperveen – <a href=\"/vaarkaart\">view our route map</a> for the best fishing spots on Belterwiede!",
      boats_cta_h2: "Book your boat now",
      boats_cta_p: "Ready to discover the Weerribben? Book your boat today and enjoy an unforgettable day on the water near Giethoorn.",
      boats_cta_details: "<ul class=\"boats-cta-list anchor-list\"><li><strong>Book online:</strong> use the reservation form at the top of this page</li><li><strong>Call:</strong> <a href=\"tel:0522281528\">0522 281 528</a></li><li><strong>Visit us:</strong> Veneweg 199, 7946 LP Wanneperveen</li></ul>",
      boats_cta_hours: "Open from 1 April to 31 October, daily 09:00–18:00. No boating licence required. Cash and card accepted.",
      boats_cta_btn: "Book now",
      boats_cta_phone: "📞 Call us",

      /* booking page – missing keys */
      booking_options_title: "Extra options",
      booking_option_motor: "Rent a motor too?",

      /* checkout page – missing keys */
      checkout_home_btn: "🏠 Back to website",
      checkout_policy_title: "Important Information",
      checkout_policy_cancellation: "A cancellation fee of 10% of the total amount applies.",
      checkout_policy_cancellation_poa:
        "The reservation fee paid online is non-refundable.",
      checkout_policy_contact: "For changes, please contact us by phone at +31 522 281 528.",
      checkout_policy_location: "Our location: Veneweg 199, 7946 LP Wanneperveen",
      checkout_secure_title: "Secure checkout",
      checkout_step_details: "Details",
      checkout_step_payment: "Payment",
      checkout_step_confirm: "Confirmation",
      checkout_booking_summary: "Booking summary",
      checkout_payment_info_title: "Payment method",
      checkout_payment_info_body:
        "Choose how you want to pay below. You will then continue to Mollie’s secure page to complete the payment.",
      checkout_method_ideal: "iDEAL",
      checkout_method_bancontact: "Bancontact",
      checkout_method_applepay: "Apple Pay",
      checkout_method_googlepay: "Google Pay",
      checkout_wallet_divider: "Or pay with",
      checkout_method_pay_on_arrival: "Pay on arrival",
      checkout_pay_on_arrival_inline: "Latest pick-up by 11 a.m.",
      checkout_poa_fee_explain:
        "Summary: full rental price; admin fee shown is only {admin_percent}% of the {percent}% reservation slice—not {admin_percent}% of the whole rental. You pay that slice plus that admin online; on arrival you pay the rest of the rental.",
      checkout_poa_fee_explain_no_admin_fee:
        "You pay {percent}% of the rental online as a reservation; the remainder is due on arrival.",
      checkout_poa_pay_now_line:
        "Pay online now: €{reservation}\nDue on arrival: €{balance}\n(Reservation: {percent}% of rental; admin fee only on that portion.)",
      checkout_error_pay_on_arrival_time:
        "Pay on arrival is only available with an arrival time no later than 11:00. Choose an earlier time or another payment method.",
      checkout_trust_secure: "Secure payment via Mollie",
      checkout_trust_support: "Need help? Call +31 522 281 528",
      checkout_trust_policy: "See cancellation terms in the box alongside",
      checkout_qty_label: "Qty:",
      checkout_arrival_time_label: "Arrival time *",
      checkout_city_label: "City / town *",

      /* global – cart sidebar */
      cart_title: "🛒 Cart",
      cart_close_aria: "Close",
      admin_fee_disclosure_note:
        "Online payment adds a {percent}% administrative fee on the rental amount.",
      cart_empty: "Your cart is empty",
      cart_total_label: "Total:",
      cart_checkout_btn: "Checkout",
      cart_clear_btn: "Clear",
      compare_max_pins: "You can compare up to 3 boats.",

      /* booking modal */
      booking_modal_checking_availability: "Checking availability...",
      booking_modal_end_date_label: "End date (optional)",
      booking_modal_engine_option: "With outboard motor (+ surcharge)",
      booking_modal_direct_checkout_btn: "💳 Pay now",
      booking_modal_confirm_btn: "Confirm booking",
      booking_modal_cancel_btn: "Cancel",
      booking_modal_success_title: "Booking successful!",
      booking_modal_success_text: "Your booking has been confirmed. You will receive a confirmation email shortly.",
      booking_modal_booking_id_label: "Booking ID:",
      booking_modal_error_title: "Error",
      booking_modal_error_default: "An error occurred while processing your booking.",
      booking_modal_retry_btn: "Try again",

      /* home page – about block */
      home_about_title: "About Nijenhuis Boat Rental",
      home_about_tagline: "Your trusted watersports partner in the Weerribben for over 50 years",

      /* camping page */
      camping_overview_cta_text: "Contact us for options and availability.",
      season_status_open: "Open now",
      season_status_closed_until: "Closed until April 1",

      /* vakantiehuis page */
      house_visit_website_btn: "🌐 Visit Waterpark Belterwiede",

      /* contact page */
      contact_success_title: "✅ Message sent successfully!",
      contact_success_message: "Thanks for your message. We’ll get back to you as soon as possible via email.",
      contact_success_sent_to: "Your message was sent to: info@nijenhuis-botenverhuur.nl",

      /* checkout (inline JS strings) */
      checkout_confirm_remove_item: "Are you sure you want to remove this reservation?",
      checkout_notification_removed: "Reservation removed",
      checkout_notification_remove_error: "Error removing reservation",
      checkout_error_unavailable_boats:
        "Unfortunately, the following boat(s) are no longer available: {boats}. Remove them from your cart and try again.",
      checkout_error_payment_method: "Please choose a valid payment method.",

      /* payment failure (inline JS strings) */
      payment_failure_status_failed: "Payment status: {status}. The payment failed.",
      payment_failure_status_pending:
        "Payment status: {status}. Your payment is still being processed. Check your email for updates.",

      /* FAQ page */
      faq_header_h1: "Frequently asked questions about boat rental Giethoorn",
      faq_header_p: "Everything you need to know about boat hire and renting a boat at Nijenhuis",
      faq_intro_expanded: "On this page you'll find answers about boat rental Giethoorn, rent a boat in the Weerribben, and boat hire at Nijenhuis. Topics: prices per boat type, licence, opening hours, reservations, what's included, sailing to Giethoorn, payment and pets. Can't find your question? Contact us – we're happy to help.",
      faq_contact_cta_p: "Can't find your question? Feel free to contact us.",
      faq_contact_cta_form: "Contact form",
      faq_fleet_title: "View our fleet",
      faq_fleet_subtitle: "Choose the boat type that best fits your group and wishes:",
      faq_fleet_cta: "Check availability →",
      faq_page_html:
        "<div class='faq-intro' style='max-width: 800px; margin: 0 auto 2rem;'><p style='font-size: 1.1rem; line-height: 1.7; color: var(--text-secondary);'>Here you’ll find answers to the most frequently asked questions about renting a boat at Nijenhuis. Can’t find your question? Feel free to <a href='/contact'>contact</a> us or call <a href='tel:0522281528'>0522 281 528</a>.</p></div><div class='faq-list' style='max-width: 800px; margin: 0 auto;'><h2 style='margin-top: 2rem; color: var(--secondary-color);'>💰 Prices &amp; payment</h2><div class='faq-item' style='margin-bottom: 1.5rem; padding: 1.5rem; background: #f8fafc; border-radius: 12px;'><h3 style='margin: 0 0 0.75rem; font-size: 1.15rem; color: var(--secondary-color);'>How much does it cost to rent a boat?</h3><div class='faq-answer' style='line-height: 1.7;'><p>Prices depend on the boat type:</p><ul id='faq-price-list' style='margin: 0.5rem 0; padding-left: 1.5rem;'></ul><p style='margin-top: 0.75rem;'>Multi-day rentals may include a discount. <a href='/botenverhuur'>See all prices →</a></p></div></div><div class='faq-item' style='margin-bottom: 1.5rem; padding: 1.5rem; background: #f8fafc; border-radius: 12px;'><h3 style='margin: 0 0 0.75rem; font-size: 1.15rem; color: var(--secondary-color);'>Do I need to pay a deposit?</h3><div class='faq-answer' style='line-height: 1.7;'><p>Yes, for sloops we require a deposit of <span data-faq-dynamic='deposit-sloep'></span> which must be paid <strong>in cash</strong> upon arrival. For the sailboat, a deposit of <span data-faq-dynamic='deposit-zeilboot'></span> <strong>in cash</strong> is required. For canoes and SUPs, a deposit is usually not required, but we do ask for a valid ID to be left behind.</p></div></div><h2 style='margin-top: 2.5rem; color: var(--secondary-color);'>📋 Practical info</h2><div class='faq-item' style='margin-bottom: 1.5rem; padding: 1.5rem; background: #f8fafc; border-radius: 12px;'><h3 style='margin: 0 0 0.75rem; font-size: 1.15rem; color: var(--secondary-color);'>Do I need a license?</h3><div class='faq-answer' style='line-height: 1.7;'><p><strong>No</strong>, you don’t need a license. Before departure, you’ll receive a short briefing.</p></div></div></div>"
    },

  };

  /* ---------- 1b. SEO META (per-page, per-language) ------ */
  const meta = {
    home: {
      title: { nl: "Botenverhuur Wanneperveen & Weerribben | Nijenhuis", en: "Boat Rental Wanneperveen & Weerribben | Nijenhuis", de: "Bootsverleih Wanneperveen & Weerribben | Nijenhuis" },
      desc: { nl: "Botenverhuur bij Giethoorn. Huur een bootje, luxe sloep, fluisterboot of kano in de Weerribben. Sloepverhuur en bootverhuur vanaf €20/dag. Geen vaarbewijs nodig.", en: "Boat rental at Giethoorn. Rent a boat, luxury sloop, electric boat or canoe in the Weerribben. Sloop rental from €20/day. No boating license required.", de: "Bootsverleih bei Giethoorn. Miete ein Boot, Luxussloep oder Kanu in den Weerribben. Bootsverleih ab €20/Tag. Kein Führerschein erforderlich." }
    },
    boats: {
      title: { nl: "Botenverhuur Giethoorn | Sloep, Fluisterboot & Kano Huren", en: "Boat Rental Giethoorn | Sloop, Electric Boat & Canoe Hire", de: "Bootsverleih Giethoorn | Sloep, Elektroboot & Kanu mieten" },
      desc: { nl: "Boot huren bij Giethoorn? Nijenhuis Botenverhuur verhuurt luxe sloepen, fluisterboten, zeilboten, kano's en SUP boards in de Weerribben. Vanaf €20/dag. Geen vaarbewijs nodig. Reserveer nu!", en: "Rent a boat at Giethoorn? Nijenhuis Boat Rental offers luxury sloops, electric boats, sailboats, canoes and SUP boards in the Weerribben. From €20/day. No licence required. Book now!", de: "Boot mieten bei Giethoorn? Nijenhuis Bootsverleih vermietet Luxus-Sloopen, Elektroboote, Segelboote, Kanus und SUP-Boards in den Weerribben. Ab €20/Tag. Kein Führerschein nötig. Jetzt reservieren!" }
    },
    camping: {
      title: { nl: "Camping Weerribben bij Giethoorn", en: "Camping Weerribben near Giethoorn", de: "Camping Weerribben bei Giethoorn" },
      desc: { nl: "Seizoenscamping in Nationaal Park Weerribben-Wieden bij Giethoorn. ✓ Eigen aanlegplaats ✓ Water, elektriciteit & sanitair ✓ Caravan mag jaarrond staan. Al 50+ jaar familiebedrijf.", en: "Seasonal camping in National Park Weerribben-Wieden near Giethoorn. ✓ Own berth ✓ Water, electricity & sanitary ✓ Caravan can stay year-round. 50+ years family business.", de: "Saisoncamping im Nationalpark Weerribben-Wieden bei Giethoorn. ✓ Eigener Liegeplatz ✓ Wasser, Strom & Sanitär ✓ Wohnwagen kann ganzjährig stehen. 50+ Jahre Familienbetrieb." }
    },
    vakantiehuis: {
      title: { nl: "Vakantiehuis Belterwiede bij Giethoorn", en: "Holiday House Belterwiede near Giethoorn", de: "Ferienhaus Belterwiede bei Giethoorn" },
      desc: { nl: "Ruim vakantiehuis aan het Belterwiede meer bij Giethoorn. 5 slaapkamers, 2 badkamers, volledig uitgerust. ✓ Het hele jaar geopend ✓ Direct aan het water ✓ Ideaal voor families.", en: "Spacious holiday house at Lake Belterwiede near Giethoorn. 5 bedrooms, 2 bathrooms, fully equipped. ✓ Open year-round ✓ Right by the water ✓ Ideal for families.", de: "Geräumiges Ferienhaus am Belterwiede-See bei Giethoorn. 5 Schlafzimmer, 2 Badezimmer, voll ausgestattet. ✓ Ganzjährig geöffnet ✓ Direkt am Wasser ✓ Ideal für Familien." }
    },
    "te-koop": {
      title: { nl: "Chalets & stacaravans te koop", en: "Chalets & caravans for sale", de: "Chalets & Wohnwagen zu verkaufen" },
      desc: { nl: "Bekijk chalets en stacaravans te koop bij Camping Nijenhuis in Wanneperveen. Direct aan het water in de Weerribben. Actueel aanbod met prijzen.", en: "View chalets and caravans for sale at Camping Nijenhuis in Wanneperveen. Right by the water in the Weerribben. Current listings with prices.", de: "Chalets und Wohnwagen zum Verkauf bei Camping Nijenhuis in Wanneperveen. Direkt am Wasser in den Weerribben. Aktuelle Angebote mit Preisen." }
    },
    vaarkaart: {
      title: { nl: "Vaarkaart Weerribben-Wieden | Routes", en: "Sailing Chart Weerribben-Wieden | Routes", de: "Fahrkarte Weerribben-Wieden | Routen" },
      desc: { nl: "Interactieve vaarkaart voor de Weerribben. Ontdek vaarroutes naar Giethoorn, Wanneperveen en door Nationaal Park Weerribben-Wieden. Inclusief vaarregels en veiligheidstips.", en: "Interactive sailing chart for the Weerribben. Discover routes to Giethoorn, Wanneperveen and through National Park Weerribben-Wieden. Including sailing rules and safety tips.", de: "Interaktive Fahrkarte für die Weerribben. Entdecke Routen nach Giethoorn, Wanneperveen und durch den Nationalpark Weerribben-Wieden. Mit Fahrregeln und Sicherheitstipps." }
    },
    contact: {
      title: { nl: "Contact & Route | Wanneperveen", en: "Contact & Directions | Wanneperveen", de: "Kontakt & Anfahrtsweg | Wanneperveen" },
      desc: { nl: "Contact opnemen met Nijenhuis Botenverhuur. Adres: Veneweg 199, Wanneperveen. Tel: 0522 281 528. Open april-oktober, dagelijks 9:00-18:00. Gratis parkeren.", en: "Contact Nijenhuis Boat Rental. Address: Veneweg 199, Wanneperveen. Tel: 0522 281 528. Open April-October, daily 9:00-18:00. Free parking.", de: "Kontakt zu Nijenhuis Bootsverleih. Adresse: Veneweg 199, Wanneperveen. Tel: 0522 281 528. Geöffnet April-Oktober, täglich 9:00-18:00. Kostenloses Parken." }
    },
    faq: {
      title: { nl: "Veelgestelde vragen", en: "Frequently asked questions", de: "Häufig gestellte Fragen" },
      desc: { nl: "Antwoorden op veelgestelde vragen over boot huren bij Nijenhuis. Prijzen, vaarbewijs, openingstijden, reserveren en meer informatie over botenverhuur in de Weerribben.", en: "Answers to frequently asked questions about boat hire at Nijenhuis. Prices, license, opening hours, reservations and more about boat rental in the Weerribben.", de: "Antworten auf häufig gestellte Fragen zum Bootsverleih bei Nijenhuis. Preise, Führerschein, Öffnungszeiten, Reservierungen und mehr zum Bootsverleih in den Weerribben." }
    },
    giethoorn: {
      title: { nl: "Nijenhuis Botenverhuur bij Giethoorn – ontdek het Venetië van het Noorden", en: "Nijenhuis Boat Rental at Giethoorn – discover the Venice of the North", de: "Nijenhuis Bootsverleih bei Giethoorn – entdecke das Venedig des Nordens" },
      desc: { nl: "Ontdek Giethoorn per boot. Huur een fluisterboot, zeilpunter of kano bij Nijenhuis Botenverhuur en vaar door de prachtige grachten van het Venetië van het Noorden. Reserveer online!", en: "Discover Giethoorn by boat. Rent an electric boat, sailpunter or canoe at Nijenhuis Boat Rental and cruise through the beautiful canals of the Venice of the North. Book online!", de: "Entdecke Giethoorn mit dem Boot. Miete ein Elektroboot, Segelpunter oder Kanu bei Nijenhuis Bootsverleih und fahre durch die malerischen Grachten des Venedigs des Nordens. Online buchen!" }
    },
    "belt-schutsloot": {
      title: { nl: "Belt-schutsloot | Alternatief Giethoorn", en: "Belt-schutsloot | Alternative to Giethoorn", de: "Belt-schutsloot | Alternative zu Giethoorn" },
      desc: { nl: "Ontdek Belt-schutsloot, een verborgen parel nabij Giethoorn. Minder toeristisch, maar met dezelfde idyllische charme: grachten, bruggetjes en rietgedekte huizen. Boot huren voor Belt-schutsloot bij Nijenhuis Botenverhuur.", en: "Discover Belt-schutsloot, a hidden gem near Giethoorn. Less touristic but with the same idyllic charm: canals, bridges and thatched houses. Rent a boat for Belt-schutsloot at Nijenhuis Boat Rental.", de: "Entdecke Belt-schutsloot, ein verborgenes Juwel bei Giethoorn. Weniger touristisch, aber mit dem gleichen idyllischen Charme: Grachten, Brücken und Reetdachhäuser. Boot mieten für Belt-schutsloot bei Nijenhuis Bootsverleih." }
    },
    wanneperveen: {
      title: { nl: "Bootverhuur Nijenhuis Wanneperveen: waarom bij ons", en: "Nijenhuis Boat Rental Wanneperveen: why choose us", de: "Nijenhuis Bootsverleih Wanneperveen: warum wir" },
      desc: { nl: "Geniet van rustig varen met Bootverhuur Nijenhuis Wanneperveen. Huur een boot in het vredige Wanneperveen en ontdek prachtige vaarwegen zonder drukte.", en: "Enjoy peaceful cruising with Nijenhuis Boat Rental Wanneperveen. Rent a boat in tranquil Wanneperveen and discover beautiful waterways without crowds.", de: "Genieße entspanntes Fahren mit Nijenhuis Bootsverleih Wanneperveen. Miete ein Boot im ruhigen Wanneperveen und entdecke wunderschöne Wasserstraßen ohne Trubel." }
    },
    booking: {
      title: { nl: "Boek je boot", en: "Book your boat", de: "Boot buchen" },
      desc: { nl: "Boek je boot bij Nijenhuis Botenverhuur. Voltooi je reservering voor een perfecte dag op het water in de Weerribben.", en: "Book your boat at Nijenhuis Boat Rental. Complete your reservation for a perfect day on the water in the Weerribben.", de: "Buch dein Boot bei Nijenhuis Bootsverleih. Schließe deine Reservierung für einen perfekten Tag auf dem Wasser in den Weerribben ab." }
    },
    checkout: {
      title: { nl: "Afrekenen", en: "Checkout", de: "Kasse" },
      desc: { nl: "Voltooi je reservering bij Nijenhuis Botenverhuur.", en: "Complete your reservation at Nijenhuis Boat Rental.", de: "Schließe deine Reservierung bei Nijenhuis Bootsverleih ab." }
    }
  };

  const OG_LOCALES = { nl: "nl_NL", en: "en_GB", de: "de_DE" };

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

    /* HTML lang attribute */
    document.documentElement.lang = lang;

    /* Dynamic meta (title, description, OG) */
    if (!isBlogPage()) {
      const page = document.body?.getAttribute?.("data-page");
      const m = page && meta[page];
      if (m) {
        const title = m.title?.[lang] || m.title?.[DEFAULT_LANG];
        const desc = m.desc?.[lang] || m.desc?.[DEFAULT_LANG];
        if (title) document.title = title;
        const descMeta = document.querySelector('meta[name="description"]');
        if (descMeta && desc) descMeta.setAttribute("content", desc);
        const ogTitle = document.querySelector('meta[property="og:title"]');
        if (ogTitle && title) ogTitle.setAttribute("content", title);
        const ogDesc = document.querySelector('meta[property="og:description"]');
        if (ogDesc && desc) ogDesc.setAttribute("content", desc);
        const ogLocale = document.querySelector('meta[property="og:locale"]');
        if (ogLocale) ogLocale.setAttribute("content", OG_LOCALES[lang] || "nl_NL");
        const basePath = window.location.pathname || "/";
        const ogUrl = document.querySelector('meta[property="og:url"]');
        if (ogUrl) {
          const url = lang === "nl" ? window.location.origin + basePath : window.location.origin + basePath + (basePath.includes("?") ? "&" : "?") + "lang=" + lang;
          ogUrl.setAttribute("content", url);
        }
        const ogImgAlt = document.querySelector('meta[property="og:image:alt"]');
        if (ogImgAlt && desc) ogImgAlt.setAttribute("content", desc);
      }
    }

    /* GA4 language dimension (dimension1 = Language) */
    if (typeof gtag === "function") {
      try { gtag("set", "dimension1", lang); } catch (e) { /* ignore */ }
    }

    /* Update active language button */
    document.querySelectorAll('.lang-btn').forEach(btn => {
      btn.classList.remove('active');
      if (btn.dataset.lang === lang) {
        btn.classList.add('active');
      }
    });

    /* Text content */
    document.querySelectorAll("[data-i18n]").forEach(el => {
      const key = el.getAttribute("data-i18n");
      const value = dict[key];
      if (!value) return;

      // If this element uses translated attributes (e.g. placeholder/aria-label),
      // don't overwrite its displayed value/text (important for inputs, textareas, icon-only buttons).
      const attrList = el.getAttribute("data-i18n-attr");
      const tag = el.tagName;
      if (attrList && (tag === 'INPUT' || tag === 'TEXTAREA' || tag === 'BUTTON')) {
        return;
      }

      // Handle params if present
      let text = value;
      const paramsAttr = el.getAttribute("data-i18n-params");
      if (paramsAttr) {
        try {
          const params = JSON.parse(paramsAttr);
          Object.keys(params).forEach(param => {
            text = text.replace(`{${param}}`, params[param]);
          });
        } catch (e) {
          console.warn('Invalid JSON in data-i18n-params', e);
        }
      }

      // Allow limited HTML for whitelisted keys; use textContent otherwise
      const htmlAllowedKeys = new Set([
        'boats_intro_text', 'boats_bluf_summary', 'boats_why_flexible_desc', 'boats_cta_details', 'boats_fishing_p2', 'intro_h2_p', 'intro_h2_p2', 'deposit_notice_cash',
        'house_overview_p1', 'house_overview_p3', 'house_overview_p4',
        'footer_bottom', 'footer_rights', 'hero_book_badge',
        'camping_overview_seasonal_description',
        'boat_modal_description_title', // Add title if we want bolding etc, but not needed for basic
        'vaarkaart_footer_source', // Needs link
        'vaarkaart_disclaimer_text',
        'vaarkaart_disclaimer_title', // Bold
        'about_location_desc', 'about_season_desc', 'about_fleet_desc', 'about_prices_desc',
        'fleet_hourly_note',
        'faq_page_html'
      ]);

      if (htmlAllowedKeys.has(key) || key.startsWith('vaarkaart_') || key.startsWith('boats_card_specs_')) { // Allow all vaarkaart keys for links/formatting
        // Sanitize HTML to prevent XSS attacks
        if (window.SecurityUtils && window.SecurityUtils.sanitizeHtml) {
          el.innerHTML = window.SecurityUtils.sanitizeHtml(text, {
            allowedTags: ['strong', 'em', 'b', 'i', 'u', 'br', 'p', 'a', 'span', 'div', 'h2', 'h3', 'ul', 'li'],
            allowedAttributes: {
              'a': ['href', 'target', 'rel'],
              'ul': ['id', 'class', 'style'],
              '*': ['class', 'style', 'data-faq-dynamic']
            }
          });
        } else {
          // Fallback: simple replacement if SecurityUtils not available,
          // but for links we might need to trust content or use innerHTML with caution
          el.innerHTML = text;
        }
      } else {
        el.textContent = text.replace(/<[^>]*>/g, '');
      }
    });

    /* Attribute values */
    document.querySelectorAll("[data-i18n-attr]").forEach(el => {
      const key = el.getAttribute("data-i18n");
      const attrs = el.getAttribute("data-i18n-attr").split(",");
      attrs.forEach(attr => {
        if (dict[key]) el.setAttribute(attr.trim(), dict[key]);
      });
    });

    /* Image alt text (data-i18n-alt) */
    document.querySelectorAll("img[data-i18n-alt]").forEach(el => {
      const key = el.getAttribute("data-i18n-alt");
      const alt = dict[key];
      if (alt) el.setAttribute("alt", alt);
    });

    /* Update active state on flag buttons */
    document
      .querySelectorAll("#languageSwitcher .lang-btn")
      .forEach(btn => btn.classList.toggle("active", btn.dataset.lang === lang));
  }

  /* Helper: get equivalent blog URL for a target language */
  function getBlogUrlForLang(targetLang) {
    const pathname = (window.location.pathname || '').replace(/\/$/, '');
    const blogMatch = pathname.match(/^\/(?:en|de)?\/?blog(?:\/([a-z0-9\-]+))?$/);
    if (!blogMatch) return null;
    const slug = blogMatch[1] || '';
    const prefix = targetLang === 'nl' ? '' : '/' + targetLang;
    return prefix + '/blog' + (slug ? '/' + slug : '');
  }

  function isBlogPage() {
    const pathname = (window.location.pathname || '').replace(/\/$/, '');
    return /^\/(?:en|de)?\/?blog(\/[a-z0-9\-]+)?$/.test(pathname);
  }

  function isBlogPageActive(pathname, code) {
    const p = (pathname || '').replace(/\/$/, '');
    if (code === 'nl') return /^\/blog(\/[a-z0-9\-]+)?$/.test(p);
    return new RegExp('^/' + code + '/blog').test(p);
  }

  /* ---------- 4. LANGUAGE SWITCHER UI ---------------------- */
  function buildSwitcher() {
    console.log("buildSwitcher() called");
    let switcher = document.getElementById("languageSwitcher");
    console.log("Language switcher element found:", switcher);
    if (!switcher) {
      // Create a fallback container if missing
      switcher = document.createElement('div');
      switcher.id = 'languageSwitcher';
      switcher.className = 'language-switcher';
      const navTarget = document.querySelector('.top-bar .top-bar-content') || document.querySelector('.nav-container') || document.body;
      if (navTarget === document.body) {
        // Attach as floating widget in top-right if no obvious container
        switcher.style.position = 'fixed';
        switcher.style.top = '10px';
        switcher.style.right = '10px';
        switcher.style.zIndex = '9999';
      }
      navTarget.appendChild(switcher);
    }
    // Clear and rebuild to avoid duplicates
    while (switcher.firstChild) switcher.removeChild(switcher.firstChild);

    const langs = [
      { code: "nl", flag: "nl.svg", label: "Nederlands" },
      { code: "de", flag: "de.svg", label: "Deutsch" },
      { code: "en", flag: "gb.svg", label: "English" }
    ];

    const onBlogPage = isBlogPage();
    const currentLang = getStoredLang();

    langs.forEach(({ code, flag, label }) => {
      const img = document.createElement('img');
      img.src = `/frontend/public/flags/${flag}`;
      img.alt = label;
      img.className = 'flag-icon';

      if (onBlogPage) {
        const blogUrl = getBlogUrlForLang(code);
        const active = isBlogPageActive(window.location.pathname, code);
        const a = document.createElement('a');
        a.href = blogUrl || '#';
        a.className = 'lang-btn' + (active ? ' active' : '');
        a.dataset.lang = code;
        a.setAttribute("aria-label", label);
        a.appendChild(img);
        switcher.appendChild(a);
      } else {
        const btn = document.createElement("button");
        btn.className = "lang-btn" + (currentLang === code ? ' active' : '');
        btn.dataset.lang = code;
        btn.setAttribute("aria-label", label);
        btn.appendChild(img);
        btn.addEventListener("click", () => {
          console.log(`Language switched to: ${code}`);
          storeLang(code);
          updateUrlForLang(code);
          applyTranslations(code);
          window.dispatchEvent(new CustomEvent('languageChanged', { detail: { language: code } }));
        });
        switcher.appendChild(btn);
      }
    });

    if (!onBlogPage) {
      document.querySelectorAll("#languageSwitcher .lang-btn").forEach(btn =>
        btn.classList.toggle("active", btn.dataset.lang === currentLang));
    }
  }

  /* ---------- 5. URL SYNC (for main pages with ?lang=) --------- */
  function updateUrlForLang(lang) {
    if (isBlogPage()) return;
    const url = new URL(window.location.href);
    if (lang === 'nl') {
      url.searchParams.delete('lang');
    } else {
      url.searchParams.set('lang', lang);
    }
    const newUrl = url.pathname + (url.search || '') + (url.hash || '');
    window.history.replaceState({}, '', newUrl);
  }

  /* ---------- 6. INITIALISE ON DOM READY ------------------- */
  function initializeTranslation() {
    console.log("Initializing translation system...");
    buildSwitcher();
    let lang = getStoredLang();
    if (isBlogPage()) {
      const pathname = (window.location.pathname || '').replace(/\/$/, '');
      if (/^\/en\/blog/.test(pathname)) lang = 'en';
      else if (/^\/de\/blog/.test(pathname)) lang = 'de';
      else lang = 'nl';
      storeLang(lang);
    } else {
      const urlLang = new URLSearchParams(window.location.search).get('lang');
      if (urlLang === 'en' || urlLang === 'de') {
        lang = urlLang;
        storeLang(lang);
      }
    }
    applyTranslations(lang);
  }

  // Initialize immediately if DOM is already ready, otherwise wait for DOMContentLoaded
  if (document.readyState === 'loading') {
    document.addEventListener("DOMContentLoaded", initializeTranslation);
  } else {
    // DOM is already ready
    initializeTranslation();
  }

  // Fallback initialization after a short delay to ensure everything is loaded
  setTimeout(() => {
    const switcher = document.getElementById("languageSwitcher");
    if (switcher && switcher.children.length === 0) {
      console.log("Fallback initialization: rebuilding language switcher");
      buildSwitcher();
    }
  }, 1000);

  /* --------- 7. Expose API for other scripts --------------- */
  window.getTranslation = (key) => {
    const lang = getStoredLang();
    const dict = t[lang] || t[DEFAULT_LANG];
    return dict[key] || key;
  };

  window.setLanguage = lang => {
    if (t[lang]) {
      storeLang(lang);
      updateUrlForLang(lang);
      applyTranslations(lang);
      // Dispatch event for other scripts to react to language changes
      window.dispatchEvent(new CustomEvent('languageChanged', { detail: { language: lang } }));
    }
  };

  // Expose buildSwitcher for manual debugging
  window.rebuildLanguageSwitcher = buildSwitcher;

  /** Re-apply [data-i18n] to the whole document (e.g. after injecting modal HTML). */
  window.refreshI18n = () => applyTranslations(getStoredLang());
})();
console.log("Translation.js IIFE executed");