<?php
/**
 * FAQPage JSON-LD for blog articles with a Veelgestelde vragen section.
 *
 * @return array<string, mixed>|null
 */
function schema_blog_faq_ld(string $slug): ?array
{
    $faqs = [
        'motorboot-huren-giethoorn-weerribben' => [
            [
                'name' => 'Hoe ver kom ik met een elektrische motorboot?',
                'text' => "De accu's gaan een hele dag mee bij normaal gebruik. De route naar Giethoorn en terug (±20 km) is geen enkel probleem.",
            ],
            [
                'name' => 'Mag ik met een motorboot de grachten van Giethoorn in?',
                'text' => 'Ja, onze elektrische motorboten zijn toegestaan in de grachten van Giethoorn. Er geldt een maximumsnelheid van 6 km/u.',
            ],
            [
                'name' => 'Kan ik een motorboot huren voor 12 personen?',
                'text' => 'Ja, de Classic Tender 720 is geschikt voor maximaal 12 personen. Voor optimaal comfort raden we 10 personen aan.',
            ],
            [
                'name' => 'Wat als het regent?',
                'text' => 'Bij lichte regen kun je gewoon varen — neem regenkleding mee. Bij onweer of storm adviseren we om niet het water op te gaan. Bij extreme omstandigheden kun je gratis omboeken.',
            ],
            [
                'name' => 'Kan ik een motorboot huren in de Weerribben zonder naar Giethoorn te varen?',
                'text' => 'Absoluut. De Weerribben zelf zijn minstens zo mooi als Giethoorn. Vaar richting Belt-Schutsloot, de Kalenbergergracht of maak een rondvaart door het Nationaal Park.',
            ],
        ],
        'bootje-huren-drenthe' => [
            [
                'name' => 'Is de Weerribben-Wieden hetzelfde als Giethoorn?',
                'text' => 'Nee. Giethoorn is een dorp dat in de Weerribben-Wieden ligt. Het Nationaal Park is veel groter — 11.000 hectare. Giethoorn is het bekendste stukje, maar er is veel meer te ontdekken.',
            ],
            [
                'name' => 'Heb ik een vaarbewijs nodig om in Drenthe een boot te huren?',
                'text' => 'Nee, voor geen van onze boten heb je een vaarbewijs nodig. Je krijgt voor vertrek een persoonlijke instructie.',
            ],
            [
                'name' => 'Kan ik met kinderen varen in de Weerribben?',
                'text' => 'Absoluut. Onze electrosloepen zijn stabiel en veilig. Reddingsvesten voor kinderen zijn aanwezig. Veel gezinnen uit Drenthe komen hier voor een dagje uit.',
            ],
            [
                'name' => 'Zijn honden toegestaan op de boot?',
                'text' => "Huisdieren zijn welkom op de electrosloepen, kano's, de zeilpunter en de electroboot. Neem een handdoek mee waar je hond op kan liggen.",
            ],
            [
                'name' => 'Hoelang duurt een dagje bootje varen?',
                'text' => 'Een halve dag is 4 uur, een hele dag is 8 uur. Voor een bezoek aan Giethoorn raden we een hele dag aan. Voor Belt-Schutsloot of een rondvaart in de buurt is een halve dag voldoende.',
            ],
        ],
    ];

    if (!isset($faqs[$slug])) {
        return null;
    }

    $mainEntity = [];
    foreach ($faqs[$slug] as $faq) {
        $mainEntity[] = [
            '@type' => 'Question',
            'name' => $faq['name'],
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text' => $faq['text'],
            ],
        ];
    }

    return [
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => $mainEntity,
    ];
}
