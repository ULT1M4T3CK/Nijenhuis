---
title: "FAQ Schema Templates — nijenhuis-botenverhuur.com"
description: "Ready-to-use FAQPage JSON-LD schema for pages that need it"
---

# FAQ Schema Templates

## Status per page

| Page | Has FAQ section? | Has FAQ schema? | Action needed |
|---|---|---|---|
| `/giethoorn` | ✅ Yes (8 Q&As) | ✅ Yes | None |
| `/wanneperveen` | ✅ Yes (6 Q&As) | ✅ Yes | None |
| `/botenverhuur` | ⚠️ Yes but BROKEN | ❌ No | Fix i18n + add schema |
| `/blog/sloep-huren-groep-*` | ✅ Yes | ❌ No | Add schema |
| `/blog/bootje-huren-weerribben-*` | ✅ Yes | ❌ No | Add schema |
| `/blog/fluisterboot-huren-*` | ✅ Yes | ❌ No | Add schema |

---

## ⚠️ BUG: /botenverhuur FAQ is broken

The FAQ section on `/botenverhuur` shows "Vraag" and "Antwoord" for every item.
The `data-i18n` attributes (`boats_faq_q1`, `boats_faq_a1`, etc.) are not being translated.

**Fix:** Add the actual Dutch FAQ content to your i18n translation file for keys:
`boats_faq_q1` through `boats_faq_q8` and `boats_faq_a1` through `boats_faq_a8`.

Suggested FAQ content for `/botenverhuur`:

```
Q1: Heb ik een vaarbewijs nodig?
A1: Nee, voor geen van onze boten is een vaarbewijs nodig. Alle boten zijn kleiner dan 15 meter en varen langzamer dan 20 km/u. Je krijgt voor vertrek een persoonlijke instructie.

Q2: Hoe ver kan ik varen met een elektrische boot?
A2: De accu's gaan een hele dag mee bij normaal gebruik. De route naar Giethoorn en terug (±20 km) is geen probleem. Bij aankomst controleren we altijd of de accu volledig is opgeladen.

Q3: Wat kost bootje huren bij Nijenhuis?
A3: Prijzen starten vanaf €20 per dag voor een kano of kajak. Electrosloepen zijn er vanaf €95 per halve dag. Bekijk de volledige prijslijst op onze boekingspagina.

Q4: Kan ik een boot huren voor 12 personen?
A4: Ja, onze Classic Tender 720 is geschikt voor maximaal 12 personen. Voor optimaal comfort raden we 10 personen aan. Bij grotere groepen kun je ook twee boten naast elkaar boeken.

Q5: Mag ik mijn hond meenemen?
A5: Huisdieren zijn toegestaan op de electrosloepen, kano's, de zeilpunter en de electroboot. Op de Classic Tenders zijn huisdieren niet toegestaan.

Q6: Wat als het slecht weer is?
A6: Bij lichte regen kun je gewoon varen — neem regenkleding mee. Bij onweer of storm adviseren we om niet het water op te gaan. Bij extreme omstandigheden kun je gratis omboeken naar een andere datum.

Q7: Hoe laat kan ik vertrekken?
A7: Je kunt vanaf 9:00 uur 's ochtends vertrekken. De laatste verhuurtijden zijn afhankelijk van het seizoen. In de zomer kun je tot 18:00 uur een boot ophalen voor een avondvaart.

Q8: Is er parkeergelegenheid?
A8: Ja, bij Nijenhuis Botenverhuur in Wanneperveen is parkeren helemaal gratis. Je parkeert direct naast de steiger, zodat je meteen het water op kunt.
```

---

## Schema template for `/botenverhuur`

Once you fix the FAQ content, add this `<script>` tag to the page `<head>`:

```json
{
    "@context": "https://schema.org",
    "@type": "FAQPage",
    "mainEntity": [
        {
            "@type": "Question",
            "name": "Heb ik een vaarbewijs nodig?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Nee, voor geen van onze boten is een vaarbewijs nodig. Alle boten zijn kleiner dan 15 meter en varen langzamer dan 20 km/u. Je krijgt voor vertrek een persoonlijke instructie."
            }
        },
        {
            "@type": "Question",
            "name": "Hoe ver kan ik varen met een elektrische boot?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "De accu's gaan een hele dag mee bij normaal gebruik. De route naar Giethoorn en terug (±20 km) is geen probleem. Bij aankomst controleren we altijd of de accu volledig is opgeladen."
            }
        },
        {
            "@type": "Question",
            "name": "Wat kost bootje huren bij Nijenhuis?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Prijzen starten vanaf €20 per dag voor een kano of kajak. Electrosloepen zijn er vanaf €95 per halve dag. Bekijk de volledige prijslijst op onze boekingspagina."
            }
        },
        {
            "@type": "Question",
            "name": "Kan ik een boot huren voor 12 personen?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Ja, onze Classic Tender 720 is geschikt voor maximaal 12 personen. Voor optimaal comfort raden we 10 personen aan. Bij grotere groepen kun je ook twee boten naast elkaar boeken."
            }
        },
        {
            "@type": "Question",
            "name": "Mag ik mijn hond meenemen?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Huisdieren zijn toegestaan op de electrosloepen, kano's, de zeilpunter en de electroboot. Op de Classic Tenders zijn huisdieren niet toegestaan."
            }
        },
        {
            "@type": "Question",
            "name": "Wat als het slecht weer is?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Bij lichte regen kun je gewoon varen — neem regenkleding mee. Bij onweer of storm adviseren we om niet het water op te gaan. Bij extreme omstandigheden kun je gratis omboeken naar een andere datum."
            }
        },
        {
            "@type": "Question",
            "name": "Hoe laat kan ik vertrekken?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Je kunt vanaf 9:00 uur 's ochtends vertrekken. De laatste verhuurtijden zijn afhankelijk van het seizoen. In de zomer kun je tot 18:00 uur een boot ophalen voor een avondvaart."
            }
        },
        {
            "@type": "Question",
            "name": "Is er parkeergelegenheid?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Ja, bij Nijenhuis Botenverhuur in Wanneperveen is parkeren helemaal gratis. Je parkeert direct naast de steiger, zodat je meteen het water op kunt."
            }
        }
    ]
}
```

---

## Schema template for blog posts

For each blog post that has a "Veelgestelde vragen" section, add the matching FAQPage schema. Here's the template for the motorboot blog post:

```json
{
    "@context": "https://schema.org",
    "@type": "FAQPage",
    "mainEntity": [
        {
            "@type": "Question",
            "name": "Hoe ver kom ik met een elektrische motorboot?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "De accu's gaan een hele dag mee bij normaal gebruik. De route naar Giethoorn en terug (±20 km) is geen enkel probleem."
            }
        },
        {
            "@type": "Question",
            "name": "Mag ik met een motorboot de grachten van Giethoorn in?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Ja, onze elektrische motorboten zijn toegestaan in de grachten van Giethoorn. Er geldt een maximumsnelheid van 6 km/u."
            }
        },
        {
            "@type": "Question",
            "name": "Kan ik een motorboot huren voor 12 personen?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Ja, de Classic Tender 720 is geschikt voor maximaal 12 personen. Voor optimaal comfort raden we 10 personen aan."
            }
        },
        {
            "@type": "Question",
            "name": "Wat als het regent?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Bij lichte regen kun je gewoon varen — neem regenkleding mee. Bij onweer of storm adviseren we om niet het water op te gaan. Bij extreme omstandigheden kun je gratis omboeken."
            }
        },
        {
            "@type": "Question",
            "name": "Kan ik een motorboot huren in de Weerribben zonder naar Giethoorn te varen?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Absoluut. De Weerribben zelf zijn minstens zo mooi als Giethoorn. Vaar richting Belt-Schutsloot, de Kalenbergergracht of maak een rondvaart door het Nationaal Park."
            }
        }
    ]
}
```

And for the Drenthe blog post:

```json
{
    "@context": "https://schema.org",
    "@type": "FAQPage",
    "mainEntity": [
        {
            "@type": "Question",
            "name": "Is de Weerribben-Wieden hetzelfde als Giethoorn?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Nee. Giethoorn is een dorp dat in de Weerribben-Wieden ligt. Het Nationaal Park is veel groter — 11.000 hectare. Giethoorn is het bekendste stukje, maar er is veel meer te ontdekken."
            }
        },
        {
            "@type": "Question",
            "name": "Heb ik een vaarbewijs nodig om in Drenthe een boot te huren?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Nee, voor geen van onze boten heb je een vaarbewijs nodig. Je krijgt voor vertrek een persoonlijke instructie."
            }
        },
        {
            "@type": "Question",
            "name": "Kan ik met kinderen varen in de Weerribben?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Absoluut. Onze electrosloepen zijn stabiel en veilig. Reddingsvesten voor kinderen zijn aanwezig. Veel gezinnen uit Drenthe komen hier voor een dagje uit."
            }
        },
        {
            "@type": "Question",
            "name": "Zijn honden toegestaan op de boot?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Huisdieren zijn welkom op de electrosloepen, kano's, de zeilpunter en de electroboot. Neem een handdoek mee waar je hond op kan liggen."
            }
        },
        {
            "@type": "Question",
            "name": "Hoelang duurt een dagje bootje varen?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Een halve dag is 4 uur, een hele dag is 8 uur. Voor een bezoek aan Giethoorn raden we een hele dag aan. Voor Belt-Schutsloot of een rondvaart in de buurt is een halve dag voldoende."
            }
        }
    ]
}
```
