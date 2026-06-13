<?php
/**
 * Returns Service + OfferCatalog JSON-LD for the botenverhuur page (inline script only; Google does not use script[src] for JSON-LD).
 */
function schema_botenverhuur_ld(): array
{
    $y = date('Y');
    $until = $y . '-12-31';
    $base = SITE_URL;

    $offer = function (string $name, string $desc, $category, string $imagePath, string $price) use ($base, $until) {
        $img = $base . $imagePath;
        return [
            '@type' => 'Offer',
            'itemOffered' => [
                '@type' => 'Product',
                'name' => $name,
                'description' => $desc,
                'category' => $category,
                'image' => $img,
                'offers' => [
                    '@type' => 'Offer',
                    'url' => $base . '/booking',
                    'price' => $price,
                    'priceCurrency' => 'EUR',
                    'priceValidUntil' => $until,
                    'availability' => 'https://schema.org/InStock',
                ],
            ],
            'price' => $price,
            'priceCurrency' => 'EUR',
            'priceValidUntil' => $until,
            'availability' => 'https://schema.org/InStock',
        ];
    };

    return [
        '@context' => 'https://schema.org',
        '@type' => ['Service', 'TouristAttraction'],
        '@id' => $base . '/botenverhuur',
        'serviceType' => 'Boat Rental',
        'name' => 'Nijenhuis Botenverhuur - Boot Huren Weerribben',
        'alternateName' => [
            'Electrosloep & Kano Verhuur Giethoorn',
            'Botenverhuur Wanneperveen',
            'Weerribben Bootverhuur',
            'Fluisterboot Verhuur Giethoorn',
            'Sloepverhuur Giethoorn',
            'Bootjes Verhuur Weerribben',
        ],
        'description' => 'Boot huren bij Giethoorn? Nijenhuis Botenverhuur verhuurt luxe sloepen, fluisterboten, zeilboten, kano\'s en SUP boards in de Weerribben. Vanaf €20/dag. Geen vaarbewijs nodig.',
        'url' => $base . '/botenverhuur',
        'image' => $base . '/frontend/Images/Boats/electrosloep-8/electrosloop-8.jpg',
        'provider' => [
            '@type' => 'LocalBusiness',
            'name' => 'Nijenhuis Botenverhuur',
            'telephone' => SITE_PHONE_LINK,
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => 'Veneweg 199',
                'addressLocality' => 'Wanneperveen',
                'postalCode' => '7946 LP',
                'addressRegion' => 'Overijssel',
                'addressCountry' => 'NL',
            ],
            'geo' => [
                '@type' => 'GeoCoordinates',
                'latitude' => 52.6972,
                'longitude' => 6.0780,
            ],
        ],
        'areaServed' => [
            '@type' => 'GeoCircle',
            'geoMidpoint' => [
                '@type' => 'GeoCoordinates',
                'latitude' => 52.6972,
                'longitude' => 6.0780,
            ],
            'geoRadius' => '25000',
        ],
        'containedInPlace' => [
            '@type' => 'NaturalFeature',
            'name' => 'Nationaal Park Weerribben-Wieden',
        ],
        'hasOfferCatalog' => [
            '@type' => 'OfferCatalog',
            'name' => 'Boten te huur',
            'itemListElement' => [
                $offer(
                    'Classic Tender 720',
                    'Ruime elektrische sloep voor maximaal 12 personen. Fluisterstil, comfortabel, met stuurwielbediening.',
                    ['Electrosloep', 'Fluisterboot'],
                    '/frontend/Images/Boats/tender-720/tender-720-10-12.jpg',
                    '230.00'
                ),
                $offer(
                    'Classic Tender 570',
                    'Compacte elektrische sloep voor maximaal 8 personen. Fluisterstil en wendbaar.',
                    ['Electrosloep', 'Fluisterboot'],
                    '/frontend/Images/Boats/tender-570/tender-570-8.jpg',
                    '200.00'
                ),
                $offer(
                    'Electrosloep 10 personen',
                    'Grote elektrische sloep voor maximaal 10 personen. Met zwemtrap en extra ruimte.',
                    ['Electrosloep', 'Fluisterboot'],
                    '/frontend/Images/Boats/electrosloep-10/electrosloop-10.jpg',
                    '200.00'
                ),
                $offer(
                    'Electrosloep 8 personen',
                    'Elektrische sloep voor 8 personen. Compact en eenvoudig te besturen.',
                    ['Electrosloep', 'Fluisterboot'],
                    '/frontend/Images/Boats/electrosloep-8/electrosloop-8.jpg',
                    '175.00'
                ),
                $offer(
                    'Electroboot 5 personen',
                    'Compacte elektrische boot voor stellen of klein gezin.',
                    ['Electrosloep', 'Fluisterboot'],
                    '/frontend/Images/Boats/electroboat-5.jpg',
                    '80.00'
                ),
                $offer(
                    'Zeilboot \'t Waar',
                    'Randmeer zeilboot voor 4-5 personen, optioneel met buitenboordmotor.',
                    ['Zeilboot'],
                    '/frontend/Images/Boats/zeilboot/zeilboot-4-5.jpg',
                    '70.00'
                ),
                $offer(
                    'Zeilpunter',
                    'Traditionele zeilpunter voor 3-4 personen, alleen op wind en zeil.',
                    ['Zeilboot', 'Punter'],
                    '/frontend/Images/Boats/sailpunter-3-4.jpg',
                    '40.00'
                ),
                $offer(
                    'Canadese Kano',
                    'Stabiele Canadese kano voor 3 personen. Inclusief peddels en vaarkaart.',
                    ['Kano'],
                    '/frontend/Images/Boats/canoe/canoe-3.jpg',
                    '25.00'
                ),
                $offer(
                    'Kajak 2-persoons',
                    'Sportieve kajak voor 2 personen. Inclusief peddels.',
                    ['Kajak'],
                    '/frontend/Images/Boats/kayak-2/kayak-2.jpg',
                    '25.00'
                ),
                $offer(
                    'Kajak 1-persoons',
                    'Eenpersoonskajak voor solo-avonturen op het water.',
                    ['Kajak'],
                    '/frontend/Images/Boats/Kayak-1/kayak-1.jpg',
                    '20.00'
                ),
                $offer(
                    'SUP Board',
                    'Stand-up paddleboard in de natuur. Inclusief peddel.',
                    ['SUP'],
                    '/frontend/Images/Boats/Sup/sub-1.jpg',
                    '35.00'
                ),
            ],
        ],
        'termsOfService' => 'Geen vaarbewijs vereist. Borg vereist. Minimum leeftijd 18 jaar.',
        'keywords' => 'botenverhuur, electrosloep, fluisterboot, sloepverhuur, bootje huren, kano, kajak, zeilboot, sup giethoorn, weerribben, giethoorn, wanneperveen',
    ];
}
