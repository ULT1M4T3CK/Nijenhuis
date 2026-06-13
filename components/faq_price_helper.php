<?php
/**
 * FAQ Price Helper - Nijenhuis Botenverhuur
 * Renders FAQ price and deposit content from boats.json (single source of truth).
 * Used by veelgestelde-vragen.php, waneeperveen.php, botenverhuur.php.
 */

if (!defined('NIJENHUIS_SITE')) {
    require_once __DIR__ . '/config.php';
}

/**
 * Load boats from boats.json.
 *
 * @return array
 */
function faq_load_boats() {
    require_once __DIR__ . '/data_paths.php';
    $boatsFile = nijenhuis_data_path('boats.json');
    if (!file_exists($boatsFile)) {
        return [];
    }
    $json = file_get_contents($boatsFile);
    $boats = json_decode($json, true);
    return is_array($boats) ? $boats : [];
}

/**
 * Get FAQ price list: array of {name, price, priceWithMotor, id, slug} for display.
 * Groups/orders boats for FAQ display.
 *
 * @param array $boats
 * @return array
 */
function faq_get_price_list($boats) {
    if (empty($boats)) {
        return [];
    }

    $items = [];
    $seenCategories = [];

    // Order: canoe/kayak (lowest first), electroboat, electrosloepen, sailboat, sailpunter, sup
    $categoryOrder = ['canoe', 'electric', 'sailing', 'sup'];
    $idOrder = [
        'kayak-1', 'kayak-2', 'canoe-3', 'sup-board',
        'electroboat-5', 'electrosloop-8', 'electrosloop-10',
        'classic-tender-570', 'classic-tender-720',
        'sailpunter-3-4', 'sailboat-4-5'
    ];

    foreach ($idOrder as $id) {
        foreach ($boats as $boat) {
            if (($boat['id'] ?? '') !== $id) {
                continue;
            }
            $price = (float)($boat['pricePerDay'] ?? 0);
            $priceWithMotor = null;
            if (!empty($boat['pricingWithEngine']) && isset($boat['pricingWithEngine'][0])) {
                $priceWithMotor = (float)$boat['pricingWithEngine'][0];
            }
            $items[] = [
                'id' => $boat['id'],
                'name' => $boat['name'] ?? '',
                'price' => $price,
                'priceWithMotor' => $priceWithMotor,
                'slug' => $boat['id'],
            ];
            break;
        }
    }

    // Fallback: add any boats not in idOrder
    foreach ($boats as $boat) {
        $id = $boat['id'] ?? '';
        if (empty($id)) continue;
        $found = false;
        foreach ($items as $item) {
            if ($item['id'] === $id) {
                $found = true;
                break;
            }
        }
        if (!$found) {
            $price = (float)($boat['pricePerDay'] ?? 0);
            $priceWithMotor = null;
            if (!empty($boat['pricingWithEngine']) && isset($boat['pricingWithEngine'][0])) {
                $priceWithMotor = (float)$boat['pricingWithEngine'][0];
            }
            $items[] = [
                'id' => $id,
                'name' => $boat['name'] ?? '',
                'price' => $price,
                'priceWithMotor' => $priceWithMotor,
                'slug' => $id,
            ];
        }
    }

    return $items;
}

/**
 * Get deposit range: {min, max} for boats with deposit > 0.
 *
 * @param array $boats
 * @return array{min: int, max: int}
 */
function faq_get_deposit_range($boats) {
    $deposits = [];
    foreach ($boats as $boat) {
        $d = (int)($boat['deposit'] ?? 0);
        if ($d > 0) {
            $deposits[] = $d;
        }
    }
    if (empty($deposits)) {
        return ['min' => 0, 'max' => 0];
    }
    return ['min' => min($deposits), 'max' => max($deposits)];
}

/**
 * Get boat-specific deposit for display (e.g. "sloepen €100, zeilboot €50").
 *
 * @param array $boats
 * @return array Array of {label, amount}
 */
function faq_get_deposit_breakdown($boats) {
    $sloepDeposit = null;
    $sailboatDeposit = null;
    foreach ($boats as $boat) {
        $d = (int)($boat['deposit'] ?? 0);
        if ($d <= 0) continue;
        $cat = $boat['category'] ?? '';
        $id = $boat['id'] ?? '';
        if ($cat === 'sailing' && strpos($id, 'sailboat') !== false) {
            $sailboatDeposit = $d;
        } elseif (in_array($cat, ['electric']) || strpos($id, 'electro') !== false || strpos($id, 'tender') !== false) {
            $sloepDeposit = $d; // Sloepen (electric boats)
        }
    }
    $result = [];
    if ($sloepDeposit !== null) {
        $result[] = ['label' => 'sloepen', 'amount' => $sloepDeposit];
    }
    if ($sailboatDeposit !== null) {
        $result[] = ['label' => 'zeilboot', 'amount' => $sailboatDeposit];
    }
    return $result;
}

/**
 * Render FAQ price list as HTML <ul> for Dutch.
 *
 * @param array $boats
 * @param string $lang 'nl'|'de'|'en'
 * @return string HTML
 */
function faq_render_price_list_html($boats, $lang = 'nl') {
    $items = faq_get_price_list($boats);
    if (empty($items)) {
        return '<ul style="margin: 0.5rem 0; padding-left: 1.5rem;"><li>Bekijk <a href="/botenverhuur">onze boten</a> voor actuele prijzen.</li></ul>';
    }

    $labels = [
        'nl' => [
            'from' => 'vanaf ',
            'per_day' => ' per dag',
            'without_motor' => ' (zonder motor)',
            'with_motor' => ' (met motor)',
            'or' => ' of ',
        ],
        'de' => [
            'from' => 'ab ',
            'per_day' => ' pro Tag',
            'without_motor' => ' (ohne Motor)',
            'with_motor' => ' (mit Motor)',
            'or' => ' oder ',
        ],
        'en' => [
            'from' => 'from ',
            'per_day' => ' per day',
            'without_motor' => ' (without motor)',
            'with_motor' => ' (with motor)',
            'or' => ' or ',
        ],
    ];
    $l = $labels[$lang] ?? $labels['nl'];

    $lines = [];
    foreach ($items as $item) {
        $name = htmlspecialchars($item['name']);
        $slug = htmlspecialchars($item['id']);
        $url = '/' . $slug . '#booking';
        $priceStr = '';
        if ($item['priceWithMotor'] !== null) {
            $priceStr = '€' . (int)$item['price'] . $l['without_motor'] . $l['or'] . '€' . (int)$item['priceWithMotor'] . $l['with_motor'];
        } else {
            $priceStr = '€' . (int)$item['price'] . $l['per_day'];
        }
        $lines[] = '<li><strong>' . $name . ':</strong> <a href="' . $url . '">' . $priceStr . '</a></li>';
    }

    return '<ul style="margin: 0.5rem 0; padding-left: 1.5rem;">' . implode('', $lines) . '</ul>';
}

/**
 * Get plain text for "Wat kost het om een boot te huren" JSON-LD answer.
 *
 * @param array $boats
 * @return string
 */
function faq_get_price_answer_text($boats) {
    $items = faq_get_price_list($boats);
    if (empty($items)) {
        return 'De huurprijzen variëren per boottype. Bekijk onze website voor actuele prijzen.';
    }

    $parts = [];
    foreach ($items as $item) {
        if ($item['priceWithMotor'] !== null) {
            $parts[] = $item['name'] . ' €' . (int)$item['price'] . '-' . (int)$item['priceWithMotor'] . ' per dag';
        } else {
            $parts[] = $item['name'] . ' €' . (int)$item['price'] . ' per dag';
        }
    }
    $text = 'De huurprijzen variëren per boottype. ' . implode(', ', $parts) . '. Bij meerdaagse verhuur krijg je korting. Een week huren is voordeliger dan 7 losse dagen.';
    return $text;
}

/**
 * Get deposit range snippet for FAQ answers ( Dutch ).
 *
 * @param array $boats
 * @return string e.g. "€50-100" or "€50" or "geen borg"
 */
function faq_get_deposit_snippet($boats) {
    $range = faq_get_deposit_range($boats);
    if ($range['min'] === 0 && $range['max'] === 0) {
        return 'meestal geen borg vereist';
    }
    if ($range['min'] === $range['max']) {
        return '€' . $range['min'];
    }
    return '€' . $range['min'] . '-' . $range['max'];
}

/**
 * Get lowest electrosloop price (for destination page snippets).
 *
 * @param array $boats
 * @return int|null
 */
function faq_get_lowest_electrosloop_price($boats) {
    $lowest = null;
    // Electrosloepen = 8+ person electric sloepen (excludes electroboat-5)
    $electroIds = ['electrosloop-8', 'electrosloop-10', 'classic-tender-570', 'classic-tender-720'];
    foreach ($boats as $boat) {
        if (in_array($boat['id'] ?? '', $electroIds)) {
            $p = (int)($boat['pricePerDay'] ?? 0);
            if ($p > 0 && ($lowest === null || $p < $lowest)) {
                $lowest = $p;
            }
        }
    }
    return $lowest;
}

/**
 * Get lowest canoe/kayak price.
 *
 * @param array $boats
 * @return int|null
 */
function faq_get_lowest_canoe_price($boats) {
    $lowest = null;
    $canoeIds = ['canoe-3', 'kayak-1', 'kayak-2'];
    foreach ($boats as $boat) {
        if (in_array($boat['id'] ?? '', $canoeIds)) {
            $p = (int)($boat['pricePerDay'] ?? 0);
            if ($p > 0 && ($lowest === null || $p < $lowest)) {
                $lowest = $p;
            }
        }
    }
    return $lowest;
}

/**
 * Get FAQ data for client-side rendering (boats, deposit breakdown, price list per lang).
 * Used to pass to JS for dynamic FAQ population when language switches.
 *
 * @param array $boats
 * @return array
 */
function faq_get_client_data($boats) {
    $items = faq_get_price_list($boats);
    $breakdown = faq_get_deposit_breakdown($boats);
    $range = faq_get_deposit_range($boats);

    $priceListByLang = [
        'nl' => faq_render_price_list_html($boats, 'nl'),
        'de' => faq_render_price_list_html($boats, 'de'),
        'en' => faq_render_price_list_html($boats, 'en'),
    ];

    $depositSloep = null;
    $depositZeilboot = null;
    foreach ($breakdown as $b) {
        if ($b['label'] === 'sloepen') $depositSloep = $b['amount'];
        if ($b['label'] === 'zeilboot') $depositZeilboot = $b['amount'];
    }

    $depositRange = ($range['min'] === $range['max'] && $range['min'] > 0)
        ? '€' . $range['min']
        : (($range['min'] > 0 || $range['max'] > 0)
            ? '€' . $range['min'] . '-' . $range['max']
            : '');

    return [
        'boats' => $boats,
        'priceListByLang' => $priceListByLang,
        'depositRange' => $depositRange,
        'depositSloep' => $depositSloep,
        'depositZeilboot' => $depositZeilboot,
    ];
}
