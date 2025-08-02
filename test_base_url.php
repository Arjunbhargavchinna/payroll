<?php
/**
 * Test BASE_URL Configuration
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>BASE_URL Test</h1>";

// Load the config
require_once 'config/config.php';

echo "<h2>Current Configuration</h2>";
echo "<p><strong>Protocol:</strong> " . (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . "</p>";
echo "<p><strong>Host:</strong> " . ($_SERVER['HTTP_HOST'] ?? 'localhost') . "</p>";
echo "<p><strong>Script Name:</strong> " . ($_SERVER['SCRIPT_NAME'] ?? '') . "</p>";

// Test the BASE_URL construction
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';

$basePath = str_replace('/index.php', '', $scriptName);
$basePath = str_replace('/public', '', $basePath);

// Ensure basePath starts with / if it's not empty
if (!empty($basePath) && $basePath[0] !== '/') {
    $basePath = '/' . $basePath;
}

$testBaseUrl = $protocol . '://' . $host . $basePath;

echo "<h2>BASE_URL Construction</h2>";
echo "<p><strong>Base Path:</strong> {$basePath}</p>";
echo "<p><strong>Test BASE_URL:</strong> {$testBaseUrl}</p>";
echo "<p><strong>Defined BASE_URL:</strong> " . BASE_URL . "</p>";

echo "<h2>Test Links</h2>";
echo "<p><a href='" . BASE_URL . "/dashboard' target='_blank'>Test Dashboard Link</a></p>";
echo "<p><a href='" . BASE_URL . "/payroll' target='_blank'>Test Payroll Link</a></p>";
echo "<p><a href='" . BASE_URL . "/attendance' target='_blank'>Test Attendance Link</a></p>";

echo "<h2>Expected vs Actual</h2>";
echo "<p><strong>Expected:</strong> ctoadmin.itiltd.in/cams/parollpro</p>";
echo "<p><strong>Actual:</strong> " . BASE_URL . "</p>";

if (BASE_URL === 'http://ctoadmin.itiltd.in/cams/parollpro' || BASE_URL === 'https://ctoadmin.itiltd.in/cams/parollpro') {
    echo "<p style='color: green;'>✓ BASE_URL is correct!</p>";
} else {
    echo "<p style='color: red;'>✗ BASE_URL is incorrect!</p>";
    echo "<p>Please check the configuration in config/config.php</p>";
}

echo "<h2>Debug Information</h2>";
echo "<pre>";
echo "SERVER variables:\n";
echo "HTTP_HOST: " . ($_SERVER['HTTP_HOST'] ?? 'not set') . "\n";
echo "SCRIPT_NAME: " . ($_SERVER['SCRIPT_NAME'] ?? 'not set') . "\n";
echo "REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? 'not set') . "\n";
echo "PHP_SELF: " . ($_SERVER['PHP_SELF'] ?? 'not set') . "\n";
echo "</pre>";
?> 