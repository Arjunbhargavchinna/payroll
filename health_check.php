<?php
/**
 * Health Check Script
 * Verifies that all components are working properly
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>PayrollPro Health Check</h1>";

// Test 1: PHP Version
echo "<h2>1. PHP Environment</h2>";
echo "<p>PHP Version: " . PHP_VERSION . "</p>";
echo "<p>Server Software: " . $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown' . "</p>";

// Test 2: Required Extensions
echo "<h2>2. Required Extensions</h2>";
$extensions = ['pdo', 'pdo_mysql', 'json', 'mbstring', 'openssl'];
foreach ($extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "<p style='color: green;'>✓ {$ext}</p>";
    } else {
        echo "<p style='color: red;'>✗ {$ext}</p>";
    }
}

// Test 3: Database Connection
echo "<h2>3. Database Connection</h2>";
try {
    require_once 'config/database.php';
    $db = new Database();
    $result = $db->fetch("SELECT 1 as test");
    echo "<p style='color: green;'>✓ Database connection successful</p>";
    
    // Check tables
    $tables = $db->fetchAll("SHOW TABLES");
    echo "<p>Found " . count($tables) . " tables:</p>";
    echo "<ul>";
    foreach ($tables as $table) {
        $tableName = array_values($table)[0];
        echo "<li>{$tableName}</li>";
    }
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Database error: " . $e->getMessage() . "</p>";
}

// Test 4: File Permissions
echo "<h2>4. File Permissions</h2>";
$directories = ['uploads', 'logs', 'cache'];
foreach ($directories as $dir) {
    if (is_dir($dir) && is_writable($dir)) {
        echo "<p style='color: green;'>✓ {$dir} is writable</p>";
    } else {
        echo "<p style='color: red;'>✗ {$dir} is not writable</p>";
    }
}

// Test 5: Configuration
echo "<h2>5. Configuration</h2>";
if (file_exists('config/config.php')) {
    echo "<p style='color: green;'>✓ config.php exists</p>";
} else {
    echo "<p style='color: red;'>✗ config.php missing</p>";
}

if (file_exists('config/database.php')) {
    echo "<p style='color: green;'>✓ database.php exists</p>";
} else {
    echo "<p style='color: red;'>✗ database.php missing</p>";
}

// Test 6: Core Files
echo "<h2>6. Core Files</h2>";
$core_files = [
    'index.php',
    'app/core/Controller.php',
    'app/controllers/DashboardController.php',
    'app/controllers/PayrollController.php',
    'app/controllers/AttendanceController.php',
    'app/views/layout/main.php',
    'app/views/layout/sidebar.php',
    'css/app.css',
    'js/app.js'
];

foreach ($core_files as $file) {
    if (file_exists($file)) {
        echo "<p style='color: green;'>✓ {$file}</p>";
    } else {
        echo "<p style='color: red;'>✗ {$file}</p>";
    }
}

// Test 7: URL Routing
echo "<h2>7. URL Routing Test</h2>";
$base_url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']);
echo "<p>Base URL: {$base_url}</p>";

// Test 8: Session
echo "<h2>8. Session Test</h2>";
session_start();
if (session_status() === PHP_SESSION_ACTIVE) {
    echo "<p style='color: green;'>✓ Sessions are working</p>";
} else {
    echo "<p style='color: red;'>✗ Sessions are not working</p>";
}

// Test 9: CSS and JS
echo "<h2>9. Asset Loading</h2>";
$css_file = 'css/app.css';
$js_file = 'js/app.js';

if (file_exists($css_file)) {
    $css_size = filesize($css_file);
    echo "<p style='color: green;'>✓ CSS file exists ({$css_size} bytes)</p>";
} else {
    echo "<p style='color: red;'>✗ CSS file missing</p>";
}

if (file_exists($js_file)) {
    $js_size = filesize($js_file);
    echo "<p style='color: green;'>✓ JS file exists ({$js_size} bytes)</p>";
} else {
    echo "<p style='color: red;'>✗ JS file missing</p>";
}

// Test 10: Application Access
echo "<h2>10. Application Access</h2>";
echo "<p><a href='index.php' target='_blank'>Test Main Application</a></p>";
echo "<p><a href='setup_fix.php' target='_blank'>Run Setup Script</a></p>";

echo "<h2>Summary</h2>";
echo "<p>If all tests show green checkmarks, your PayrollPro installation is ready to use.</p>";
echo "<p><strong>Next Steps:</strong></p>";
echo "<ol>";
echo "<li>Run the setup script if you haven't already</li>";
echo "<li>Access the main application</li>";
echo "<li>Login with admin/admin123</li>";
echo "<li>Start configuring your payroll system</li>";
echo "</ol>";
?> 