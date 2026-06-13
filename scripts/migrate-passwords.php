#!/usr/bin/env php
<?php
/**
 * Password Migration Script
 * 
 * This script helps migrate from plain text passwords to hashed passwords.
 * Run this script to generate password hashes for your .env file.
 * 
 * Usage:
 *   php scripts/migrate-passwords.php
 */

require_once __DIR__ . '/../components/security.php';

echo "========================================\n";
echo "Password Migration Tool\n";
echo "========================================\n\n";

echo "This script will help you migrate from plain text passwords to secure password hashes.\n\n";

// Get current passwords
$adminUser = readline("Enter admin username: ");
$adminPass = readline("Enter admin password: ");
$employeeUser = readline("Enter employee username (or press Enter to skip): ");
$employeePass = !empty($employeeUser) ? readline("Enter employee password: ") : '';

echo "\nGenerating password hashes...\n\n";

// Generate hashes
$adminHash = hashPassword($adminPass);
$employeeHash = !empty($employeePass) ? hashPassword($employeePass) : '';

if (!$adminHash) {
    echo "ERROR: Failed to generate admin password hash!\n";
    exit(1);
}

echo "========================================\n";
echo "Add these to your .env file:\n";
echo "========================================\n\n";

echo "# Admin Credentials (Hashed)\n";
echo "ADMIN_USERNAME={$adminUser}\n";
echo "ADMIN_PASSWORD_HASH={$adminHash}\n";
echo "\n";

if (!empty($employeeHash)) {
    echo "# Employee Credentials (Hashed)\n";
    echo "EMPLOYEE_USERNAME={$employeeUser}\n";
    echo "EMPLOYEE_PASSWORD_HASH={$employeeHash}\n";
    echo "\n";
}

echo "========================================\n";
echo "Migration Instructions:\n";
echo "========================================\n\n";
echo "1. Add the above lines to your .env file\n";
echo "2. Keep ADMIN_PASSWORD and EMPLOYEE_PASSWORD temporarily for backward compatibility\n";
echo "3. Test login functionality\n";
echo "4. Once confirmed working, remove ADMIN_PASSWORD and EMPLOYEE_PASSWORD from .env\n";
echo "5. Restart your web server\n\n";

echo "Security Note: Never commit .env files to version control!\n";

?>
