#!/usr/bin/env php
<?php
/**
 * Automatic Password Migration Script
 * 
 * Reads existing passwords from .env and generates hashes automatically.
 * Updates .env file with password hashes and adds CORS configuration.
 * 
 * Usage:
 *   php scripts/migrate-passwords-auto.php
 */

require_once __DIR__ . '/../components/data_access.php';
require_once __DIR__ . '/../components/security.php';

$envPath = __DIR__ . '/../.env';

if (!file_exists($envPath)) {
    echo "ERROR: .env file not found at: $envPath\n";
    echo "Please create .env file first or run the interactive migration script.\n";
    exit(1);
}

echo "========================================\n";
echo "Automatic Password Migration Tool\n";
echo "========================================\n\n";

// Load existing .env
loadEnvSafe($envPath);

// Read existing credentials
$adminUser = getenv('ADMIN_USERNAME') ?: ($_ENV['ADMIN_USERNAME'] ?? '');
$adminPass = getenv('ADMIN_PASSWORD') ?: ($_ENV['ADMIN_PASSWORD'] ?? '');
$employeeUser = getenv('EMPLOYEE_USERNAME') ?: ($_ENV['EMPLOYEE_USERNAME'] ?? '');
$employeePass = getenv('EMPLOYEE_PASSWORD') ?: ($_ENV['EMPLOYEE_PASSWORD'] ?? '');

// Check if hashes already exist
$adminHashExists = !empty(getenv('ADMIN_PASSWORD_HASH') ?: ($_ENV['ADMIN_PASSWORD_HASH'] ?? ''));
$employeeHashExists = !empty(getenv('EMPLOYEE_PASSWORD_HASH') ?: ($_ENV['EMPLOYEE_PASSWORD_HASH'] ?? ''));

if ($adminHashExists && $employeeHashExists) {
    echo "Password hashes already exist in .env file.\n";
    echo "Skipping password migration.\n\n";
} else {
    if (empty($adminUser) || empty($adminPass)) {
        echo "ERROR: ADMIN_USERNAME or ADMIN_PASSWORD not found in .env\n";
        echo "Please ensure these are set before running migration.\n";
        exit(1);
    }

    echo "Found existing credentials in .env file.\n";
    echo "Generating password hashes...\n\n";

    // Generate hashes
    $adminHash = hashPassword($adminPass);
    if (!$adminHash) {
        echo "ERROR: Failed to generate admin password hash!\n";
        exit(1);
    }

    $employeeHash = '';
    if (!empty($employeeUser) && !empty($employeePass)) {
        $employeeHash = hashPassword($employeePass);
        if (!$employeeHash) {
            echo "WARNING: Failed to generate employee password hash. Continuing with admin only.\n";
        }
    }

    echo "✓ Admin password hash generated\n";
    if (!empty($employeeHash)) {
        echo "✓ Employee password hash generated\n";
    }
    echo "\n";
}

// Read .env file content
$envContent = file_get_contents($envPath);
$lines = explode("\n", $envContent);
$updatedLines = [];
$adminHashAdded = false;
$employeeHashAdded = false;
$corsAdded = false;

foreach ($lines as $line) {
    $trimmed = trim($line);
    
    // Skip if already has hash (unless we're updating)
    if (strpos($trimmed, 'ADMIN_PASSWORD_HASH=') === 0) {
        if (!$adminHashExists && !empty($adminHash)) {
            // Update existing hash
            $updatedLines[] = "ADMIN_PASSWORD_HASH={$adminHash}";
            $adminHashAdded = true;
        } else {
            // Keep existing
            $updatedLines[] = $line;
            $adminHashAdded = true;
        }
        continue;
    }
    
    if (strpos($trimmed, 'EMPLOYEE_PASSWORD_HASH=') === 0) {
        if (!$employeeHashExists && !empty($employeeHash)) {
            // Update existing hash
            $updatedLines[] = "EMPLOYEE_PASSWORD_HASH={$employeeHash}";
            $employeeHashAdded = true;
        } else {
            // Keep existing
            $updatedLines[] = $line;
            $employeeHashAdded = true;
        }
        continue;
    }
    
    // Check for CORS configuration
    if (strpos($trimmed, 'CHATBOT_ALLOWED_ORIGINS=') === 0) {
        $corsAdded = true;
    }
    
    // Add hash after ADMIN_PASSWORD line
    if (strpos($trimmed, 'ADMIN_PASSWORD=') === 0 && !$adminHashAdded && !empty($adminHash)) {
        $updatedLines[] = $line;
        $updatedLines[] = "# Admin Password Hash (generated automatically)";
        $updatedLines[] = "ADMIN_PASSWORD_HASH={$adminHash}";
        $adminHashAdded = true;
        continue;
    }
    
    // Add hash after EMPLOYEE_PASSWORD line
    if (strpos($trimmed, 'EMPLOYEE_PASSWORD=') === 0 && !$employeeHashAdded && !empty($employeeHash)) {
        $updatedLines[] = $line;
        $updatedLines[] = "# Employee Password Hash (generated automatically)";
        $updatedLines[] = "EMPLOYEE_PASSWORD_HASH={$employeeHash}";
        $employeeHashAdded = true;
        continue;
    }
    
    $updatedLines[] = $line;
}

// Add CORS configuration if not present
if (!$corsAdded) {
    // Find a good place to add it (after chatbot config or at end)
    $insertIndex = count($updatedLines);
    for ($i = 0; $i < count($updatedLines); $i++) {
        if (stripos($updatedLines[$i], 'CHATBOT') !== false || 
            stripos($updatedLines[$i], 'VITE_CHATBOT') !== false) {
            $insertIndex = $i + 1;
            break;
        }
    }
    
    array_splice($updatedLines, $insertIndex, 0, [
        '',
        '# CORS Configuration for Chatbot API',
        '# Add your chatbot domain(s) separated by commas',
        'CHATBOT_ALLOWED_ORIGINS=https://your-chatbot-domain.com'
    ]);
}

// Write updated .env file
$backupPath = $envPath . '.backup.' . date('Y-m-d_H-i-s');
copy($envPath, $backupPath);
echo "✓ Created backup: " . basename($backupPath) . "\n";

file_put_contents($envPath, implode("\n", $updatedLines));

echo "\n========================================\n";
echo "Migration Complete!\n";
echo "========================================\n\n";

if ($adminHashAdded || $employeeHashAdded) {
    echo "✓ Password hashes added to .env\n";
    echo "✓ Plain text passwords kept for backward compatibility\n";
    echo "\n";
    echo "Next steps:\n";
    echo "1. Test login functionality\n";
    echo "2. Once confirmed working, you can remove ADMIN_PASSWORD and EMPLOYEE_PASSWORD from .env\n";
    echo "3. Restart your web server\n";
    echo "\n";
}

if (!$corsAdded || strpos(file_get_contents($envPath), 'your-chatbot-domain.com') !== false) {
    echo "⚠ CORS Configuration:\n";
    echo "   Please update CHATBOT_ALLOWED_ORIGINS in .env with your actual chatbot domain(s)\n";
    echo "   Example: CHATBOT_ALLOWED_ORIGINS=https://chatbot.example.com,https://api.example.com\n";
    echo "\n";
}

echo "Security Note: Never commit .env files to version control!\n";
echo "Backup saved to: " . basename($backupPath) . "\n";

?>
