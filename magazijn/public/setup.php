<?php
/**
 * Setup Helper Script
 * Run this once to create necessary directories and check configuration
 * DELETE THIS FILE AFTER SETUP FOR SECURITY
 */

// Check if directories exist and create them
$directories = [
    __DIR__ . '/assets/qr_codes',
];

foreach ($directories as $dir) {
    if (!file_exists($dir)) {
        if (mkdir($dir, 0755, true)) {
            echo "✓ Created directory: $dir\n";
        } else {
            echo "✗ Failed to create directory: $dir\n";
        }
    } else {
        echo "✓ Directory exists: $dir\n";
    }
    
    // Check if writable
    if (is_writable($dir)) {
        echo "✓ Directory is writable: $dir\n";
    } else {
        echo "⚠ Directory is not writable: $dir (chmod 755 recommended)\n";
    }
}

// Check database connection
require_once __DIR__ . '/Includes/db.php';
try {
    $testQuery = $db->query("SELECT 1");
    echo "✓ Database connection successful\n";
} catch (Exception $e) {
    echo "✗ Database connection failed: " . $e->getMessage() . "\n";
}

// Check PHP version
if (version_compare(PHP_VERSION, '7.4.0') >= 0) {
    echo "✓ PHP version OK: " . PHP_VERSION . "\n";
} else {
    echo "⚠ PHP version should be 7.4 or higher. Current: " . PHP_VERSION . "\n";
}

// Check required extensions
$requiredExtensions = ['pdo', 'pdo_mysql', 'gd', 'mbstring'];
foreach ($requiredExtensions as $ext) {
    if (extension_loaded($ext)) {
        echo "✓ Extension loaded: $ext\n";
    } else {
        echo "⚠ Extension not loaded: $ext\n";
    }
}

echo "\nSetup check complete!\n";
echo "⚠ Remember to DELETE this file (setup.php) after setup for security.\n";
?>

