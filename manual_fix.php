<?php
/**
 * Manual Fix Script for PayrollPro
 * Addresses issues that need manual intervention
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Manual Fix Script for PayrollPro</h1>";

// Step 1: Fix database field issues
echo "<h2>Step 1: Fix Database Field Issues</h2>";
try {
    require_once 'config/database.php';
    $db = new Database();
    
    // Check if there are any tables with missing 'code' field
    $tables_to_check = ['departments', 'employees', 'salary_components'];
    
    foreach ($tables_to_check as $table) {
        try {
            // Check if table exists and has the right structure
            $columns = $db->fetchAll("SHOW COLUMNS FROM {$table}");
            $column_names = array_column($columns, 'Field');
            
            if (in_array('code', $column_names)) {
                echo "<p style='color: green;'>✓ Table '{$table}' has 'code' field</p>";
            } else {
                // Add code field if missing
                try {
                    $db->query("ALTER TABLE {$table} ADD COLUMN code VARCHAR(20) UNIQUE AFTER id");
                    echo "<p style='color: green;'>✓ Added 'code' field to table '{$table}'</p>";
                } catch (Exception $e) {
                    echo "<p style='color: orange;'>⚠ Could not add 'code' field to '{$table}': " . $e->getMessage() . "</p>";
                }
            }
        } catch (Exception $e) {
            echo "<p style='color: red;'>✗ Error checking table '{$table}': " . $e->getMessage() . "</p>";
        }
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Database error: " . $e->getMessage() . "</p>";
}

// Step 2: Manual configuration fixes
echo "<h2>Step 2: Manual Configuration Fixes</h2>";

echo "<h3>BASE_URL Configuration</h3>";
echo "<p>Please manually edit <strong>config/config.php</strong> and change:</p>";
echo "<pre>";
echo "// Change this line:\n";
echo "define('BASE_URL', \$protocol . '://' . \$host . \$basePath);\n";
echo "\n";
echo "// To this:\n";
echo "define('BASE_URL', \$protocol . '://' . \$host . dirname(\$scriptName));\n";
echo "</pre>";

// Step 3: File permission fixes
echo "<h2>Step 3: File Permission Fixes</h2>";

echo "<h3>Set File Permissions</h3>";
echo "<p>Run these commands in your terminal:</p>";
echo "<pre>";
echo "chmod 755 app/\n";
echo "chmod 755 config/\n";
echo "chmod 755 css/\n";
echo "chmod 755 js/\n";
echo "chmod 644 *.php\n";
echo "chmod 644 config/*.php\n";
echo "chmod 644 app/core/*.php\n";
echo "chmod 644 app/controllers/*.php\n";
echo "chmod 644 app/models/*.php\n";
echo "chmod 644 app/views/**/*.php\n";
echo "</pre>";

// Step 4: Create missing directories manually
echo "<h2>Step 4: Create Missing Directories</h2>";

$directories = [
    'app/views/errors',
    'app/views/auth',
    'logs',
    'cache',
    'uploads',
    'uploads/documents',
    'uploads/images'
];

echo "<p>Create these directories manually:</p>";
echo "<ul>";
foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        echo "<li style='color: red;'>✗ {$dir} - NEEDS TO BE CREATED</li>";
    } else {
        echo "<li style='color: green;'>✓ {$dir}</li>";
    }
}
echo "</ul>";

// Step 5: Test database connection
echo "<h2>Step 5: Test Database Connection</h2>";
try {
    $result = $db->fetch("SELECT 1 as test");
    echo "<p style='color: green;'>✓ Database connection successful</p>";
    
    // Test admin user
    $admin = $db->fetch("SELECT * FROM users WHERE username = 'admin'");
    if ($admin) {
        echo "<p style='color: green;'>✓ Admin user exists</p>";
        if (password_verify('admin123', $admin['password'])) {
            echo "<p style='color: green;'>✓ Admin password is correct</p>";
        } else {
            echo "<p style='color: red;'>✗ Admin password is incorrect</p>";
        }
    } else {
        echo "<p style='color: red;'>✗ Admin user not found</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Database connection failed: " . $e->getMessage() . "</p>";
}

// Step 6: Application test
echo "<h2>Step 6: Application Test</h2>";
echo "<p><a href='index.php' target='_blank' style='color: blue; text-decoration: underline;'>Test Main Application</a></p>";
echo "<p><a href='debug.php' target='_blank' style='color: blue; text-decoration: underline;'>Run Debug Script</a></p>";

echo "<h2>Summary</h2>";
echo "<p>After completing the manual steps above:</p>";
echo "<ol>";
echo "<li>Test the application at index.php</li>";
echo "<li>Login with admin/admin123</li>";
echo "<li>Check if /dashboard, /payroll, /attendance work</li>";
echo "<li>If still having issues, check web server error logs</li>";
echo "</ol>";

echo "<h2>Common Issues and Solutions</h2>";
echo "<h3>500 Internal Server Error</h3>";
echo "<ul>";
echo "<li>Check web server error logs</li>";
echo "<li>Ensure all file permissions are correct</li>";
echo "<li>Verify database connection settings</li>";
echo "<li>Make sure all required PHP extensions are installed</li>";
echo "</ul>";

echo "<h3>CSS/JS Not Loading</h3>";
echo "<ul>";
echo "<li>Check if css/app.css and js/app.js exist</li>";
echo "<li>Verify web server can serve static files</li>";
echo "<li>Check browser console for errors</li>";
echo "</ul>";

echo "<h3>Sidebar Not Working</h3>";
echo "<ul>";
echo "<li>Check browser console for JavaScript errors</li>";
echo "<li>Ensure Font Awesome icons are loading</li>";
echo "<li>Verify js/app.js is loading properly</li>";
echo "</ul>";
?> 