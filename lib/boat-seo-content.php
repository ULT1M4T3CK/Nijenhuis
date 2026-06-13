<?php
/**
 * Server-rendered SEO body content for boat detail landing pages.
 * Uses optional seoContent from boats.json; otherwise builds from templates.
 */

/**
 * Build pricing tier text for a boat.
 */
function boat_seo_pricing_text(array $boat): string {
    $pricing = $boat['pricing'] ?? [];
    $pricingWithEngine = $boat['pricingWithEngine'] ?? [];
    $parts = [];
    $labels = ['1 dag', '2 dagen', '3 dagen', '4 dagen', '5 dagen', '6 dagen', '7 dagen'];
    foreach ($pricing as $i => $price) {
        $price = (int) $price;
        if ($price <= 0) {
            continue;
        }
        $label = $labels[$i] ?? ($i + 1) . ' dagen';
        $parts[] = $label . ': €' . $price;
    }
    if (empty($parts)) {
        $day = (int) ($boat['pricePerDay'] ?? 0);
        return $day > 0 ? 'Vanaf €' . $day . ' per dag.' : '';
    }
    $text = 'Meerdaagse tarieven: ' . implode(', ', $parts) . '.';
    if (!empty($pricingWithEngine)) {
        $engineParts = [];
        foreach ($pricingWithEngine as $i => $price) {
            $price = (int) $price;
            if ($price <= 0) {
                continue;
            }
            $label = $labels[$i] ?? ($i + 1) . ' dagen';
            $engineParts[] = $label . ': €' . $price;
        }
        if (!empty($engineParts)) {
            $text .= ' Met optionele motor: ' . implode(', ', $engineParts) . '.';
        }
    }
    return $text;
}

/**
 * Category-specific context paragraphs.
 */
function boat_seo_category_context(array $boat): string {
    $cat = $boat['category'] ?? '';
    $id = $boat['id'] ?? '';
    $name = $boat['name'] ?? 'Deze boot';

    if ($cat === 'electric' || strpos($id, 'electro') !== false || strpos($id, 'tender') !== false) {
        return '<p>Als <strong>fluisterboot</strong> vaart u geruisloos door Nationaal Park Weerribben-Wieden — ideaal voor gezinnen, vriendengroepen en bedrijfsuitjes. Geen vaarbewijs nodig; vóór vertrek krijgt u uitleg over bediening en vaarregels. Vanuit Wanneperveen bereikt u via smalle vaarten dorpen als Belt-schutsloot; sommige boten zijn ook geschikt voor tochten richting Giethoorn.</p>'
            . '<p>Elektrische sloepverhuur is de populairste manier om het gebied te ontdekken: geen uitlaatgassen, geen lawaai — alleen water, riet en vogelgeluiden. Reserveer online voor zekerheid, vooral in het weekend en hoogseizoen (juli–augustus).</p>';
    }
    if ($cat === 'sailing') {
        return '<p>Varen met de wind door de Weerribben is een authentieke ervaring. Onze zeilboten zijn geschikt voor zeilers met basiskennis; bij weinig wind kunt u bij sommige modellen kiezen voor een optionele motor. Geen massatoerisme — wel ruimte, rust en fraaie uitzichten over plassen en rietland.</p>'
            . '<p>Combineer uw vaartocht met een bezoek aan <a href="/giethoorn">Giethoorn</a> of het rustigere <a href="/belt-schutsloot">Belt-schutsloot</a>. Neem contact op als u twijfelt welke zeilboot past bij uw ervaring en gezelschap.</p>';
    }
    if ($cat === 'canoe') {
        return '<p>Peddelen door kreken en smalle slootjes brengt u dichter bij de natuur dan elke motorboot. Onze kano\'s en kajaks zijn licht, stabiel en eenvoudig te bedienen — perfect voor actieve dagen op het water. Geen vaarbewijs vereist.</p>'
            . '<p>Ideaal voor koppels, gezinnen met oudere kinderen of vrienden die samen willen sporten. Neem een picknick mee en zoek een rustig stukje oever in het Weerribbengebied. Uurverhuur is ter plaatse mogelijk; dagverhuur reserveert u het beste vooraf online.</p>';
    }
    if ($cat === 'sup') {
        return '<p>Stand-up paddelen (SUP) is een unieke manier om de Weerribben te verkennen: rechtop op het water, in uw eigen tempo. Geschikt voor wie wat waterervaring heeft en houdt van een actieve, rustige dag. Combineer SUP met een bezoek aan <a href="/wanneperveen">Wanneperveen</a> of peddel richting Giethoorn voor een onvergetelijke tocht.</p>'
            . '<p>Onze SUP-boards worden inclusief peddel verhuurd. Draag altijd een reddingsvest (inbegrepen) en respecteer andere vaartuigen in smalle vaarten.</p>';
    }
    return '<p>Huur ' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . ' bij Nijenhuis Botenverhuur aan de Veneweg 199 in Wanneperveen. Gratis parkeren, persoonlijke service en directe toegang tot de mooiste vaarwegen van Overijssel.</p>';
}

/**
 * Boat-specific extra paragraphs (unique per id).
 */
function boat_seo_id_extras(array $boat): string {
    $extras = [
        'classic-tender-720' => '<p>De Classic Tender 720 is onze grootste aluminium sloep — perfect voor verjaardagen, familiedagen of bedrijfsuitjes tot twaalf personen. Ruime zitbanken met kussens maken lange tochten comfortabel. Let op: met deze boot mag u <strong>niet door Giethoorn heen varen</strong>; wel zijn er prachtige routes door de Weerribben en naar Belt-schutsloot. Huisdieren zijn niet toegestaan aan boord.</p><p>De elektrische motor is krachtig genoeg voor een hele dag varen op één lading. Bij aankomst krijgt u een vaarkaart en uitleg over maximumsnelheid (6 km/u in bepaalde zones) en veilig varen met grote groepen.</p>',
        'classic-tender-570' => '<p>De Classic Tender 570 biedt acht zitplaatsen in een moderne aluminium romp. Wendbaar genoeg voor smallere vaarten, ruim genoeg voor een gezellige lunch aan boord. Populair bij gezinnen die comfort zoeken zonder meteen de grootste sloep te huren. Huisdieren zijn niet toegestaan.</p><p>Standaard uitgerust met een fluisterstille elektromotor — geen benzine, geen geluidsoverlast voor watervogels en medewatersporters. Reserveer vooraf in het hoogseizoen; op zonnige zaterdagen zijn sloepen snel volgeboekt.</p>',
        'electrosloop-10' => '<p>Onze electrosloep voor tien personen is de favoriete keuze voor grotere gezelschappen die samen willen varen, eten en genieten. Het brede dek biedt volop ruimte voor picknickmanden en kinderwagens (die u aan land achterlaat). <strong>Huisdieren zijn welkom</strong> op deze boot — ideaal als u de hond mee wilt nemen.</p><p>De boot is eenvoudig te besturen via een joystick of stuurwiel (afhankelijk van model). Geen vaarbewijs nodig. Populaire route: Wanneperveen → open water → Belt-schutsloot → terug via de Weerribben.</p>',
        'electrosloop-8' => '<p>Acht personen comfortabel, zonder de grootste sloep te hoeven huren — dat is de kracht van de electrosloep 8-persoons. Fijn voor twee gezinnen samen of een vriendengroep die rustig wil doorvaren. <strong>Huisdieren welkom.</strong></p><p>De boot combineert stabiliteit met wendbaarheid: geschikt voor beginners én ervaren schippers. Elektrisch varen betekent geen brandstofkosten ter plaatse en geen gedoe met tanks — opladen gebeurt tussen de huurperiodes door ons.</p>',
        'electroboat-5' => '<p>De compacte electroboot voor vijf personen is de instap voor wie zonder ervaring het water op wil. Overzichtelijk dashboard, eenvoudige bediening en geen borg — daardoor populair bij gezinnen met jonge kinderen. Perfect als u voor het eerst een boot huurt in de Weerribben.</p><p>De boot is wendbaar in smalle vaarten waar grotere sloepen niet komen. Ideaal voor een halve of hele dag; uurverhuur kan ter plaatse. Combineer met een bezoek aan onze <a href="/camping">camping</a> of <a href="/vakantiehuis">vakantiehuis</a> voor een compleet weekend.</p>',
        'sailboat-4-5' => '<p>Traditionele zeilbeleving voor vier tot vijf personen. Zonder motor vaart u puur op wind — voor ervaren zeilers. Optioneel kunt u een hulpmotor bijboeken voor extra zekerheid bij weinig wind of in drukkere vaartuigen.</p><p>Authentiek hout en klassieke lijnen maken deze boot bijzonder. Huisdieren niet toegestaan. Neem ruime kleding en zonnecrème mee; op het water is het vaak winderiger dan aan land. Vraag bij reservering naar actuele beschikbaarheid — het aanbod is beperkt.</p>',
        'sailpunter-3-4' => '<p>De zeilpunter is een echte klassieker: smal, traditioneel en wendbaar. Geschikt voor zeilers met ervaring; niet aanbevolen voor complete beginners. Intieme vaart voor drie tot vier personen die houden van authentiek zeilen in een weids natuurgebied.</p><p>Alleen beschikbaar op zondagen (check beschikbaarheid in de boekingskalender). Geen borg vereist. Een unieke manier om de Weerribben te ervaren, ver van drukke sloepenroutes.</p>',
        'canoe-3' => '<p>De Canadese kano biedt plaats aan drie peddelaars — ideaal voor gezinnen of vrienden die samen willen sporten. Stabiele romp, ruim opbergvak voor rugzakken en picknickspullen. Geen motor, geen geluid — alleen peddelen en natuur.</p><p>Populaire route: kronkelende kreken rond Wanneperveen, eventueel door naar rustigere delen van het park. Draag lichte kleding die nat mag worden. Geen borg; wel reddingsvesten verplicht (inbegrepen).</p>',
        'kayak-2' => '<p>Twee personen, één kajak — peddel in sync door de Weerribben. Sportiever dan een sloep, intiemer dan een grote boot. Geschikt voor stellen, vrienden of ouder met kind dat al kan peddelen.</p><p>Licht te tillen van en naar het water. Dagverhuur geeft u tijd voor een lange tocht; uurverhuur is ter plaatse beschikbaar. Neem water en zonnecrème mee — op het water brandt de zon sneller.</p>',
        'kayak-1' => '<p>Solo peddelen in uw eigen tempo: de eenpersoonskajak is voor wie zelfstandig wil varen en van actieve buitenlucht houdt. Wendbaar, licht en direct — geen wachten op anderen.</p><p>Ervaar de Weerribben van dichtbij: rietkragen, eenden, zwanen en stilte. Geen vaarbewijs nodig. Beginners kunnen starten in rustige plassen nabij onze steiger; ervaren peddelaars kunnen langere tochten plannen richting Giethoorn.</p>',
        'sup-board' => '<p>Stand-up paddleboard voor één persoon — balans, rust en een frisse kijk op het landschap. Onze boards zijn stabiel genoeg voor beginners met wat oefening. Inclusief peddel en reddingsvest.</p><p>Populair in combinatie met een dagje Giethoorn: peddel rustig door de vaarten (let op andere boten). Niet geschikt bij harde wind of onweer — wij adviseren bij twijfel ter plaatse. Geen borg vereist.</p>',
    ];
    $id = $boat['id'] ?? '';
    return $extras[$id] ?? '';
}

/**
 * Render full SEO HTML for a boat landing page.
 */
function boat_build_seo_html(array $boat): string {
    if (!empty($boat['seoContent'])) {
        $raw = trim((string) $boat['seoContent']);
        if (strpos($raw, '<') !== false) {
            return $raw;
        }
        $html = '';
        foreach (preg_split('/\n\s*\n/u', $raw) as $p) {
            $p = trim($p);
            if ($p !== '') {
                $html .= '<p>' . nl2br(htmlspecialchars($p, ENT_QUOTES, 'UTF-8')) . '</p>';
            }
        }
        return $html;
    }

    $name = htmlspecialchars($boat['name'] ?? 'Boot', ENT_QUOTES, 'UTF-8');
    $desc = trim($boat['description'] ?? '');
    $capacity = htmlspecialchars($boat['passengerCount'] ?? '', ENT_QUOTES, 'UTF-8');
    $priceDay = (int) ($boat['pricePerDay'] ?? 0);
    $deposit = (int) ($boat['deposit'] ?? 0);

    $html = '';
    if ($desc !== '') {
        foreach (preg_split('/\n\s*\n/u', $desc) as $p) {
            $p = trim($p);
            if ($p !== '') {
                $html .= '<p>' . htmlspecialchars($p, ENT_QUOTES, 'UTF-8') . '</p>';
            }
        }
    }

    $html .= boat_seo_id_extras($boat);
    $html .= boat_seo_category_context($boat);

    $html .= '<h2>Specificaties</h2><ul>';
    $html .= '<li><strong>Boot:</strong> ' . $name . '</li>';
    if ($capacity !== '') {
        $html .= '<li><strong>Capaciteit:</strong> ' . $capacity . '</li>';
    }
    if ($priceDay > 0) {
        $html .= '<li><strong>Vanaf:</strong> €' . $priceDay . ' per dag</li>';
    }
    if ($deposit > 0) {
        $html .= '<li><strong>Borg:</strong> €' . $deposit . ' (contant of pin, retour bij onbeschadigde inlevering)</li>';
    } else {
        $html .= '<li><strong>Borg:</strong> niet vereist</li>';
    }
    $html .= '<li><strong>Vaarbewijs:</strong> niet vereist</li>';
    $html .= '<li><strong>Locatie:</strong> Veneweg 199, Wanneperveen — gratis parkeren</li>';
    $html .= '</ul>';

    $pricingText = boat_seo_pricing_text($boat);
    if ($pricingText !== '') {
        $html .= '<h2>Tarieven</h2><p>' . htmlspecialchars($pricingText, ENT_QUOTES, 'UTF-8') . ' Bekijk alle <a href="/tarieven">tarieven</a> of vergelijk boten op onze <a href="/botenverhuur">botenverhuur pagina</a>.</p>';
    }

    $html .= '<h2>Reserveren</h2><p>Reserveer ' . $name . ' direct via het boekingsformulier op deze pagina. In het hoogseizoen raden wij online reserveren aan. Vragen? Bel <a href="tel:0522281528">0522 281 528</a> of bekijk onze <a href="/veelgestelde-vragen">veelgestelde vragen</a>.</p>';

    return $html;
}
