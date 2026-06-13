#!/usr/bin/env php
<?php
/**
 * Generate BLOG_PASSWORD_HASH for .env
 * Usage: php scripts/generate-blog-password.php [password]
 * If no password given, prompts for one.
 */
if (php_sapi_name() !== 'cli') {
    die('Run from command line only.');
}
$password = $argv[1] ?? null;
if (!$password) {
    echo "Enter blog portal password: ";
    $password = trim(fgets(STDIN));
    if (empty($password)) {
        echo "No password given.\n";
        exit(1);
    }
}
require_once __DIR__ . '/../components/security.php';
$hash = hashPassword($password);
if (!$hash) {
    echo "Failed to generate hash.\n";
    exit(1);
}
echo "\nAdd to your .env file:\n\n";
echo "BLOG_USERNAME=blog-author\n";
echo "BLOG_PASSWORD_HASH=" . $hash . "\n\n";
echo "Or run: php -r \"echo password_hash('YOUR_PASSWORD', PASSWORD_ARGON2ID);\"\n";
