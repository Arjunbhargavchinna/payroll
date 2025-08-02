<?php
/**
 * Quick Setup Script for PayrollPro
 * Run this to quickly configure the system for your server
 */

echo "<h1>PayrollPro Quick Setup</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .step { background: #f5f5f5; padding: 15px; margin: 10px 0; border-radius: 5px; }
    .success { background: #d4edda; color: #155724; }
    .error { background: #f8d7da; color: #721c24; }
    .warning { background: #fff3cd; color: #856404; }
    .code { background: #f8f9fa; padding: 10px; border-radius: 3px; font-family: monospace; }
</style>";

// Step 1: Detect current setup
echo "<div class='step'>";
echo "<h2>Step 1: Current Setup Detection</h2>";

$currentPath = $_SERVER['SCRIPT_NAME'];
$basePath = str_replace('/quick_setup.php', '', $currentPath);
$fullUrl = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $basePath;

echo "<strong>Detected Installation Path:</strong> {$basePath}<br>";
echo "<strong>Full URL:</strong> {$fullUrl}<br>";
echo "<strong>Document Root:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "</div>";

// Step 2: File Structure Check
echo "<div class='step'>";
echo "<h2>Step 2: File Structure Verification</h2>";

$requiredDirs = ['app', 'config', 'public', 'uploads', 'docs'];
$requiredFiles = [
    'config/config.php',
    'config/database.php',
    'public/index.php',
    'app/core/Controller.php',
    'database_setup.sql'
];

$missingItems = [];

foreach ($requiredDirs as $dir) {
    if (is_dir($dir)) {
        echo "<span style='color: green;'>✓</span> Directory: {$dir}<br>";
    } else {
        echo "<span style='color: red;'>✗</span> Directory: {$dir} - Missing<br>";
        $missingItems[] = $dir;
    }
}

foreach ($requiredFiles as $file) {
    if (file_exists($file)) {
        echo "<span style='color: green;'>✓</span> File: {$file}<br>";
    } else {
        echo "<span style='color: red;'>✗</span> File: {$file} - Missing<br>";
        $missingItems[] = $file;
    }
}

if (empty($missingItems)) {
    echo "<div class='success'>All required files and directories are present!</div>";
} else {
    echo "<div class='error'>Missing items: " . implode(', ', $missingItems) . "</div>";
}
echo "</div>";

// Step 3: Configuration Update
echo "<div class='step'>";
echo "<h2>Step 3: Configuration Update</h2>";

if (file_exists('config/config.php')) {
    $configContent = file_get_contents('config/config.php');
    
    // Update BASE_URL in config
    $newBaseUrl = $fullUrl;
    $updatedConfig = preg_replace(
        '/define\(\'BASE_URL\',.*?\);/',
        "define('BASE_URL', '{$newBaseUrl}');",
        $configContent
    );
    
    if (file_put_contents('config/config.php', $updatedConfig)) {
        echo "<div class='success'>✓ Configuration updated successfully</div>";
        echo "<strong>Updated BASE_URL to:</strong> {$newBaseUrl}<br>";
    } else {
        echo "<div class='error'>✗ Failed to update configuration</div>";
    }
} else {
    echo "<div class='error'>Configuration file not found</div>";
}
echo "</div>";

// Step 4: .htaccess Update
echo "<div class='step'>";
echo "<h2>Step 4: .htaccess Configuration</h2>";

$htaccessContent = "RewriteEngine On\n";
$htaccessContent .= "RewriteBase {$basePath}/\n\n";
$htaccessContent .= "# Redirect to public directory\n";
$htaccessContent .= "RewriteCond %{REQUEST_URI} !^{$basePath}/public/\n";
$htaccessContent .= "RewriteRule ^(.*)$ public/$1 [L]\n\n";
$htaccessContent .= "# Security headers\n";
$htaccessContent .= "Header always set X-Content-Type-Options nosniff\n";
$htaccessContent .= "Header always set X-Frame-Options DENY\n";

if (file_put_contents('.htaccess', $htaccessContent)) {
    echo "<div class='success'>✓ .htaccess updated successfully</div>";
} else {
    echo "<div class='error'>✗ Failed to update .htaccess</div>";
}

// Public .htaccess
$publicHtaccess = "RewriteEngine On\n";
$publicHtaccess .= "RewriteBase {$basePath}/public/\n\n";
$publicHtaccess .= "RewriteCond %{REQUEST_FILENAME} !-f\n";
$publicHtaccess .= "RewriteCond %{REQUEST_FILENAME} !-d\n";
$publicHtaccess .= "RewriteRule ^(.*)$ index.php [QSA,L]\n";

if (file_put_contents('public/.htaccess', $publicHtaccess)) {
    echo "<div class='success'>✓ Public .htaccess updated successfully</div>";
} else {
    echo "<div class='error'>✗ Failed to update public .htaccess</div>";
}
echo "</div>";

// Step 5: Database Configuration
echo "<div class='step'>";
echo "<h2>Step 5: Database Configuration</h2>";
echo "<div class='warning'>";
echo "<strong>Manual Step Required:</strong><br>";
echo "1. Create a MySQL database named 'payroll_pro'<br>";
echo "2. Import the 'database_setup.sql' file into your database<br>";
echo "3. Update database credentials in 'config/database.php'<br>";
echo "4. Set the following in config/database.php:<br>";
echo "<div class='code'>";
echo "private \$host = 'localhost';<br>";
echo "private \$database = 'payroll_pro';<br>";
echo "private \$username = 'your_db_username';<br>";
echo "private \$password = 'your_db_password';<br>";
echo "</div>";
echo "</div>";
echo "</div>";

// Step 6: Final URLs
echo "<div class='step'>";
echo "<h2>Step 6: Access URLs</h2>";
echo "<strong>After completing database setup, access:</strong><br>";
echo "• <strong>Main System:</strong> <a href='{$fullUrl}/public/'>{$fullUrl}/public/</a><br>";
echo "• <strong>Login Page:</strong> <a href='{$fullUrl}/public/login'>{$fullUrl}/public/login</a><br>";
echo "• <strong>System Test:</strong> <a href='{$fullUrl}/test_system.php'>{$fullUrl}/test_system.php</a><br>";
echo "<br>";
echo "<div class='success'>";
echo "<strong>Default Login Credentials:</strong><br>";
echo "Username: admin<br>";
echo "Password: password<br>";
echo "</div>";
echo "</div>";

echo "<div class='step'>";
echo "<h2>Troubleshooting</h2>";
echo "If you still get 404 errors:<br>";
echo "1. Check if mod_rewrite is enabled on your server<br>";
echo "2. Verify file permissions (755 for directories, 644 for files)<br>";
echo "3. Check Apache error logs for detailed error messages<br>";
echo "4. Contact your hosting provider if mod_rewrite is not available<br>";
echo "</div>";
?>