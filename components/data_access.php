<?php
// components/data_access.php

require_once __DIR__ . '/data_paths.php';

/**
 * Robustly load environment variables from a .env file.
 * Handles quotes, comments, and edge cases better than simple splitting.
 * 
 * @param string $path Absolute path to .env file
 */
function loadEnvSafe($path) {
    if (!file_exists($path)) {
        return;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if (strpos($line, '#') === 0) continue; // Skip comments
        if (empty($line)) continue;

        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);

            // Remove surrounding quotes if present
            if (preg_match('/^"(.*)"$/', $value, $matches)) {
                $value = $matches[1];
            } elseif (preg_match("/^'(.*)'$/", $value, $matches)) {
                $value = $matches[1];
            }

            if (!getenv($key)) {
                putenv("$key=$value");
                $_ENV[$key] = $value;
            }
        }
    }

    // After env is loaded, migrate legacy JSON into data/ once per request (static guard inside).
    if (function_exists('nijenhuis_migrate_legacy_data_files')) {
        nijenhuis_migrate_legacy_data_files();
    }
}

/**
 * Load JSON data from a file safely.
 * 
 * @param string $filePath Absolute path to the JSON file
 * @return array The decoded data or empty array on failure
 */
function loadJsonSafe($filePath) {
    if (!file_exists($filePath)) {
        return [];
    }
    
    // We use a shared lock for reading to ensure we don't read while it's being written
    $fp = fopen($filePath, 'r');
    if (!$fp) return [];

    $data = [];
    if (flock($fp, LOCK_SH)) {
        $filesize = filesize($filePath);
        if ($filesize > 0) {
            $content = fread($fp, $filesize);
            $data = json_decode($content, true) ?: [];
        }
        flock($fp, LOCK_UN);
    }
    fclose($fp);
    return $data;
}

/**
 * Save data to a JSON file safely using exclusive locks.
 * 
 * @param string $filePath Absolute path to the JSON file
 * @param array $data The data to save
 * @return bool True on success, false on failure
 */
function saveJsonSafe($filePath, $data) {
    $fp = fopen($filePath, 'c+'); // Open for reading and writing; place pointer at beginning
    if (!$fp) {
        error_log("saveJsonSafe: Could not open file $filePath");
        return false;
    }

    $result = false;
    // Acquire exclusive lock
    if (flock($fp, LOCK_EX)) {
        // Truncate file to 0 length
        ftruncate($fp, 0);
        rewind($fp);
        
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($json !== false) {
            $written = fwrite($fp, $json);
            if ($written !== false) {
                $result = true;
            } else {
                error_log("saveJsonSafe: Failed to write to file $filePath");
            }
        } else {
            error_log("saveJsonSafe: JSON encoding failed: " . json_last_error_msg());
        }
        
        fflush($fp); // Flush output before releasing lock
        flock($fp, LOCK_UN);
    } else {
        error_log("saveJsonSafe: Could not acquire lock for $filePath");
    }
    
    fclose($fp);
    return $result;
}
?>
