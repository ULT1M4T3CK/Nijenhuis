<?php
/**
 * FAQPage JSON-LD for /botenverhuur (matches visible FAQ accordion content).
 */
function schema_botenverhuur_faq_ld(): array
{
    return [
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => [
            [
                '@type' => 'Question',
                'name' => 'Heb ik een vaarbewijs nodig?',
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => 'Nee, voor geen van onze boten is een vaarbewijs nodig. Alle boten zijn kleiner dan 15 meter en varen langzamer dan 20 km/u. Je krijgt voor vertrek een persoonlijke instructie.',
                ],
            ],
            [
                '@type' => 'Question',
                'name' => 'Hoe ver kan ik varen met een elektrische boot?',
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => "De accu's gaan een hele dag mee bij normaal gebruik. De route naar Giethoorn en terug (±20 km) is geen probleem. Bij aankomst controleren we altijd of de accu volledig is opgeladen.",
                ],
            ],
            [
                '@type' => 'Question',
                'name' => 'Wat kost bootje huren bij Nijenhuis?',
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => 'Prijzen starten vanaf €20 per dag voor een kano of kajak. Electrosloepen zijn er vanaf €95 per halve dag. Bekijk de volledige prijslijst op onze boekingspagina.',
                ],
            ],
            [
                '@type' => 'Question',
                'name' => 'Kan ik een boot huren voor 12 personen?',
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => 'Ja, onze Classic Tender 720 is geschikt voor maximaal 12 personen. Voor optimaal comfort raden we 10 personen aan. Bij grotere groepen kun je ook twee boten naast elkaar boeken.',
                ],
            ],
            [
                '@type' => 'Question',
                'name' => 'Mag ik mijn hond meenemen?',
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => "Huisdieren zijn toegestaan op de electrosloepen, kano's, de zeilpunter en de electroboot. Op de Classic Tenders zijn huisdieren niet toegestaan.",
                ],
            ],
            [
                '@type' => 'Question',
                'name' => 'Wat als het slecht weer is?',
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => 'Bij lichte regen kun je gewoon varen — neem regenkleding mee. Bij onweer of storm adviseren we om niet het water op te gaan. Bij extreme omstandigheden kun je gratis omboeken naar een andere datum.',
                ],
            ],
            [
                '@type' => 'Question',
                'name' => 'Hoe laat kan ik vertrekken?',
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => "Je kunt vanaf 9:00 uur 's ochtends vertrekken. De laatste verhuurtijden zijn afhankelijk van het seizoen. In de zomer kun je tot 18:00 uur een boot ophalen voor een avondvaart.",
                ],
            ],
            [
                '@type' => 'Question',
                'name' => 'Is er parkeergelegenheid?',
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => 'Ja, bij Nijenhuis Botenverhuur in Wanneperveen is parkeren helemaal gratis. Je parkeert direct naast de steiger, zodat je meteen het water op kunt.',
                ],
            ],
        ],
    ];
}
