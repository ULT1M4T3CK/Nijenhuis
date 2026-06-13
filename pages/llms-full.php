<?php
/**
 * llms-full.txt endpoint
 * Serves all site content as a single concatenated markdown document for AI crawlers.
 * Accessible at: /llms-full.txt (NL), /en/llms-full.txt (EN), /de/llms-full.txt (DE)
 *
 * Also includes published blog articles when available.
 */

$lang = $_GET['lang'] ?? 'nl';
if (!in_array($lang, ['nl', 'en', 'de'], true)) {
    $lang = 'nl';
}

$markdownDir = __DIR__ . '/../markdown/';

$pages = [
    'index'               => 'Homepage – Nijenhuis Botenverhuur',
    'botenverhuur'        => 'Botenverhuur',
    'vakantiehuis'        => 'Vakantiehuis',
    'camping'             => 'Camping',
    'te-koop'             => 'Te Koop',
    'vaarkaart'           => 'Vaarkaart',
    'contact'             => 'Contact',
    'veelgestelde-vragen' => 'Veelgestelde Vragen',
    'giethoorn'           => 'Giethoorn',
    'belt-schutsloot'     => 'Belt-schutsloot',
    'wanneperveen'        => 'Wanneperveen',
];

$labels = [
    'nl' => [
        'title'     => 'Nijenhuis Botenverhuur – Volledige Inhoud',
        'intro'     => 'Dit document bevat de volledige inhoud van nijenhuis-botenverhuur.com voor AI-systemen.',
        'generated' => 'Gegenereerd op',
        'blog'      => 'Blog Artikelen',
    ],
    'en' => [
        'title'     => 'Nijenhuis Boat Rental – Full Content',
        'intro'     => 'This document contains the full content of nijenhuis-botenverhuur.com for AI systems.',
        'generated' => 'Generated on',
        'blog'      => 'Blog Articles',
    ],
    'de' => [
        'title'     => 'Nijenhuis Bootsverleih – Vollständiger Inhalt',
        'intro'     => 'Dieses Dokument enthält den vollständigen Inhalt von nijenhuis-botenverhuur.com für KI-Systeme.',
        'generated' => 'Erstellt am',
        'blog'      => 'Blog-Artikel',
    ],
];
$L = $labels[$lang];

header('Content-Type: text/markdown; charset=utf-8');
header('Cache-Control: public, max-age=86400');

$latestMtime = 0;
foreach ($pages as $slug => $_) {
    $f = $markdownDir . $slug . '.md';
    if (file_exists($f)) {
        $latestMtime = max($latestMtime, filemtime($f));
    }
}
if ($latestMtime > 0) {
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s T', $latestMtime));
}

echo "# {$L['title']}\n\n";
echo "> {$L['intro']}\n";
echo "> {$L['generated']}: " . date('Y-m-d') . "\n\n";
echo "---\n\n";

foreach ($pages as $slug => $title) {
    $filePath = $markdownDir . $slug . '.md';
    if (file_exists($filePath)) {
        $content = file_get_contents($filePath);
        echo $content;
        echo "\n\n---\n\n";
    }
}

// Append published blog articles
$articlesFile = __DIR__ . '/../data/articles.json';
if (file_exists($articlesFile)) {
    $articles = json_decode(file_get_contents($articlesFile), true) ?: [];
    $blogEntries = [];
    foreach ($articles as $article) {
        if (empty($article['published'])) {
            continue;
        }
        $slug = $article['slug'] ?? '';
        if ($slug === '') {
            continue;
        }
        $articleDir = __DIR__ . '/../content/articles/' . $slug;
        $tryLangs = array_unique([$lang, 'nl', 'en', 'de']);
        foreach ($tryLangs as $tryLang) {
            $mdFile = $articleDir . '/' . $tryLang . '.md';
            if (file_exists($mdFile)) {
                $blogEntries[] = file_get_contents($mdFile);
                break;
            }
        }
    }
    if (!empty($blogEntries)) {
        echo "# {$L['blog']}\n\n---\n\n";
        foreach ($blogEntries as $entry) {
            echo $entry;
            echo "\n\n---\n\n";
        }
    }
}
