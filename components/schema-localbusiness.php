<?php
/**
 * Schema.org Structured Data - LocalBusiness / BoatRental
 *
 * Outputs JSON-LD for Nijenhuis Botenverhuur. Included by head.php (shared head)
 * so it appears on all pages that use the shared head component.
 *
 * Requires config.php to be loaded before this file is included.
 */
$_schemaYear = date('Y');
$_schemaDateModified = date('Y-m-d');
$_reviewsFile = __DIR__ . '/../data/google-reviews.json';
$_reviewData = file_exists($_reviewsFile) ? json_decode(file_get_contents($_reviewsFile), true) : null;
?>
<script type="application/ld+json" id="schema-localbusiness">
{
    "@context": "https://schema.org",
    "@type": ["BoatRental", "LocalBusiness", "TouristAttraction"],
    "@id": "<?php echo SITE_URL; ?>/#organization",
    "dateModified": "<?php echo $_schemaDateModified; ?>",
    "availableLanguage": ["nl", "en", "de"],
    "name": "<?php echo SITE_NAME; ?>",
    "alternateName": [
        "Nijenhuis Bootverhuur Wanneperveen",
        "Bootje Huren Giethoorn",
        "Sloepverhuur Giethoorn"
    ],
    "description": "Botenverhuur bij Giethoorn. Bootjes huren, fluisterboot en sloepverhuur in Nationaal Park Weerribben-Wieden. Electrosloepen, zeilboten, kano's, kajaks en SUP boards. Inclusief seizoenscamping.",
    "speakable": {
        "@type": "SpeakableSpecification",
        "cssSelector": [".hero-text p", ".page-intro p:first-of-type", ".content-section p:first-of-type"]
    },
    "url": "<?php echo SITE_URL; ?>",
    "sameAs": [
        "https://www.google.com/maps/place/Veneweg+199,+7946+LP+Wanneperveen",
        "https://www.google.com/maps/place/?q=place_id:ChIJL2z_MFpJxkcRQJGDXYF7oJU",
        "https://www.facebook.com/NijenhuisBotenverhuur",
        "https://www.tripadvisor.nl/Attraction_Review-g1901647-d12925340-Reviews-Nijenhuis_Botenverhuur-Wanneperveen_Overijssel_Province.html"
    ],
    "hasMap": "https://www.google.com/maps/place/Veneweg+199,+7946+LP+Wanneperveen",
    "logo": "<?php echo SITE_URL; ?>/frontend/Images/logo-white.svg",
    "image": "<?php echo SITE_URL; ?>/frontend/Images/banner-img.jpg",
    "telephone": "<?php echo SITE_PHONE_LINK; ?>",
    "address": {
        "@type": "PostalAddress",
        "streetAddress": "<?php echo SITE_ADDRESS; ?>",
        "addressLocality": "Wanneperveen",
        "postalCode": "7946 LP",
        "addressRegion": "Overijssel",
        "addressCountry": "NL"
    },
    "geo": {
        "@type": "GeoCoordinates",
        "latitude": <?php echo SITE_LAT; ?>,
        "longitude": <?php echo SITE_LONG; ?>
    },
    "openingHoursSpecification": {
        "@type": "OpeningHoursSpecification",
        "dayOfWeek": [
            "Monday", "Tuesday", "Wednesday", "Thursday",
            "Friday", "Saturday", "Sunday"
        ],
        "opens": "09:00",
        "closes": "18:00",
        "validFrom": "<?php echo $_schemaYear; ?>-04-01",
        "validThrough": "<?php echo $_schemaYear; ?>-10-31"
    },
    "priceRange": "€€",
    "paymentAccepted": ["Cash", "Debit Card", "iDEAL", "Bancontact"],
    "currenciesAccepted": "EUR",
    "areaServed": {
        "@type": "GeoCircle",
        "geoMidpoint": {
            "@type": "GeoCoordinates",
            "latitude": <?php echo SITE_LAT; ?>,
            "longitude": <?php echo SITE_LONG; ?>
        },
        "geoRadius": "25000"
    },
    "hasOfferCatalog": {
        "@type": "OfferCatalog",
        "name": "Bootjes en Watervaartuigen",
        "itemListElement": [
            {
                "@type": "Offer",
                "itemOffered": {
                    "@type": "Service",
                    "name": "Electrosloep Verhuur",
                    "description": "Elektrische sloepen voor 5 tot 12 personen"
                }
            },
            {
                "@type": "Offer",
                "itemOffered": {
                    "@type": "Service",
                    "name": "Zeilboot Verhuur",
                    "description": "Zeilboten en zeilpunters voor 3-5 personen"
                }
            },
            {
                "@type": "Offer",
                "itemOffered": {
                    "@type": "Service",
                    "name": "Kano en Kajak Verhuur",
                    "description": "Canadese kano's en kajaks voor 1-3 personen"
                }
            },
            {
                "@type": "Offer",
                "itemOffered": {
                    "@type": "Service",
                    "name": "SUP Board Verhuur",
                    "description": "Stand-up paddleboards voor 1 persoon"
                }
            }
        ]
    },
    "containedInPlace": {
        "@type": "NaturalFeature",
        "name": "Nationaal Park Weerribben-Wieden",
        "description": "Het grootste aaneengesloten laagveenmoeras van Noordwest-Europa"
    },
    "slogan": "Huur een boot en ontdek de Weerribben",
    "foundingDate": "1970",
    "numberOfEmployees": {
        "@type": "QuantitativeValue",
        "value": "10"
    },
    "additionalProperty": [
        {
            "@type": "PropertyValue",
            "name": "Afstand tot Giethoorn",
            "value": "10",
            "unitCode": "KMT",
            "unitText": "km"
        },
        {
            "@type": "PropertyValue",
            "name": "Maximale vaarsnelheid in Giethoorn",
            "value": "6",
            "unitCode": "KMH",
            "unitText": "km/u"
        },
        {
            "@type": "PropertyValue",
            "name": "Vlootgrootte",
            "value": "25+",
            "unitText": "vaartuigen"
        },
        {
            "@type": "PropertyValue",
            "name": "Vaarbewijs vereist",
            "value": "Nee"
        },
        {
            "@type": "PropertyValue",
            "name": "Jaren familiebedrijf",
            "value": "50+",
            "unitText": "jaar"
        }
    ],
    "knowsAbout": ["botenverhuur", "fluisterboot", "sloepverhuur", "bootje huren giethoorn", "electrosloep", "Giethoorn", "Weerribben-Wieden", "watersport"]
<?php if (!empty($_reviewData['aggregateRating'])): ?>
    ,"aggregateRating": {
        "@type": "AggregateRating",
        "ratingValue": "<?php echo htmlspecialchars($_reviewData['aggregateRating']['ratingValue']); ?>",
        "reviewCount": "<?php echo htmlspecialchars($_reviewData['aggregateRating']['reviewCount']); ?>",
        "bestRating": "5"
    }
<?php endif; ?>
<?php if (!empty($_reviewData['reviews'])): ?>
    ,"review": <?php echo json_encode(array_map(function ($r) {
        return [
            '@type' => 'Review',
            'author' => ['@type' => 'Person', 'name' => $r['author_name'] ?? 'Klant'],
            'datePublished' => $r['date'] ?? '',
            'reviewBody' => $r['text'] ?? '',
            'reviewRating' => [
                '@type' => 'Rating',
                'ratingValue' => (string)($r['rating'] ?? 5),
                'bestRating' => '5'
            ]
        ];
    }, array_slice($_reviewData['reviews'], 0, 5)), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>

<?php endif; ?>
}
</script>
