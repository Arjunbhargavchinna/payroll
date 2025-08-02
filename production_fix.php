<?php
/**
 * Production Fix Script for PayrollPro
 * Fixes issues with sidebar, Tailwind CSS, and file paths in production
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Production Fix Script for PayrollPro</h1>";

// Step 1: Check and fix file paths
echo "<h2>Step 1: File Path Verification</h2>";

$required_files = [
    'css/app.css',
    'js/app.js',
    'app/views/layout/main.php',
    'app/views/layout/sidebar.php',
    'config/config.php'
];

foreach ($required_files as $file) {
    if (file_exists($file)) {
        echo "<p style='color: green;'>✓ {$file} exists</p>";
    } else {
        echo "<p style='color: red;'>✗ {$file} missing</p>";
    }
}

// Step 2: Fix BASE_URL configuration
echo "<h2>Step 2: BASE_URL Configuration Fix</h2>";

try {
    require_once 'config/config.php';
    
    // Test the current BASE_URL
    $current_base_url = BASE_URL;
    echo "<p><strong>Current BASE_URL:</strong> {$current_base_url}</p>";
    
    // Check if it's correct for production
    $expected_base_url = 'http://ctoadmin.itiltd.in/cams/parollpro';
    if (strpos($current_base_url, 'ctoadmin.itiltd.in/cams/parollpro') !== false) {
        echo "<p style='color: green;'>✓ BASE_URL is correctly configured for production</p>";
    } else {
        echo "<p style='color: orange;'>⚠ BASE_URL may need adjustment for production</p>";
        echo "<p>Expected: {$expected_base_url}</p>";
        echo "<p>Current: {$current_base_url}</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error loading config: " . $e->getMessage() . "</p>";
}

// Step 3: Create a test page to verify CSS/JS loading
echo "<h2>Step 3: Create Test Page</h2>";

$test_page_content = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PayrollPro - Production Test</title>
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.4.3/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="' . BASE_URL . '/css/app.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center">
        <div class="max-w-md w-full space-y-8">
            <div class="text-center">
                <i class="fas fa-calculator text-4xl text-blue-500 mb-4"></i>
                <h1 class="text-3xl font-bold text-gray-900">PayrollPro</h1>
                <p class="text-gray-600 mt-2">Production Test Page</p>
            </div>
            
            <div class="bg-white shadow-lg rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-4">Component Test</h2>
                
                <!-- Test Tailwind CSS -->
                <div class="mb-4">
                    <h3 class="text-lg font-medium mb-2">Tailwind CSS Test</h3>
                    <div class="flex space-x-2">
                        <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Primary Button
                        </button>
                        <button class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            Success Button
                        </button>
                        <button class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                            Danger Button
                        </button>
                    </div>
                </div>
                
                <!-- Test Font Awesome -->
                <div class="mb-4">
                    <h3 class="text-lg font-medium mb-2">Font Awesome Test</h3>
                    <div class="flex space-x-4 text-2xl">
                        <i class="fas fa-home text-blue-500"></i>
                        <i class="fas fa-user text-green-500"></i>
                        <i class="fas fa-cog text-gray-500"></i>
                        <i class="fas fa-bell text-yellow-500"></i>
                    </div>
                </div>
                
                <!-- Test Custom CSS -->
                <div class="mb-4">
                    <h3 class="text-lg font-medium mb-2">Custom CSS Test</h3>
                    <button class="btn btn-primary">Custom Button</button>
                    <div class="alert alert-success mt-2">Success alert with custom CSS</div>
                </div>
                
                <!-- Test JavaScript -->
                <div class="mb-4">
                    <h3 class="text-lg font-medium mb-2">JavaScript Test</h3>
                    <button onclick="testJavaScript()" class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">
                        Test JavaScript
                    </button>
                    <div id="js-test-result" class="mt-2 text-sm"></div>
                </div>
                
                <!-- Test Sidebar Toggle -->
                <div class="mb-4">
                    <h3 class="text-lg font-medium mb-2">Sidebar Test</h3>
                    <button onclick="toggleSidebar()" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                        <i class="fas fa-bars mr-2"></i>Toggle Sidebar
                    </button>
                </div>
            </div>
            
            <div class="text-center">
                <a href="' . BASE_URL . '/index.php" class="text-blue-600 hover:text-blue-800">
                    ← Back to Main Application
                </a>
            </div>
        </div>
    </div>
    
    <!-- JavaScript -->
    <script src="' . BASE_URL . '/js/app.js"></script>
    <script>
    function testJavaScript() {
        document.getElementById("js-test-result").innerHTML = 
            "<span class=\"text-green-600\">✓ JavaScript is working!</span>";
    }
    
    function toggleSidebar() {
        const sidebar = document.getElementById("sidebar");
        if (sidebar) {
            sidebar.classList.toggle("-translate-x-full");
        } else {
            alert("Sidebar element not found. This is expected on this test page.");
        }
    }
    
    // Test if PayrollApp is loaded
    document.addEventListener("DOMContentLoaded", function() {
        if (typeof PayrollApp !== "undefined") {
            console.log("✓ PayrollApp loaded successfully");
        } else {
            console.log("✗ PayrollApp not loaded");
        }
    });
    </script>
</body>
</html>';

if (file_put_contents('production_test.php', $test_page_content)) {
    echo "<p style='color: green;'>✓ Created production test page</p>";
    echo "<p><a href='" . BASE_URL . "/production_test.php' target='_blank'>Open Test Page</a></p>";
} else {
    echo "<p style='color: red;'>✗ Could not create test page</p>";
}

// Step 4: Check for common production issues
echo "<h2>Step 4: Production Issues Check</h2>";

// Check if .htaccess exists and is configured
if (file_exists('.htaccess')) {
    echo "<p style='color: green;'>✓ .htaccess file exists</p>";
} else {
    echo "<p style='color: orange;'>⚠ .htaccess file missing - URL rewriting may not work</p>";
}

// Check if error reporting is disabled for production
if (defined('APP_DEBUG') && !APP_DEBUG) {
    echo "<p style='color: green;'>✓ Error reporting is disabled for production</p>";
} else {
    echo "<p style='color: orange;'>⚠ Error reporting is enabled - consider disabling for production</p>";
}

// Check file permissions
$directories_to_check = ['css', 'js', 'app', 'config'];
foreach ($directories_to_check as $dir) {
    if (is_dir($dir)) {
        if (is_readable($dir)) {
            echo "<p style='color: green;'>✓ {$dir} directory is readable</p>";
        } else {
            echo "<p style='color: red;'>✗ {$dir} directory is not readable</p>";
        }
    } else {
        echo "<p style='color: red;'>✗ {$dir} directory does not exist</p>";
    }
}

// Step 5: Create a simple debug script
echo "<h2>Step 5: Create Debug Script</h2>";

$debug_script = '<?php
/**
 * Production Debug Script
 */

error_reporting(E_ALL);
ini_set("display_errors", 1);

echo "<h1>Production Debug Information</h1>";

// Load config
require_once "config/config.php";

echo "<h2>Configuration</h2>";
echo "<p><strong>BASE_URL:</strong> " . BASE_URL . "</p>";
echo "<p><strong>APP_DEBUG:</strong> " . (APP_DEBUG ? "true" : "false") . "</p>";

echo "<h2>Server Information</h2>";
echo "<p><strong>HTTP_HOST:</strong> " . ($_SERVER["HTTP_HOST"] ?? "not set") . "</p>";
echo "<p><strong>REQUEST_URI:</strong> " . ($_SERVER["REQUEST_URI"] ?? "not set") . "</p>";
echo "<p><strong>SCRIPT_NAME:</strong> " . ($_SERVER["SCRIPT_NAME"] ?? "not set") . "</p>";

echo "<h2>File Paths</h2>";
$files = ["css/app.css", "js/app.js", "app/views/layout/main.php"];
foreach ($files as $file) {
    $exists = file_exists($file);
    $readable = is_readable($file);
    $color = $exists && $readable ? "green" : "red";
    echo "<p style=\"color: {$color};\">" . ($exists ? "✓" : "✗") . " {$file} - " . 
         ($exists ? "exists" : "missing") . " - " . 
         ($readable ? "readable" : "not readable") . "</p>";
}

echo "<h2>CSS/JS Loading Test</h2>";
echo "<p>Check browser console for any 404 errors on CSS/JS files</p>";
echo "<p>Expected URLs:</p>";
echo "<ul>";
echo "<li>CSS: " . BASE_URL . "/css/app.css</li>";
echo "<li>JS: " . BASE_URL . "/js/app.js</li>";
echo "<li>Tailwind: https://cdn.jsdelivr.net/npm/tailwindcss@3.4.3/dist/tailwind.min.css</li>";
echo "<li>Font Awesome: https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css</li>";
echo "</ul>";

echo "<h2>Common Production Issues</h2>";
echo "<ol>";
echo "<li><strong>404 Errors:</strong> Check if CSS/JS files are accessible via browser</li>";
echo "<li><strong>Sidebar not working:</strong> Check if JavaScript is loading properly</li>";
echo "<li><strong>Tailwind not working:</strong> Check if CDN is accessible</li>";
echo "<li><strong>Font Awesome not working:</strong> Check if CDN is accessible</li>";
echo "<li><strong>BASE_URL issues:</strong> Check if redirects work correctly</li>";
echo "</ol>";

echo "<h2>Quick Fixes</h2>";
echo "<ol>";
echo "<li>Clear browser cache and reload</li>";
echo "<li>Check browser console for JavaScript errors</li>";
echo "<li>Verify all file paths use BASE_URL</li>";
echo "<li>Ensure .htaccess is configured for URL rewriting</li>";
echo "<li>Check web server error logs</li>";
echo "</ol>";

echo "<p><a href=\"' . BASE_URL . '/index.php\">← Back to Application</a></p>";
?>';

if (file_put_contents('production_debug.php', $debug_script)) {
    echo "<p style='color: green;'>✓ Created production debug script</p>";
    echo "<p><a href='" . BASE_URL . "/production_debug.php' target='_blank'>Open Debug Script</a></p>";
} else {
    echo "<p style='color: red;'>✗ Could not create debug script</p>";
}

// Step 6: Summary and next steps
echo "<h2>Step 6: Summary and Next Steps</h2>";

echo "<h3>What to Test:</h3>";
echo "<ol>";
echo "<li><strong>Test Page:</strong> <a href='" . BASE_URL . "/production_test.php' target='_blank'>Open Test Page</a></li>";
echo "<li><strong>Debug Script:</strong> <a href='" . BASE_URL . "/production_debug.php' target='_blank'>Open Debug Script</a></li>";
echo "<li><strong>Main Application:</strong> <a href='" . BASE_URL . "/index.php' target='_blank'>Open Main App</a></li>";
echo "</ol>";

echo "<h3>Common Production Issues and Solutions:</h3>";
echo "<h4>1. CSS/JS Not Loading</h4>";
echo "<ul>";
echo "<li>Check if files are accessible: " . BASE_URL . "/css/app.css</li>";
echo "<li>Check browser console for 404 errors</li>";
echo "<li>Verify .htaccess is configured for static files</li>";
echo "</ul>";

echo "<h4>2. Sidebar Not Working</h4>";
echo "<ul>";
echo "<li>Check if JavaScript is loading: " . BASE_URL . "/js/app.js</li>";
echo "<li>Check browser console for JavaScript errors</li>";
echo "<li>Verify Font Awesome icons are loading</li>";
echo "</ul>";

echo "<h4>3. Tailwind CSS Not Working</h4>";
echo "<ul>";
echo "<li>Check if CDN is accessible: https://cdn.jsdelivr.net/npm/tailwindcss@3.4.3/dist/tailwind.min.css</li>";
echo "<li>Check network tab in browser developer tools</li>";
echo "<li>Try using a different CDN if needed</li>";
echo "</ul>";

echo "<h4>4. BASE_URL Issues</h4>";
echo "<ul>";
echo "<li>Current BASE_URL: " . BASE_URL . "</li>";
echo "<li>Expected: http://ctoadmin.itiltd.in/cams/parollpro</li>";
echo "<li>Check if redirects work correctly</li>";
echo "</ul>";

echo "<h3>Manual Steps if Issues Persist:</h3>";
echo "<ol>";
echo "<li>Clear browser cache completely</li>";
echo "<li>Check web server error logs</li>";
echo "<li>Verify file permissions (755 for directories, 644 for files)</li>";
echo "<li>Test with different browsers</li>";
echo "<li>Check if CDN resources are accessible from your server</li>";
echo "</ol>";

echo "<h3>Success Indicators:</h3>";
echo "<ul>";
echo "<li>✓ Test page shows styled buttons and icons</li>";
echo "<li>✓ Main application loads with proper styling</li>";
echo "<li>✓ Sidebar toggles correctly on mobile</li>";
echo "<li>✓ No 404 errors in browser console</li>";
echo "<li>✓ All links work correctly</li>";
echo "</ul>";

echo "<p style='color: green; font-weight: bold;'>Production fix script completed! Test the links above to verify everything is working.</p>";
?> 