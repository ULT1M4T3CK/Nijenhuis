#!/usr/bin/env php
<?php
/**
 * Security Update Script for .env File
 * 
 * This script helps update your .env file with the new security requirements:
 * - Converts plain text passwords to hashes
 * - Adds missing security variables
 * - Removes deprecated variables
 * 
 * Usage:
 *   php scripts/update-env-security.php
 */

require_once __DIR__ . '/../components/data_access.php';
require_once __DIR__ . '/../components/security.php';

$envPath = __DIR__ . '/../.env';

if (!file_exists($envPath)) {
    echo "ERROR: .env file not found at: $envPath\n";
    echo "Please create .env file first.\n";
    exit(1);
}

echo "========================================\n";
echo "Environment Security Update Tool\n";
echo "========================================\n\n";

// Backup .env file
$backupPath = $envPath . '.backup.' . date('Y-m-d_His');
if (copy($envPath, $backupPath)) {
    echo "✓ Created backup: $backupPath\n\n";
} else {
    echo "⚠ Warning: Could not create backup. Continuing anyway...\n\n";
}

// Load existing .env
loadEnvSafe($envPath);

// Read existing values
$adminUser = getenv('ADMIN_USERNAME') ?: ($_ENV['ADMIN_USERNAME'] ?? '');
$adminPass = getenv('ADMIN_PASSWORD') ?: ($_ENV['ADMIN_PASSWORD'] ?? '');
$adminHash = getenv('ADMIN_PASSWORD_HASH') ?: ($_ENV['ADMIN_PASSWORD_HASH'] ?? '');

$employeeUser = getenv('EMPLOYEE_USERNAME') ?: ($_ENV['EMPLOYEE_USERNAME'] ?? '');
$employeePass = getenv('EMPLOYEE_PASSWORD') ?: ($_ENV['EMPLOYEE_PASSWORD'] ?? '');
$employeeHash = getenv('EMPLOYEE_PASSWORD_HASH') ?: ($_ENV['EMPLOYEE_PASSWORD_HASH'] ?? '');

$mollieWebhookSecret = getenv('MOLLIE_WEBHOOK_SECRET') ?: ($_ENV['MOLLIE_WEBHOOK_SECRET'] ?? '');
$chatbotOrigins = getenv('CHATBOT_ALLOWED_ORIGINS') ?: ($_ENV['CHATBOT_ALLOWED_ORIGINS'] ?? '');
$appEnv = getenv('APP_ENV') ?: ($_ENV['APP_ENV'] ?? 'production');

$changes = [];

// Generate password hashes if needed
if (empty($adminHash) && !empty($adminPass)) {
    echo "Generating admin password hash...\n";
    $adminHash = hashPassword($adminPass);
    if ($adminHash) {
        $changes[] = "ADMIN_PASSWORD_HASH";
        echo "✓ Admin password hash generated\n";
    } else {
        echo "✗ Failed to generate admin password hash\n";
        exit(1);
    }
}

if (empty($employeeHash) && !empty($employeePass) && !empty($employeeUser)) {
    echo "Generating employee password hash...\n";
    $employeeHash = hashPassword($employeePass);
    if ($employeeHash) {
        $changes[] = "EMPLOYEE_PASSWORD_HASH";
        echo "✓ Employee password hash generated\n";
    } else {
        echo "✗ Failed to generate employee password hash\n";
    }
}

// Read .env file
$lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$updatedLines = [];
$adminHashAdded = false;
$employeeHashAdded = false;
$mollieSecretAdded = !empty($mollieWebhookSecret);
$chatbotOriginsAdded = !empty($chatbotOrigins);
$appEnvSet = ($appEnv === 'production');

foreach ($lines as $line) {
    $trimmed = trim($line);
    
    // Skip comments and empty lines (we'll preserve them)
    if (empty($trimmed) || strpos($trimmed, '#') === 0) {
        $updatedLines[] = $line;
        continue;
    }
    
    // Remove old ADMIN_PASSWORD line (if hash exists)
    if (strpos($trimmed, 'ADMIN_PASSWORD=') === 0 && strpos($trimmed, 'ADMIN_PASSWORD_HASH=') === false) {
        if (!empty($adminHash)) {
            echo "⚠ Removing plain text ADMIN_PASSWORD (replaced with hash)\n";
            continue; // Skip this line
        }
    }
    
    // Remove old EMPLOYEE_PASSWORD line (if hash exists)
    if (strpos($trimmed, 'EMPLOYEE_PASSWORD=') === 0 && strpos($trimmed, 'EMPLOYEE_PASSWORD_HASH=') === false) {
        if (!empty($employeeHash)) {
            echo "⚠ Removing plain text EMPLOYEE_PASSWORD (replaced with hash)\n";
            continue; // Skip this line
        }
    }
    
    // Update or add ADMIN_PASSWORD_HASH
    if (strpos($trimmed, 'ADMIN_PASSWORD_HASH=') === 0) {
        if (!empty($adminHash)) {
            $updatedLines[] = "ADMIN_PASSWORD_HASH={$adminHash}";
            $adminHashAdded = true;
            continue;
        }
    }
    
    // Update or add EMPLOYEE_PASSWORD_HASH
    if (strpos($trimmed, 'EMPLOYEE_PASSWORD_HASH=') === 0) {
        if (!empty($employeeHash)) {
            $updatedLines[] = "EMPLOYEE_PASSWORD_HASH={$employeeHash}";
            $employeeHashAdded = true;
            continue;
        }
    }
    
    // Check for existing security variables
    if (strpos($trimmed, 'MOLLIE_WEBHOOK_SECRET=') === 0) {
        $mollieSecretAdded = true;
    }
    if (strpos($trimmed, 'CHATBOT_ALLOWED_ORIGINS=') === 0) {
        $chatbotOriginsAdded = true;
    }
    if (strpos($trimmed, 'APP_ENV=') === 0) {
        $appEnvSet = true;
    }
    
    $updatedLines[] = $line;
}

// Add missing password hashes
if (!empty($adminHash) && !$adminHashAdded) {
    $updatedLines[] = "";
    $updatedLines[] = "# Admin Password Hash (generated automatically)";
    $updatedLines[] = "ADMIN_PASSWORD_HASH={$adminHash}";
    echo "✓ Added ADMIN_PASSWORD_HASH\n";
}

if (!empty($employeeHash) && !$employeeHashAdded && !empty($employeeUser)) {
    $updatedLines[] = "";
    $updatedLines[] = "# Employee Password Hash (generated automatically)";
    $updatedLines[] = "EMPLOYEE_PASSWORD_HASH={$employeeHash}";
    echo "✓ Added EMPLOYEE_PASSWORD_HASH\n";
}

// Add security warnings and missing variables
if (!$mollieSecretAdded) {
    $updatedLines[] = "";
    $updatedLines[] = "# SECURITY: Webhook secret is REQUIRED for production";
    $updatedLines[] = "# Get this from your Mollie dashboard";
    $updatedLines[] = "MOLLIE_WEBHOOK_SECRET=your_webhook_secret_here";
    echo "⚠ Added MOLLIE_WEBHOOK_SECRET placeholder (MUST be configured!)\n";
}

if (!$chatbotOriginsAdded) {
    $updatedLines[] = "";
    $updatedLines[] = "# SECURITY: Chatbot API origins (comma-separated)";
    $updatedLines[] = "# Only required if using chatbot API";
    $updatedLines[] = "# CHATBOT_ALLOWED_ORIGINS=https://your-chatbot-domain.com";
}

if (!$appEnvSet || $appEnv !== 'production') {
    $updatedLines[] = "";
    $updatedLines[] = "# Application Environment";
    $updatedLines[] = "APP_ENV=production";
    echo "✓ Set APP_ENV=production\n";
}

// Write updated .env file
$content = implode("\n", $updatedLines) . "\n";

if (file_put_contents($envPath, $content)) {
    echo "\n========================================\n";
    echo "✓ .env file updated successfully!\n";
    echo "========================================\n\n";
    
    echo "Summary of changes:\n";
    if (!empty($adminHash) && !$adminHashAdded) {
        echo "  ✓ Added ADMIN_PASSWORD_HASH\n";
    }
    if (!empty($employeeHash) && !$employeeHashAdded) {
        echo "  ✓ Added EMPLOYEE_PASSWORD_HASH\n";
    }
    if (!$mollieSecretAdded) {
        echo "  ⚠ Added MOLLIE_WEBHOOK_SECRET placeholder (MUST configure!)\n";
    }
    if (!$appEnvSet) {
        echo "  ✓ Set APP_ENV=production\n";
    }
    
    echo "\n⚠ IMPORTANT: Review and update the following:\n";
    echo "  1. MOLLIE_WEBHOOK_SECRET - Get from Mollie dashboard\n";
    echo "  2. CHATBOT_ALLOWED_ORIGINS - Add your chatbot domains\n";
    echo "  3. Remove any remaining ADMIN_PASSWORD or EMPLOYEE_PASSWORD lines\n";
    echo "\nBackup saved to: $backupPath\n";
    echo "Restart your web server after making changes.\n";
} else {
    echo "\n✗ ERROR: Failed to write .env file\n";
    echo "Please check file permissions.\n";
    exit(1);
}

?>
