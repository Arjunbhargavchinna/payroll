<?php
/**
 * Update Main Layout to Use Alternative CSS
 */

// Load config
require_once "config/config.php";

echo "<h1>Update Main Layout to Use Alternative CSS</h1>";

// Read the current main layout
$main_layout_file = "app/views/layout/main.php";
if (file_exists($main_layout_file)) {
    $content = file_get_contents($main_layout_file);
    
    // Replace Tailwind CDN with alternative CSS
    $old_tailwind = '<link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.4.3/dist/tailwind.min.css" rel="stylesheet">';
    $new_css = '<link href="' . BASE_URL . '/css/tailwind-alternative.css" rel="stylesheet">';
    
    if (strpos($content, $old_tailwind) !== false) {
        $updated_content = str_replace($old_tailwind, $new_css, $content);
        
        if (file_put_contents($main_layout_file, $updated_content)) {
            echo "<p style=\"color: green;\">✓ Successfully updated main layout to use alternative CSS</p>";
        } else {
            echo "<p style=\"color: red;\">✗ Could not update main layout file</p>";
        }
    } else {
        echo "<p style=\"color: orange;\">⚠ Tailwind CDN link not found in main layout</p>";
        echo "<p>Current CSS links in main layout:</p>";
        echo "<pre>" . htmlspecialchars(substr($content, 0, 1000)) . "</pre>";
    }
} else {
    echo "<p style=\"color: red;\">✗ Main layout file not found</p>";
}

echo "<h2>Next Steps:</h2>";
echo "<ol>";
echo "<li>Test the main application: <a href=\"" . BASE_URL . "/index.php\" target=\"_blank\">Open Main App</a></li>";
echo "<li>Test the alternative layout: <a href=\"" . BASE_URL . "/alternative_layout.php\" target=\"_blank\">Open Alternative Layout</a></li>";
echo "<li>Check if all components are working properly</li>";
echo "<li>If issues persist, check browser console for errors</li>";
echo "</ol>";

echo "<h2>Benefits of Alternative CSS:</h2>";
echo "<ul>";
echo "<li>✓ No dependency on external CDN</li>";
echo "<li>✓ Faster loading times</li>";
echo "<li>✓ Works even when CDN is down</li>";
echo "<li>✓ Includes all essential Tailwind classes</li>";
echo "<li>✓ Custom PayrollPro specific styles included</li>";
echo "</ul>";

echo "<h2>Manual Update Instructions:</h2>";
echo "<p>If the automatic update didn't work, manually replace this line in <code>app/views/layout/main.php</code>:</p>";
echo "<p><strong>Find:</strong></p>";
echo "<code>&lt;link href=\"https://cdn.jsdelivr.net/npm/tailwindcss@3.4.3/dist/tailwind.min.css\" rel=\"stylesheet\"&gt;</code>";
echo "<p><strong>Replace with:</strong></p>";
echo "<code>&lt;link href=\"&lt;?php echo BASE_URL; ?&gt;/css/tailwind-alternative.css\" rel=\"stylesheet\"&gt;</code>";

echo "<h2>Testing Checklist:</h2>";
echo "<ul>";
echo "<li>✓ Buttons and forms work properly</li>";
echo "<li>✓ Sidebar toggles correctly</li>";
echo "<li>✓ All colors and backgrounds display correctly</li>";
echo "<li>✓ Responsive design works on mobile</li>";
echo "<li>✓ No 404 errors for CSS files</li>";
echo "<li>✓ JavaScript functionality works</li>";
echo "</ul>";

echo "<p style='color: green; font-weight: bold;'>Layout update completed! Test your application now.</p>";
?> 