<?php
/**
 * Responsive image helper (M8 + M9)
 *
 * Generates <picture> elements with WebP sources and srcset/sizes.
 * Variants are expected to follow the naming convention produced by
 * scripts/convert-webp.js:
 *   original.jpg  →  original.webp, original-400w.webp, original-800w.webp, ...
 *                     original-400w.jpg, original-800w.jpg, ...
 */

/**
 * Build a <picture> element string with WebP + fallback srcset.
 *
 * @param string $src     Path relative to web root, e.g. 'frontend/Images/Boats/electrosloop-8.jpg'
 *                        May start with '/' (stripped) or be passed through assetPath().
 * @param string $alt     Alt text.
 * @param string $sizes   Sizes attribute, e.g. '(max-width: 768px) 100vw, 50vw'.
 * @param array  $attrs   Extra HTML attributes: loading, width, height, class, style, fetchpriority,
 *                        data-i18n-alt, etc.
 * @param int[]  $widths  Breakpoint widths to include in srcset.
 * @return string         HTML <picture> markup.
 */
function responsiveImage(string $src, string $alt, string $sizes = '100vw', array $attrs = [], array $widths = [400, 800, 1200]): string
{
    $src = ltrim($src, '/');

    $ext  = pathinfo($src, PATHINFO_EXTENSION);
    $base = substr($src, 0, -(strlen($ext) + 1));
    $webpFull = $base . '.webp';

    $webpSrcset = [];
    $origSrcset = [];
    $docRoot = rtrim(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT'] ?? ''), '/');

    foreach ($widths as $w) {
        $wFile = $base . '-' . $w . 'w.' . $ext;
        $wWebp = $base . '-' . $w . 'w.webp';
        if ($docRoot === '' || is_file($docRoot . '/' . $wWebp)) {
            $webpSrcset[] = '/' . $wWebp . ' ' . $w . 'w';
        }
        if ($docRoot === '' || is_file($docRoot . '/' . $wFile)) {
            $origSrcset[] = '/' . $wFile . ' ' . $w . 'w';
        }
    }

    if ($docRoot === '' || is_file($docRoot . '/' . $webpFull)) {
        $webpSrcset[] = '/' . $webpFull;
    }
    $origSrcset[] = '/' . $src;

    $webpSrcsetStr = implode(', ', $webpSrcset);
    $origSrcsetStr = implode(', ', $origSrcset);

    $attrStr = '';
    $defaults = ['loading' => 'lazy'];
    $merged = array_merge($defaults, $attrs);
    foreach ($merged as $k => $v) {
        if ($v === null || $v === false) continue;
        $attrStr .= ' ' . htmlspecialchars($k) . '="' . htmlspecialchars($v) . '"';
    }

    $html  = '<picture>';
    $html .= '<source type="image/webp" srcset="' . htmlspecialchars($webpSrcsetStr) . '" sizes="' . htmlspecialchars($sizes) . '">';
    $html .= '<img src="/' . htmlspecialchars($src) . '"';
    if (count($origSrcset) > 1) {
        $html .= ' srcset="' . htmlspecialchars($origSrcsetStr) . '"';
    }
    $html .= ' sizes="' . htmlspecialchars($sizes) . '"';
    $html .= ' alt="' . htmlspecialchars($alt) . '"';
    $html .= $attrStr;
    $html .= '>';
    $html .= '</picture>';

    return $html;
}
