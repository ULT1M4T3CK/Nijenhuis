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
      nav_faq: "Veelgestelde vragen",
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
      hero_h1: "Ontsnap naar het natuurparadijs",
      hero_h1_p: "Ervaar de schoonheid van het natuurgebied Weerribben met onze premium botenverhuur. Perfect voor families, vrienden en natuurliefhebbers.",
      hero_btn: "Beschikbaarheid controleren",
      intro_h2: "Ontsnap aan de dagelijkse sleur met dé botenverhuur van de Weerribben",
      intro_h2_p: "In onze drukke wereld snak iedereen naar rust. Laat files, stress en dagelijkse routine achter je – ontdek de Nationaal Park Weerribben-Wieden vanaf het water bij Nijenhuis Botenverhuur in Wanneperveen, dé bootverhuur van de Weerribben.",
      intro_h2_p2: "Verhuur boot voor quality time met familie of vrienden. Onze fluisterboten en electrosloepen glijden stil door smalste slootjes, weg van de massa. Creëer onvergetelijke momenten – perfect om even helemaal weg te zijn.",
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
      about_prices_desc: "Vanaf €20/dag<br><span class='fact-sub'>Geen vaarbewijs nodig</span>",

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
      services_p_1: "Elektrische boten, kano's, kajaks en SUP boards voor alle leeftijden en ervaringsniveaus.",
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
      /* boats page */
      boats_header_h1: "Boot en sloep huren in de Weerribben bij Giethoorn",
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
      boats_cta_h2: "Klaar voor het water?",
      boats_cta_p: "Reserveer vandaag nog je boot en geniet van een onvergetelijke dag op het water!",
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

      /* Giethoorn & Belt-schutsloot page H1s */
      giethoorn_title: "Giethoorn bezoeken - boot huren in het Venetië van het Noorden",
      belt_schutsloot_title: "Belt-schutsloot - verborgen parel nabij Giethoorn en Weerribben",

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
      checkout_total: "Totaal te betalen:",
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
      boats_intro_title: "Ontdek de Weerribben op jouw manier",
      boats_intro_text:
        "<p>Boot huren in de Weerribben? Ervaar de rust en ruimte van Nationaal Park Weerribben-Wieden vanaf het water. Bij Nijenhuis Botenverhuur in Wanneperveen vind je botenverhuur voor elke groep: van fluisterstille elektrische sloepen voor gezinnen tot kano verhuur Weerribben voor avonturiers.</p><p>Verhuur boot zonder vaarbewijs? Wij geven duidelijke uitleg, een routekaart en tips voor de mooiste vaarroute Weerribben naar Belterwiede. Ideaal voor dagtochten of sloep huren Overijssel!</p><p>Reserveer nu jouw boot en geniet direct van het water.</p>",
      seo_sloop_title: "Luxe Electrosloepen",
      seo_sloop_desc:
        "Geniet van ultieme stilte en comfort met onze luxe electrosloepen. Perfect voor wie een luxe sloep wil huren in de buurt van Giethoorn, zonder de drukte van toeristen en gecombineerd met een rustige tocht door Nationaal Park Weerribben-Wieden. Geen lawaai of uitstoot: alleen kabbelend water en vogelgezang. Met comfortabele kussens en eenvoudige stuurwielbediening – geen vaarbewijs nodig.",
      seo_sail_title: "Zeilboten & Punters Huren in Wanneperveen",
      seo_sail_desc:
        "Ontdek de watertraditie van Overijssel met onze zeilpunter – een authentieke ervaring op de Belterwiede en Weerribben; de zeilpunter vaart alleen op wind en zeil. Liever meer stabiliteit of bij weinig wind een hulpmotor? Kies dan onze Randmeer-zeilboot ('t Waar): die kun je optioneel laten uitrusten met een buitenboordmotor.",
      seo_active_title: "Kano, Kajak & SUP Huren in de Weerribben",
      seo_active_desc:
        "Peddel door de smalste slootjes waar motorboten niet kunnen komen – ontdek de Weerribben op z'n mooist en huur onze kajak in het hart van de Weerribben. Geniet van ruimte en stabiliteit met onze Canadese kanoverhuur in Wanneperveen, of probeer eens een SUP-verhuur in Overijssel voor een unieke workout op het water.",
      seo_active_footer: "Geen ervaring? Korte intro op locatie. Boek voor routes door Nationaal Park Weerribben-Wieden!",
      faq_title: "Veelgestelde vragen",

      /* booking page – missing keys */
      booking_options_title: "Extra opties",
      booking_option_motor: "Motor erbij huren?",

      /* checkout page – missing keys */
      checkout_home_btn: "🏠 Naar website",

      /* global – cart sidebar */
      cart_title: "🛒 Winkelwagen",
      cart_close_aria: "Sluiten",
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

      /* payment failure (inline JS strings) */
      payment_failure_status_failed: "Betaling status: {status}. De betaling is niet gelukt.",
      payment_failure_status_pending:
        "Betaling status: {status}. Je betaling wordt nog verwerkt. Check je e-mail voor updates.",

      /* FAQ page */
      faq_header_h1: "Veelgestelde vragen over boot huren in de Weerribben",
      faq_header_p: "Alles wat je moet weten over boot huren bij Nijenhuis",
      faq_intro_expanded: "Op deze pagina vind je antwoorden op de meest gestelde vragen over boot huren bij Nijenhuis Botenverhuur in de Weerribben. Onderwerpen die aan bod komen: prijzen per boottype, of je een vaarbewijs nodig hebt, openingstijden en reserveren, wat er bij de huur inbegrepen is, of je naar Giethoorn mag varen, en praktische zaken zoals betaling en huisdieren. Staat je vraag er niet bij? Neem gerust contact met ons op via het telefoonnummer of het contactformulier – we helpen je graag verder.",
      faq_page_html:
        "<div class='faq-intro' style='max-width: 800px; margin: 0 auto 2rem;'><p style='font-size: 1.1rem; line-height: 1.7; color: var(--text-secondary);'>Hier vind je antwoorden op de meest gestelde vragen over boot huren bij Nijenhuis Botenverhuur. Staat je vraag er niet bij? Neem gerust <a href='/contact'>contact</a> met ons op of bel <a href='tel:0522281528'>0522 281 528</a>.</p></div><div class='faq-list' style='max-width: 800px; margin: 0 auto;'><h2 style='margin-top: 2rem; color: var(--secondary-color);'>💰 Prijzen &amp; betaling</h2><div class='faq-item' style='margin-bottom: 1.5rem; padding: 1.5rem; background: #f8fafc; border-radius: 12px;'><h3 style='margin: 0 0 0.75rem; font-size: 1.15rem; color: var(--secondary-color);'>Wat kost het om een boot te huren bij Nijenhuis?</h3><div class='faq-answer' style='line-height: 1.7;'><p>De huurprijzen variëren per boottype:</p><ul style='margin: 0.5rem 0; padding-left: 1.5rem;'><li><strong>Kajak (1-2 personen):</strong> vanaf €35 per dag</li><li><strong>Canadese kano (3 personen):</strong> €45 per dag</li><li><strong>Electroboot (5 personen):</strong> €70 per dag</li><li><strong>Electrosloep (8 personen):</strong> €125 per dag</li><li><strong>Electrosloep (10-12 personen):</strong> €175-200 per dag</li><li><strong>Zeilboot (4-5 personen):</strong> €70-85 per dag</li></ul><p style='margin-top: 0.75rem;'>Bij meerdaagse verhuur krijg je korting. <a href='botenverhuur.php'>Bekijk alle prijzen →</a></p></div></div><div class='faq-item' style='margin-bottom: 1.5rem; padding: 1.5rem; background: #f8fafc; border-radius: 12px;'><h3 style='margin: 0 0 0.75rem; font-size: 1.15rem; color: var(--secondary-color);'>Hoe betaal ik?</h3><div class='faq-answer' style='line-height: 1.7;'><p>Je kunt betalen met:</p><ul style='margin: 0.5rem 0; padding-left: 1.5rem;'><li>Contant geld</li><li>Pinpas</li><li>Online via iDEAL bij het reserveren</li></ul><p>De borg (€50-150) betaal je ter plaatse <strong>contant</strong> en krijg je terug bij onbeschadigde retour.</p></div></div><div class='faq-item' style='margin-bottom: 1.5rem; padding: 1.5rem; background: #f8fafc; border-radius: 12px;'><h3 style='margin: 0 0 0.75rem; font-size: 1.15rem; color: var(--secondary-color);'>Moet ik borg betalen?</h3><div class='faq-answer' style='line-height: 1.7;'><p>Ja, voor de sloepen vragen wij een borg van €100 die <strong>contant</strong> betaald moet worden bij aankomst. Voor de zeilboot is een borg van €50 <strong>contant</strong> vereist. Voor kano's en SUPs is meestal geen borg vereist, maar vragen we wel een geldig legitimatiebewijs achter te laten.</p></div></div><h2 style='margin-top: 2.5rem; color: var(--secondary-color);'>📋 Praktische informatie</h2><div class='faq-item' style='margin-bottom: 1.5rem; padding: 1.5rem; background: #f8fafc; border-radius: 12px;'><h3 style='margin: 0 0 0.75rem; font-size: 1.15rem; color: var(--secondary-color);'>Heb ik een vaarbewijs nodig?</h3><div class='faq-answer' style='line-height: 1.7;'><p><strong>Nee</strong>, voor alle boten bij Nijenhuis Botenverhuur is geen vaarbewijs vereist. Onze elektrische sloepen en boten varen langzaam (maximaal 6 km/u) en zijn eenvoudig te bedienen.</p><p style='margin-top: 0.5rem;'>Voor vertrek krijg je een korte instructie over de bediening en de vaarregels in het Weerribbengebied.</p></div></div><div class='faq-item' style='margin-bottom: 1.5rem; padding: 1.5rem; background: #f8fafc; border-radius: 12px;'><h3 style='margin: 0 0 0.75rem; font-size: 1.15rem; color: var(--secondary-color);'>Moet ik vooraf reserveren?</h3><div class='faq-answer' style='line-height: 1.7;'><p><strong>Ja, reserveren is aan te raden</strong>, vooral in het hoogseizoen en in het weekend.</p><p>Zonder reservering is beschikbaarheid niet gegarandeerd. <a href='booking.php'>Reserveer online →</a></p></div></div><h2 style='margin-top: 2.5rem; color: var(--secondary-color);'>🌧️ Weer &amp; veiligheid</h2><div class='faq-item' style='margin-bottom: 1.5rem; padding: 1.5rem; background: #f8fafc; border-radius: 12px;'><h3 style='margin: 0 0 0.75rem; font-size: 1.15rem; color: var(--secondary-color);'>Wat gebeurt er bij slecht weer?</h3><div class='faq-answer' style='line-height: 1.7;'><p>Bij extreme weersomstandigheden (storm, onweer) kunnen we besluiten de boten niet uit te laten varen voor jouw veiligheid.</p><p>In dat geval kun je je reservering <strong>kosteloos verzetten</strong> naar een andere datum. Bij lichte regen kun je gewoon varen – een regenjas meenemen is dan aan te raden.</p></div></div></div><div style='text-align: center; margin-top: 3rem; padding: 2rem; background: var(--primary-color); border-radius: 16px; color: white;'><h2 style='color: white; margin-bottom: 1rem;'>Nog vragen?</h2><p style='margin-bottom: 1.5rem; opacity: 0.9;'>We helpen je graag verder!</p><div style='display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;'><a href='tel:0522281528' class='btn' style='background: white; color: var(--primary-color);'>📞 0522 281 528</a><a href='/contact' class='btn btn-outline' style='border-color: white; color: white;'>✉️ Contact</a></div></div>"
    },

    /* ---------- German – informal (“du”) ------------------- */
    de: {
      nav_opening: "Öffnungszeiten: 9:00 – 18:00 Uhr",
      nav_boats: "Bootsverleih",
      nav_house: "Ferienhaus",
      nav_forsale: "Zu verkaufen",
      nav_camping: "Camping",
      nav_chart: "Seekarte",
      nav_faq: "Häufige Fragen",
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
      hero_h1: "Entflieh ins Naturparadies",
      hero_h1_p: "Erlebe die Schönheit des Weerribben-Gebiets mit unserem Premium-Bootsverleih – perfekt für Familien, Freunde und Naturliebhaber.",
      hero_btn: "Verfügbarkeit prüfen",
      intro_h2: "Entfliehe dem Alltag mit dem Bootsverleih der Weerribben",
      intro_h2_p: "In unserer hektischen Welt sehnt sich jeder nach Ruhe. Lassen Sie Staus, Stress und die tägliche Routine hinter sich – entdecken Sie den Nationalpark Weerribben-Wieden vom Wasser aus bei Nijenhuis Botenverhuur in Wanneperveen, dem Bootsverleih der Weerribben.",
      intro_h2_p2: "Boot mieten für Quality Time mit Familie oder Freunden. Unsere Flüsterboote und Elektro-Sloopen gleiten leise durch die schmalsten Gräben, fernab der Massen. Schaffen Sie unvergessliche Momente – perfekt, um einfach mal ganz weg zu sein.",
      intro_h3: "Warum Nijenhuis wählen?",
      intro_h3_li1: "📍 Gelegen im Herzen des Weerribben-Naturschutzgebietes",
      intro_h3_li2: "🚤 Große Auswahl an Booten für alle Vorlieben",
      intro_h3_li3: "🌿 Verfügbarkeit milieubewusster elektrischer Booten",
      intro_h3_li4: "👨‍👩‍👧‍👦 Perfekt für Familien und Gruppen",
      intro_h3_li5: "💰 Konkurrenzstarke Preise für alle Budgets",
      intro_h3_li6: "📞 Persönliche Service und Unterstützung",
      intro_cta_p: "Für mehr Informationen, rufen Sie uns an 0522 - 281 528",
      intro_cta_p2: "Barzahlung und Kartenzahlung akzeptiert",

      /* About Grid */
      about_location_title: "Standort",
      about_location_desc: "Wanneperveen, Overijssel<br><span class='fact-sub'>10 km von Giethoorn</span>",
      about_season_title: "Saison",
      about_season_desc: "1. April – 31. Oktober<br><span class='fact-sub'>Täglich 09:00–18:00</span>",
      about_fleet_title: "Boote",
      about_fleet_desc: "25+ Boote<br><span class='fact-sub'>1 bis 12 Personen</span>",
      about_prices_title: "Preise",
      about_prices_desc: "Ab €20/Tag<br><span class='fact-sub'>Kein Führerschein nötig</span>",

      index_season_title: "Saisoncamping",
      index_season_dates: "Geöffnet vom 1. April bis 31. Oktober",
      index_season_status: "Jetzt für Reservierungen geöffnet",

      index_camping_title: "Saisoncamping in den Weerribben",
      index_camping_description: "Genießen Sie eine einzigartige Campingerfahrung mitten im wunderschönen Naturschutzgebiet Weerribben. Unser Saisoncamping ist vom 1. April bis 31. Oktober geöffnet und bietet eine ruhige, gemütliche Umgebung für Ihren Wohnwagen.",
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
      services_p_1: "Elektrische Booten, Kanus, Kajaks und SUP-Boards für alle Altersgruppen und Erfahrungsstufen.",
      services_btn_1: "Mehr erfahren",
      services_h3_2: "Ferienhaus",
      services_p_2: "Komfortable Ferienunterkünfte für Familien und Gruppen.",
      services_btn_2: "Mehr erfahren",
      services_h3_3: "Camping",
      services_p_3: "Saisoncamping vom 1. April bis 31. Oktober. Prachtige Campingplätze im Weerribben-Gebiet mit modernen Anlagen und atemberaubenden Aussichten.",
      services_btn_3: "Mehr erfahren",
      map_h2: "Unser Standort",
      footer_p: "Hier beginnt Ihr Abenteuer in den wunderschönen Weerribben!",
      footer_bottom: "&copy; 2026 <a href=\"https://ultimAItech.com\" target=\"_blank\" rel=\"noopener noreferrer\">ultimAItech</a>. Alle Rechte vorbehalten.",
      footer_company_name: "Nijenhuis Bootsverleih",
      footer_company_location: "Camping",
      footer_company_address: "Veneweg 199",
      footer_company_postal: "7946 LP Wanneperveen",
      footer_company_phone: "Tel: 0522 281 528",
      footer_company_kvk: "Kvk: 6769 7097",
      footer_company_btw: "Btw nr: NL857 1361 48 B01",
      footer_rights: "&copy; 2026 <a href=\"https://ultimAItech.com\" target=\"_blank\" rel=\"noopener noreferrer\">ultimAItech</a>. Alle Rechte vorbehalten.",
      /* boats page */
      boats_header_h1: "Boot und Sloep mieten in den Weerribben bei Giethoorn",
      boats_header_p: "Steig ein und entdecke das wunderschöne Weerribben-Naturschutzgebiet mit unseren Booten, Kanus und Kajaks!",
      boats_h2: "Unsere Boote",
      boats_intro: "Wir bieten eine große Auswahl an Booten für alle Vorlieben und Erfahrungsstufen",
      fleet_h2: "Unsere Flotte",
      fleet_p: "Wählen Sie aus unserem großen Angebot an elektrischen Schaluppen, Segelbooten und Kanus",
      fleet_hourly_note: "ℹ️ Hinweis: Für alle Boote ist es auch möglich, stundenweise statt tageweise zu mieten. Stundenmiete kann nur direkt vor Ort bei der Bootsvermietung gebucht werden, nicht online oder telefonisch. Besuchen Sie unsere Vermietungsstelle für Verfügbarkeit und direkte Buchung.",
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
      boats_cta_h2: "Bereit für das Wasser?",
      boats_cta_p: "Reserviere noch heute dein Boot und genieße einen unvergesslichen Tag auf dem Wasser!",
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
      booking_subtitle: "Ihr Boot ist verfügbar! Füllen Sie Ihre Daten aus, um Ihre Reservierung zu bestätigen.",
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
      booking_status_select: "Bitte wählen Sie die obigen Optionen",
      booking_status_available: "Verfügbar ✓",
      booking_your_info: "Ihre Daten",
      booking_name_label: "Vollständiger Name *",
      booking_email_label: "E-Mail-Adresse *",
      booking_phone_label: "Telefonnummer *",
      booking_address_label: "Adresse (optional)",
      booking_notes_label: "Besondere Wünsche oder Anmerkungen",
      booking_notes_placeholder: "Besondere Anforderungen...",
      booking_confirm_btn: "Buchung bestätigen",
      booking_back_btn: "Zurück zur Startseite",
      booking_processing: "Ihre Buchung wird bearbeitet...",
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
      house_why_p1: "Das Ferienhaus Belterwiede vereint das Beste aus zwei Welten: die Ruhe des Nationalparks Weerribben-Wieden und die Lebendigkeit von Giethoorn in der Nähe. Da das Haus direkt am Belterwijde-See liegt, steigen Sie buchstäblich aus dem Garten in Ihr Boot oder Kanu. Ideal für Familien, die segeln, angeln oder schwimmen möchten, ohne ständig ein- und ausladen zu müssen.",
      house_why_p2: "Das Haus ist das ganze Jahr über geöffnet, sodass Sie auch im Herbst und Winter Spaziergänge, Radtouren und die einzigartige Atmosphäre der Weerribben genießen können. Im Sommer ist es der perfekte Ausgangspunkt für Tagesausflüge nach Giethoorn, Belt-Schutsloot oder andere Dörfer in der Umgebung. Waterpark Belterwiede übernimmt die Reservierungen und Verwaltung des Ferienhauses.",
      house_surroundings_title: "Umgebung & Aktivitäten",
      house_surroundings_p1: "Vom Ferienhaus Belterwiede aus haben Sie direkten Zugang zum ausgedehnten Wassernetz der Weerribben. Segeln, Kanufahren oder Angeln – alles ist von Ihrem eigenen Steg aus möglich. Fahrrad- und Wanderwege führen durch die Region und verbinden Sie mit malerischen Dörfern wie Giethoorn, Wanneperveen und Blokzijl.",
      house_surroundings_p2: "In der Umgebung finden Sie Restaurants, Geschäfte und Attraktionen. Giethoorn liegt etwa 15 Fahrminuten entfernt und ist für seine Grachten und Reetdachhäuser bekannt. Für Familien gibt es Spielplätze und Strände am Wasser. Das Ferienhaus bietet Platz für bis zu zwölf Personen und genug Raum für einen entspannten Aufenthalt.",
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
      te_koop_intro_p1: "Bei Camping Nijenhuis stehen regelmäßig Chalets und Mobilheime zum Verkauf auf einem festen Stellplatz im Nationalpark Weerribben-Wieden. Ein eigenes Chalet oder Mobilheim auf unserem Campingplatz bedeutet einen festen Platz am Wasser, direkten Zugang zu den Wasserwegen nach Giethoorn und den Weerribben sowie eine ruhige Umgebung, in der Sie das ganze Jahr über die Natur genießen können.",
      te_koop_intro_p2: "Käufer erhalten einen Saisonplatz mit allen Annehmlichkeiten: Wasser, Strom, Kanalisation und eigenem Bootsanleger. Wohnwagen und Chalets dürfen das ganze Jahr über auf dem Platz stehen bleiben. Aufgrund der überschaubaren Größe des Campingplatzes ist das Angebot begrenzt – neue Angebote werden auf dieser Seite veröffentlicht, sobald sie verfügbar sind.",
      te_koop_why_h2: "Warum bei Nijenhuis kaufen?",
      te_koop_why_p1: "Camping Nijenhuis ist ein Familienunternehmen mit über 50 Jahren Erfahrung in den Weerribben. Unser Campingplatz bietet eine einzigartige Lage direkt am Wasser mit eigenen Bootsanlegern und allen modernen Einrichtungen. Chalets und Mobilheime, die hier zum Verkauf stehen, haben einen bewährten Stellplatz in einem begehrten Naturgebiet. Interessiert? Nehmen Sie Kontakt auf für Verfügbarkeit, Preise und die Möglichkeit, sich für zukünftige Angebote auf die Warteliste setzen zu lassen.",

      /* camping page */

      camping_title: "Saisoncamping in den Weerribben bei Giethoorn",
      camping_description: "Kommt ganz zur Ruhe beim Zelten mitten im wunderschönen Naturschutzgebiet De Weerribben.",

      camping_season_title: "Saisoncamping",
      camping_season_dates: "Geöffnet vom 1. April bis 31. Oktober",
      camping_season_status: "Jetzt für Reservierungen geöffnet",

      camping_overview_title: "Unser Campingplatz",
      camping_overview_description: "Ein ruhiger und gemütlicher Campingplatz mitten in der Natur",
      camping_overview_seasonal_title: "Seit über 50 Jahren ein Familienbegriff",
      camping_overview_seasonal_description: "Seit mehr als einem halben Jahrhundert ist Camping Nijenhuis ein verborgenes Juwel im Herzen der Weerribben. Was vor mehr als fünfzig Jahren aus Leidenschaft für Gastfreundschaft und Natur begann, hat sich zu einem einzigartigen Familiencampingplatz entwickelt, auf dem sich Generationen von Gästen zu Hause fühlen. Immer noch in Familienhand pflegen wir die persönliche Atmosphäre und die Ruhe, die unseren Campingplatz so besonders machen. <br><br> Unser Saisoncamping ist klein angelegt, sodass Sie maximale Privatsphäre und Platz genießen. Es ist der perfekte Ort, um dem Alltag zu entfliehen. Einzigartig an unserem Campingplatz ist, dass jeder Stellplatz über einen eigenen Bootsanleger verfügt, sodass Sie direkt von Ihrem Wohnwagen aus aufs Wasser können, um die wunderschönen Wasserwege von Giethoorn und den Weerribben zu erkunden.",
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
      camping_area_p1: "Camping Nijenhuis liegt mitten im Nationalpark Weerribben-Wieden, einem der schönsten Naturgebiete der Niederlande. Von Ihrem Stellplatz aus können Sie direkt aufs Wasser – kein Gedränge mit Anhängern oder Schleppen. Die Wasserwege verbinden Sie mit Giethoorn, Wanneperveen, Belt-Schutsloot und zahlreichen ruhigen Plätzen, an denen Sie nur die Vögel hören.",
      camping_area_p2: "Neben Bootfahren können Sie Rad fahren, wandern, angeln und schwimmen. Es gibt ausgeschilderte Routen für jede Entfernung. In der Umgebung finden Sie Restaurants, Museen und Bootsverleihe. Viele Gäste kombinieren ihren Aufenthalt mit einem Boot oder Kanu von Nijenhuis Bootsverleih – fragen Sie bei der Buchung nach den Möglichkeiten.",
      camping_tips_title: "Praktische Informationen zur Saisoncamping",
      camping_tips_p1: "Der Campingplatz ist vom 1. April bis 31. Oktober geöffnet. Wohnwagen dürfen das ganze Jahr über auf dem Platz stehen bleiben, sodass Sie in der Vor- und Nachsaison stressfrei kommen und gehen können. Jeder Stellplatz hat Wasser, Strom (mit eigenem Zähler), Kanalanschluss und einen eigenen Bootsanleger. Sanitäranlagen mit Duschen und Toiletten sind vorhanden, ebenso eine Waschmaschine und ein Trockner.",
      camping_tips_p2: "Aufgrund der überschaubaren Größe und der beliebten Lage empfehlen wir, rechtzeitig zu buchen. Rufen Sie uns für Verfügbarkeit und Preise an. Hunde sind willkommen, an der Leine auf dem Campingplatz. Die Atmosphäre ist ruhig und geeignet für Familien und Naturliebhaber, die Einfachheit und direkten Kontakt mit dem Wasser schätzen.",

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
      vaarkaart_interactive_map_description: "Entdecken Sie die schönsten Routen durch den Nationalpark Weerribben-Wieden. Diese Bootskarte zeigt alle Fahrtrouten im Gebiet.",
      vaarkaart_intro_extra: "Der Nationalpark Weerribben-Wieden ist das größte Niedermoorgebiet Nordwesteuropas. Das Gewässer besteht aus Seen, Gräben und Kanälen, die einst durch den Torfabbau entstanden. Heute ist es ein Paradies für Bootsfahrer mit ruhigen Routen, Schilfgürteln, Mooren und weiten Ausblicken. Von Nijenhuis Bootsverleih in Wanneperveen aus fahren Sie direkt ins Netz. Unten finden Sie die interaktive Karte, beliebte Routen und wichtige Fahrregeln.",
      vaarkaart_route_giethoorn_desc: "Die Route führt durch enge Gräben und breitere Kanäle zum Zentrum von Giethoorn. Unterwegs sehen Sie Reetdachhäuser, Brücken und typische Punter. In Giethoorn können Sie anlegen zum Spazierengehen oder Mittagessen. Planen Sie mindestens 2–3 Stunden für eine entspannte Hin- und Rückfahrt ein.",
      vaarkaart_route_weerribben_desc: "Diese Route führt tiefer in den Park, vorbei an Mooren, Schilffeldern und offenem Wasser. Sie können Eisvögel, Reiher, Libellen und verschiedene Wasservögel beobachten. Nehmen Sie ein Picknick mit und suchen Sie einen ruhigen Platz am Ufer. Ein Elektrosloop oder Kanu ist ideal für diese Route.",
      vaarkaart_route_wanneperveen_desc: "Eine ideale Route für eine erste Bekanntschaft mit dem Gebiet oder wenn Sie wenig Zeit haben. Sie fahren rund um Wanneperveen und genießen den Dorfblick und die umliegenden Gewässer. Geeignet für alle Bootstypen, einschließlich Kajaks und Kanus.",
      vaarkaart_interactive_map_map_title: "Naturschutzgebiet Weerribben - Interaktive Wasserkarte",
      vaarkaart_interactive_map_attribution_source: "Quelle:",
      vaarkaart_interactive_map_attribution_source_link: "Waterkaart.net",
      vaarkaart_interactive_map_attribution_suffix: "– Professionelle Bootskarten für niederländische Gewässer",
      vaarkaart_interactive_map_placeholder_title: "Interaktive Wasserkarte",
      vaarkaart_interactive_map_placeholder_description: "Für die aktuellsten und detailliertesten Wasserkarte des Weerribben-Gebiets besuchen Sie die professionelle Waterkaart der Niederlande.",
      vaarkaart_interactive_map_placeholder_button: "Öffne waterkaart.net",
      vaarkaart_interactive_map_footer_description: "Diese interaktive Wasserkarte wird von Waterkaart.net bereitgestellt. Für die aktuellsten Informationen und detaillierten Karten besuchen Sie deren Website.",
      vaarkaart_expand_map: "Karte vergrößern",
      vaarkaart_close_fullscreen: "Schließen",
      vaarkaart_view_osm: "OpenStreetMap",
      vaarkaart_disclaimer_title: "Haftungsausschluss:",
      vaarkaart_disclaimer_text: "Wir übernehmen keine Verantwortung für den Inhalt und die Richtigkeit dieser Karte. Lokale Gesetze, Regeln und Schilder entlang des Wassers müssen immer zuerst befolgt werden.",
      vaarkaart_footer_source: "Für detaillierte Wasserkarten und aktuelle Fahrinformationen besuchen Sie <a href='https://waterkaart.net/' target='_blank' rel='noopener noreferrer'>Waterkaart.net</a>.",

      giethoorn_title: "Giethoorn besuchen - Boot mieten im Venedig des Nordens",
      belt_schutsloot_title: "Belt-schutsloot - verborgenes Juwel bei Giethoorn und Weerribben",

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
      contact_title: "Kontakt und Anfahrt - Nijenhuis Wanneperveen",
      contact_p: "Kontaktieren Sie uns für Fragen, Reservierungen oder weitere Informationen",

      contact_h2: "Kontakt & Route",
      contact_h2_p: "Kontaktieren Sie Nijenhuis Bootsverleih in Wanneperveen. Hier finden Sie unsere Kontaktdaten und Wegbeschreibung.",
      contact_intro_extra: "Nijenhuis Bootsverleih liegt an der Veneweg 199 in Wanneperveen, am Rand des Nationalparks Weerribben-Wieden. Wir sind spezialisiert auf Bootsverleih – von Elektroslopen und Segelbooten bis zu Kajaks und SUP-Boards – und bieten außerdem Saisoncamping. Für Reservierungen, Fragen zu Preisen oder Verfügbarkeit können Sie uns anrufen oder während der Öffnungszeiten vorbeikommen. Es gibt kostenlose Parkmöglichkeiten vor Ort.",
      contact_route_h2: "Wegbeschreibung",
      contact_route_p1: "Wanneperveen liegt in der Kop van Overijssel, zwischen Meppel und Steenwijk. Kommen Sie mit dem Auto? Folgen Sie den Schildern nach Wanneperveen und suchen Sie die Veneweg – wir sind unter Nummer 199, direkt am Wasser. Von Giethoorn aus sind es etwa 15 Fahrminuten. Es gibt kostenlose Parkplätze vor Ort. Öffentliche Verkehrsmittel: Buslinie 77 hält in der Nähe von Wanneperveen; für genaue Haltestellen konsultieren Sie den Fahrplan.",
      contact_route_p2: "Während der Saison (1. April – 31. Oktober) sind wir täglich von 09:00 bis 18:00 Uhr geöffnet. Für Boote und Kanus empfehlen wir eine Vorausbuchung, besonders am Wochenende und in den Sommermonaten. Bei Ankunft können Sie direkt zu uns kommen für den Schlüssel, die Einweisung und die Routenkarte.",

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
      contact_call_p: "Für Fragen, Reservierungen oder weitere Informationen rufen Sie uns direkt an:",
      contact_call_button: "Jetzt Anrufen",
      contact_call_info_p: "Verfügbar: Täglich von 09:00 - 18:00 Uhr",
      contact_call_info_p2: "Saison: 1. April - 31. Oktober",

      contact_map_title: "Wo finden Sie uns?",
      contact_map_p: "Sehen Sie unseren Standort auf der Karte",
      /* payment pages */
      payment_success_title: "Zahlung Erfolgreich!",
      payment_success_subtitle: "Ihre Bootsbuchung wurde bestätigt. Sie erhalten in Kürze eine Bestätigungs-E-Mail.",
      payment_success_processing: "Ihre Zahlung wird bearbeitet...",
      payment_success_back: "Zurück zur Startseite",
      payment_success_contact: "Kontakt",
      payment_success_booking_id: "Buchungs-ID:",
      payment_success_date: "Datum:",
      payment_success_duration: "Dauer:",
      payment_success_boat_type: "Bootstyp:",
      payment_success_customer: "Kunde:",
      payment_success_status: "Status:",
      payment_success_price: "Preis:",
      payment_failure_title: "Zahlung Fehlgeschlagen",
      payment_failure_subtitle: "Leider konnte Ihre Zahlung nicht verarbeitet werden. Bitte versuchen Sie es erneut oder kontaktieren Sie uns.",
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
      checkout_empty_cart_title: "Ihr Warenkorb ist leer",
      checkout_empty_cart_desc: "Fügen Sie Boote zu Ihrem Warenkorb hinzu, um zur Kasse zu gehen.",
      checkout_empty_cart_btn: "Zum Bootsverleih",
      checkout_reservations_title: "Ihre Reservierungen",
      checkout_total: "Gesamtbetrag:",
      checkout_deposit_note: "Hinweis: Für die gemieteten Boote ist eine Kaution von €{amount} bei Ankunft in bar zu hinterlegen.",
      checkout_your_details: "Ihre Daten",
      checkout_name_label: "Vollständiger Name *",
      checkout_email_label: "E-Mail-Adresse *",
      checkout_phone_label: "Telefonnummer *",
      checkout_address_label: "Adresse (optional)",
      checkout_notes_label: "Anmerkungen (optional)",
      checkout_notes_placeholder: "Besondere Wünsche...",
      checkout_back_btn: "Zurück",
      checkout_pay_btn: "Bezahlen",
      checkout_loading: "Ihre Zahlung wird vorbereitet...",
      checkout_error_fields: "Bitte füllen Sie alle Pflichtfelder aus.",
      checkout_error_email: "Bitte geben Sie eine gültige E-Mail-Adresse ein.",
      checkout_error_general: "Ein Fehler ist aufgetreten. Bitte versuchen Sie es erneut.",
      checkout_day: "Tag",
      checkout_days: "Tage",

      /* botenverhuur page – SEO blocks (missing keys) */
      boats_intro_title: "Entdecke die Weerribben auf deine Art",
      boats_intro_text:
        "Boot mieten in den Weerribben? Erlebe die Ruhe und Weite des Nationalparks Weerribben-Wieden vom Wasser aus. Bei Nijenhuis Bootsverleih findest du das perfekte Boot für jede Gruppe – von flüsterleisen Elektro-Sloopen für einen entspannten Tagesausflug bis zu sportlichen Kanus für Abenteurer. Kein Bootsführerschein nötig: Wir geben dir eine klare Einweisung vor der Abfahrt und eine schöne Routenkarte.",
      seo_sloop_title: "Luxus-Elektro-Sloopen",
      seo_sloop_desc:
        "Genieße absolute Ruhe und Komfort. Unsere Elektro-Sloopen sind für die Natur gemacht: kein Lärm, keine Abgase – nur plätscherndes Wasser und Vogelstimmen. Die Boote sind mit bequemen Polstern ausgestattet und dank Steuerrad sehr einfach zu fahren.",
      seo_sail_title: "Segelboote & Punters",
      seo_sail_desc:
        "Giethoorn ist untrennbar mit dem Punter verbunden. Miete unseren traditionellen Segelpunter – er fährt ausschließlich unter Segeln. Mehr Komfort oder bei wenig Wind einen Hilfsmotor? Dann sind unsere stabilen Randmeer-Segelboote ('t Waar) die richtige Wahl; nur diese kannst du optional mit einem Außenbordmotor ausstatten, nicht den Segelpunter.",
      seo_active_title: "Kanus & SUP-Boards",
      seo_active_desc:
        "Paddle durch die schmalsten Gräben, wo Motorboote nicht hinkommen. Mit Kanu oder Kajak siehst du die Weerribben von ihrer schönsten Seite. Lust auf eine neue Herausforderung? Probiere unsere SUP-Boards (Stand Up Paddling) für ein einzigartiges Workout auf dem Wasser.",
      seo_active_footer: "Keine Erfahrung? Kurze Einführung vor Ort. Buche für Routen durch Nationalpark Weerribben-Wieden!",
      faq_title: "Häufige Fragen",

      /* booking page – missing keys */
      booking_options_title: "Zusatzoptionen",
      booking_option_motor: "Motor dazumieten?",

      /* checkout page – missing keys */
      checkout_home_btn: "🏠 Zur Website",

      /* global – cart sidebar */
      cart_title: "🛒 Warenkorb",
      cart_close_aria: "Schließen",
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

      /* payment failure (inline JS strings) */
      payment_failure_status_failed: "Zahlungsstatus: {status}. Die Zahlung ist fehlgeschlagen.",
      payment_failure_status_pending:
        "Zahlungsstatus: {status}. Deine Zahlung wird noch verarbeitet. Prüfe deine E-Mail für Updates.",

      /* FAQ page */
      faq_header_h1: "Häufige Fragen zum Boot mieten in den Weerribben",
      faq_header_p: "Alles, was du über das Boot-Mieten bei Nijenhuis wissen musst",
      faq_intro_expanded: "Auf dieser Seite findest du Antworten auf die häufigsten Fragen zum Bootsverleih bei Nijenhuis Bootsverleih in den Weerribben. Themen: Preise pro Bootstyp, ob du einen Bootsführerschein brauchst, Öffnungszeiten und Reservierung, was im Mietpreis enthalten ist, ob du nach Giethoorn fahren darfst sowie praktische Dinge wie Zahlung und Haustiere. Ist deine Frage nicht dabei? Nimm gerne Kontakt mit uns auf – wir helfen dir weiter.",
      faq_page_html:
        "<div class='faq-intro' style='max-width: 800px; margin: 0 auto 2rem;'><p style='font-size: 1.1rem; line-height: 1.7; color: var(--text-secondary);'>Hier findest du Antworten auf die häufigsten Fragen rund um den Bootsverleih bei Nijenhuis. Ist deine Frage nicht dabei? Nimm gerne <a href='/contact'>Kontakt</a> auf oder ruf uns an unter <a href='tel:0522281528'>0522 281 528</a>.</p></div><div class='faq-list' style='max-width: 800px; margin: 0 auto;'><h2 style='margin-top: 2rem; color: var(--secondary-color);'>💰 Preise &amp; Zahlung</h2><div class='faq-item' style='margin-bottom: 1.5rem; padding: 1.5rem; background: #f8fafc; border-radius: 12px;'><h3 style='margin: 0 0 0.75rem; font-size: 1.15rem; color: var(--secondary-color);'>Was kostet es, ein Boot zu mieten?</h3><div class='faq-answer' style='line-height: 1.7;'><p>Die Preise variieren je nach Bootstyp:</p><ul style='margin: 0.5rem 0; padding-left: 1.5rem;'><li><strong>Kajak (1-2 Personen):</strong> ab €35 pro Tag</li><li><strong>Kanadisches Kanu (3 Personen):</strong> €45 pro Tag</li><li><strong>Elektroboot (5 Personen):</strong> €70 pro Tag</li><li><strong>Elektrosloep (8 Personen):</strong> €125 pro Tag</li><li><strong>Elektrosloep (10-12 Personen):</strong> €175-200 pro Tag</li><li><strong>Segelboot (4-5 Personen):</strong> €70-85 pro Tag</li></ul><p style='margin-top: 0.75rem;'>Bei mehreren Tagen bekommst du Rabatt. <a href='botenverhuur.php'>Alle Preise ansehen →</a></p></div></div><h2 style='margin-top: 2.5rem; color: var(--secondary-color);'>📋 Praktische Infos</h2><div class='faq-item' style='margin-bottom: 1.5rem; padding: 1.5rem; background: #f8fafc; border-radius: 12px;'><h3 style='margin: 0 0 0.75rem; font-size: 1.15rem; color: var(--secondary-color);'>Brauche ich einen Führerschein?</h3><div class='faq-answer' style='line-height: 1.7;'><p><strong>Nein</strong>, ein Führerschein ist nicht erforderlich. Vor der Abfahrt bekommst du eine kurze Einweisung.</p></div></div></div><div style='text-align: center; margin-top: 3rem; padding: 2rem; background: var(--primary-color); border-radius: 16px; color: white;'><h2 style='color: white; margin-bottom: 1rem;'>Noch Fragen?</h2><p style='margin-bottom: 1.5rem; opacity: 0.9;'>Wir helfen dir gerne weiter!</p><div style='display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;'><a href='tel:0522281528' class='btn' style='background: white; color: var(--primary-color);'>📞 0522 281 528</a><a href='/contact' class='btn btn-outline' style='border-color: white; color: white;'>✉️ Kontakt</a></div></div>"
    },


    /* ---------- English ------------------------------------ */
    en: {
      nav_opening: "Opening hours: 9:00 am – 6:00 pm",
      nav_boats: "Boat Rental",
      nav_house: "Holiday Home",
      nav_forsale: "For Sale",
      nav_camping: "Camping",
      nav_chart: "Water Map",
      nav_faq: "FAQ",
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
      hero_h1: "Escape to Nature’s Paradise",
      hero_h1_p: "Experience the beauty of the Weerribben nature reserve with our premium boat rentals – perfect for families, friends and nature lovers.",
      hero_btn: "Check availability",
      intro_h2: "Escape the daily grind with the Weerribben's premier boat rental",
      intro_h2_p: "In our busy world, everyone craves peace. Leave traffic, stress and the daily routine behind – discover National Park Weerribben-Wieden from the water at Nijenhuis Botenverhuur in Wanneperveen, the Weerribben's boat rental.",
      intro_h2_p2: "Rent a boat for quality time with family or friends. Our whisper boats and electric sloops glide quietly through the narrowest canals, away from the crowds. Create unforgettable moments – perfect for getting away from it all.",
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
      about_prices_desc: "From €20/day<br><span class='fact-sub'>No license required</span>",

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
      services_p_1: "Electric boats, canoes, kayaks and SUP boards for all ages and experience levels.",
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
      /* boats page */
      boats_header_h1: "Rent boats and sloops in the Weerribben near Giethoorn",
      boats_header_p: "Hop on board and explore the stunning Weerribben area with our boats, canoes and kayaks!",
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
      boats_cta_h2: "Ready for the water?",
      boats_cta_p: "Book your boat today and enjoy an unforgettable day on the water!",
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
      checkout_total: "Total to pay:",
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
      boats_intro_title: "Discover the Weerribben your way",
      boats_intro_text:
        "Rent a boat in the Weerribben? Experience the peace and space of National Park Weerribben-Wieden from the water. At Nijenhuis Boat Rental you’ll find the perfect boat for any group - from whisper-quiet electric sloops for a relaxing day trip to sporty canoes for adventurers. No license required: we’ll give you a clear briefing before departure and a great route map.",
      seo_sloop_title: "Luxury electric sloops",
      seo_sloop_desc:
        "Enjoy ultimate quiet and comfort. Our electric sloops are designed for nature: no noise, no emissions - just rippling water and birdsong. They have comfortable cushions and are very easy to handle thanks to the steering wheel.",
      seo_sail_title: "Sailboats & punters",
      seo_sail_desc:
        "Giethoorn is closely connected to the punter. Rent our traditional zeilpunter — it is sailed under wind power only. Want more stability or an optional auxiliary motor in light wind? Choose our Randmeer sailboat ('t Waar); only that boat can be fitted with an outboard as an option, not the punter.",
      seo_active_title: "Canoes & SUP boards",
      seo_active_footer: "No experience? Short intro on location. Book for routes through National Park Weerribben-Wieden!",
      seo_active_desc:
        "Paddle through the narrowest canals where motorboats can’t go. With a canoe or kayak you’ll see the Weerribben at its best. Looking for a new challenge? Try our SUP boards (stand-up paddling) for a unique workout on the water.",
      faq_title: "Frequently asked questions",

      /* booking page – missing keys */
      booking_options_title: "Extra options",
      booking_option_motor: "Rent a motor too?",

      /* checkout page – missing keys */
      checkout_home_btn: "🏠 Back to website",

      /* global – cart sidebar */
      cart_title: "🛒 Cart",
      cart_close_aria: "Close",
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

      /* payment failure (inline JS strings) */
      payment_failure_status_failed: "Payment status: {status}. The payment failed.",
      payment_failure_status_pending:
        "Payment status: {status}. Your payment is still being processed. Check your email for updates.",

      /* FAQ page */
      faq_header_h1: "Frequently asked questions about boat rental in the Weerribben",
      faq_header_p: "Everything you need to know about renting a boat at Nijenhuis",
      faq_intro_expanded: "On this page you'll find answers to the most frequently asked questions about boat rental at Nijenhuis Boat Rental in the Weerribben. Topics covered include: prices per boat type, whether you need a licence, opening hours and reservations, what's included in the rental, whether you can sail to Giethoorn, and practical matters like payment and pets. Can't find your question? Feel free to contact us by phone or the contact form – we're happy to help.",
      faq_page_html:
        "<div class='faq-intro' style='max-width: 800px; margin: 0 auto 2rem;'><p style='font-size: 1.1rem; line-height: 1.7; color: var(--text-secondary);'>Here you’ll find answers to the most frequently asked questions about renting a boat at Nijenhuis. Can’t find your question? Feel free to <a href='/contact'>contact</a> us or call <a href='tel:0522281528'>0522 281 528</a>.</p></div><div class='faq-list' style='max-width: 800px; margin: 0 auto;'><h2 style='margin-top: 2rem; color: var(--secondary-color);'>💰 Prices &amp; payment</h2><div class='faq-item' style='margin-bottom: 1.5rem; padding: 1.5rem; background: #f8fafc; border-radius: 12px;'><h3 style='margin: 0 0 0.75rem; font-size: 1.15rem; color: var(--secondary-color);'>How much does it cost to rent a boat?</h3><div class='faq-answer' style='line-height: 1.7;'><p>Prices depend on the boat type:</p><ul style='margin: 0.5rem 0; padding-left: 1.5rem;'><li><strong>Kayak (1-2 persons):</strong> from €35 per day</li><li><strong>Canadian canoe (3 persons):</strong> €45 per day</li><li><strong>Electric boat (5 persons):</strong> €70 per day</li><li><strong>Electric sloop (8 persons):</strong> €125 per day</li><li><strong>Electric sloop (10-12 persons):</strong> €175-200 per day</li><li><strong>Sailboat (4-5 persons):</strong> €70-85 per day</li></ul><p style='margin-top: 0.75rem;'>Multi-day rentals may include a discount. <a href='botenverhuur.php'>See all prices →</a></p></div></div><div class='faq-item' style='margin-bottom: 1.5rem; padding: 1.5rem; background: #f8fafc; border-radius: 12px;'><h3 style='margin: 0 0 0.75rem; font-size: 1.15rem; color: var(--secondary-color);'>Do I need to pay a deposit?</h3><div class='faq-answer' style='line-height: 1.7;'><p>Yes, for sloops we require a deposit of €100 which must be paid <strong>in cash</strong> upon arrival. For the sailboat, a deposit of €50 <strong>in cash</strong> is required. For canoes and SUPs, a deposit is usually not required, but we do ask for a valid ID to be left behind.</p></div></div><h2 style='margin-top: 2.5rem; color: var(--secondary-color);'>📋 Practical info</h2><div class='faq-item' style='margin-bottom: 1.5rem; padding: 1.5rem; background: #f8fafc; border-radius: 12px;'><h3 style='margin: 0 0 0.75rem; font-size: 1.15rem; color: var(--secondary-color);'>Do I need a license?</h3><div class='faq-answer' style='line-height: 1.7;'><p><strong>No</strong>, you don’t need a license. Before departure, you’ll receive a short briefing.</p></div></div></div><div style='text-align: center; margin-top: 3rem; padding: 2rem; background: var(--primary-color); border-radius: 16px; color: white;'><h2 style='color: white; margin-bottom: 1rem;'>Still have questions?</h2><p style='margin-bottom: 1.5rem; opacity: 0.9;'>We’re happy to help!</p><div style='display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;'><a href='tel:0522281528' class='btn' style='background: white; color: var(--primary-color);'>📞 0522 281 528</a><a href='/contact' class='btn btn-outline' style='border-color: white; color: white;'>✉️ Contact</a></div></div>"
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

      if (htmlAllowedKeys.has(key) || key.startsWith('vaarkaart_')) { // Allow all vaarkaart keys for links/formatting
        // Sanitize HTML to prevent XSS attacks
        if (window.SecurityUtils && window.SecurityUtils.sanitizeHtml) {
          el.innerHTML = window.SecurityUtils.sanitizeHtml(text, {
            allowedTags: ['strong', 'em', 'b', 'i', 'u', 'br', 'p', 'a', 'span', 'div', 'h2', 'h3', 'ul', 'li'],
            allowedAttributes: {
              'a': ['href', 'target', 'rel'],
              '*': ['class']
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

    /* Update active state on flag buttons */
    document
      .querySelectorAll("#languageSwitcher .lang-btn")
      .forEach(btn => btn.classList.toggle("active", btn.dataset.lang === lang));
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

    langs.forEach(({ code, flag, label }) => {
      const btn = document.createElement("button");
      btn.className = "lang-btn";
      btn.dataset.lang = code;
      btn.setAttribute("aria-label", label);
      // Build flag element safely
      const img = document.createElement('img');
      img.src = `../frontend/public/flags/${flag}`;
      img.alt = label;
      img.className = 'flag-icon';

      // Debug: Log flag path and handle load errors
      console.log(`Loading flag: ${img.src}`);
      img.onerror = () => console.error(`Failed to load flag: ${img.src}`);
      img.onload = () => console.log(`Successfully loaded flag: ${img.src}`);
      while (btn.firstChild) btn.removeChild(btn.firstChild);
      btn.appendChild(img);
      btn.addEventListener("click", () => {
        console.log(`Language switched to: ${code}`);
        storeLang(code);
        applyTranslations(code);
        // Dispatch event for other scripts to react to language changes
        window.dispatchEvent(new CustomEvent('languageChanged', { detail: { language: code } }));
      });
      switcher.appendChild(btn);
    });

  }

  /* ---------- 5. INITIALISE ON DOM READY ------------------- */
  function initializeTranslation() {
    console.log("Initializing translation system...");
    buildSwitcher();
    applyTranslations(getStoredLang());
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

  /* --------- 6. Expose API for other scripts --------------- */
  window.getTranslation = (key) => {
    const lang = getStoredLang();
    const dict = t[lang] || t[DEFAULT_LANG];
    return dict[key] || key;
  };

  window.setLanguage = lang => {
    if (t[lang]) {
      storeLang(lang);
      applyTranslations(lang);
      // Dispatch event for other scripts to react to language changes
      window.dispatchEvent(new CustomEvent('languageChanged', { detail: { language: lang } }));
    }
  };

  // Expose buildSwitcher for manual debugging
  window.rebuildLanguageSwitcher = buildSwitcher;
})();
console.log("Translation.js IIFE executed");