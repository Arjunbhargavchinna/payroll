<?php
/**
 * Test Alternative CSS Solution
 */

// Load config
require_once 'config/config.php';

echo "<h1>Test Alternative CSS Solution</h1>";

// Check if files exist
echo "<h2>File Status:</h2>";
$files_to_check = [
    'css/tailwind-alternative.css' => 'Alternative CSS file',
    'alternative_layout.php' => 'Alternative layout file',
    'update_layout.php' => 'Layout update script',
    'app/views/layout/main.php' => 'Main layout file'
];

foreach ($files_to_check as $file => $description) {
    if (file_exists($file)) {
        $size = filesize($file);
        echo "<p style='color: green;'>✓ {$description} exists ({$size} bytes)</p>";
    } else {
        echo "<p style='color: red;'>✗ {$description} missing</p>";
    }
}

// Test CSS file content
echo "<h2>CSS File Test:</h2>";
if (file_exists('css/tailwind-alternative.css')) {
    $css_content = file_get_contents('css/tailwind-alternative.css');
    $has_flex = strpos($css_content, '.flex') !== false;
    $has_bg_white = strpos($css_content, '.bg-white') !== false;
    $has_btn = strpos($css_content, '.btn') !== false;
    
    echo "<p>" . ($has_flex ? "✓" : "✗") . " Flex utilities included</p>";
    echo "<p>" . ($has_bg_white ? "✓" : "✗") . " Background colors included</p>";
    echo "<p>" . ($has_btn ? "✓" : "✗") . " Button styles included</p>";
} else {
    echo "<p style='color: red;'>✗ CSS file not found</p>";
}

// Test main layout
echo "<h2>Main Layout Test:</h2>";
if (file_exists('app/views/layout/main.php')) {
    $layout_content = file_get_contents('app/views/layout/main.php');
    $has_tailwind_cdn = strpos($layout_content, 'tailwindcss@3.4.3') !== false;
    $has_alternative_css = strpos($layout_content, 'tailwind-alternative.css') !== false;
    
    if ($has_tailwind_cdn && !$has_alternative_css) {
        echo "<p style='color: orange;'>⚠ Main layout still uses Tailwind CDN</p>";
        echo "<p><a href='" . BASE_URL . "/update_layout.php'>Run Layout Update</a></p>";
    } elseif ($has_alternative_css) {
        echo "<p style='color: green;'>✓ Main layout uses alternative CSS</p>";
    } else {
        echo "<p style='color: orange;'>⚠ Main layout CSS configuration unclear</p>";
    }
} else {
    echo "<p style='color: red;'>✗ Main layout file not found</p>";
}

echo "<h2>Test Links:</h2>";
echo "<ul>";
echo "<li><a href='" . BASE_URL . "/alternative_layout.php' target='_blank'>Test Alternative Layout</a></li>";
echo "<li><a href='" . BASE_URL . "/update_layout.php' target='_blank'>Update Main Layout</a></li>";
echo "<li><a href='" . BASE_URL . "/index.php' target='_blank'>Test Main Application</a></li>";
echo "</ul>";

echo "<h2>Quick Fix Instructions:</h2>";
echo "<ol>";
echo "<li><strong>If CSS file is missing:</strong> The alternative CSS file should be created automatically</li>";
echo "<li><strong>If main layout still uses CDN:</strong> Run the update script or manually replace the CSS link</li>";
echo "<li><strong>If styles are not working:</strong> Check browser console for 404 errors</li>";
echo "<li><strong>If sidebar is not working:</strong> Ensure JavaScript is loading properly</li>";
echo "</ol>";

echo "<h2>Expected Results:</h2>";
echo "<ul>";
echo "<li>✓ Alternative layout should show styled buttons and components</li>";
echo "<li>✓ Main application should work without Tailwind CDN dependency</li>";
echo "<li>✓ No 404 errors for CSS files</li>";
echo "<li>✓ Sidebar should toggle properly on mobile</li>";
echo "<li>✓ All colors and layouts should display correctly</li>";
echo "</ul>";

echo "<p style='color: green; font-weight: bold;'>Alternative CSS solution is ready! Test the links above.</p>";
?> 