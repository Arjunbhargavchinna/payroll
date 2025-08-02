<?php
/**
 * Debug Script for PayrollPro
 * This script will help identify any remaining issues
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>PayrollPro Debug Script</h1>";

// Test 1: Check if we can load the main index file
echo "<h2>1. Testing Main Application</h2>";
try {
    // Test if we can include the main files without errors
    require_once 'config/config.php';
    require_once 'config/database.php';
    require_once 'app/core/Controller.php';
    
    echo "<p style='color: green;'>✓ Core files can be loaded</p>";
    
    // Test database connection
    $db = new Database();
    $result = $db->fetch("SELECT 1 as test");
    echo "<p style='color: green;'>✓ Database connection works</p>";
    
    // Test if we can create a controller
    class TestController extends Controller {
        public function test() {
            return true;
        }
    }
    
    $testController = new TestController();
    echo "<p style='color: green;'>✓ Controller can be instantiated</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error: " . $e->getMessage() . "</p>";
    echo "<p>Stack trace:</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

// Test 2: Check specific routes
echo "<h2>2. Testing Specific Routes</h2>";

$routes_to_test = [
    '/dashboard' => 'DashboardController',
    '/payroll' => 'PayrollController', 
    '/attendance' => 'AttendanceController'
];

foreach ($routes_to_test as $route => $controller_name) {
    try {
        $controller_file = "app/controllers/{$controller_name}.php";
        if (file_exists($controller_file)) {
            require_once $controller_file;
            if (class_exists($controller_name)) {
                echo "<p style='color: green;'>✓ {$controller_name} exists and can be loaded</p>";
            } else {
                echo "<p style='color: red;'>✗ {$controller_name} class not found</p>";
            }
        } else {
            echo "<p style='color: red;'>✗ {$controller_file} not found</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'>✗ Error loading {$controller_name}: " . $e->getMessage() . "</p>";
    }
}

// Test 3: Check view files
echo "<h2>3. Testing View Files</h2>";

$views_to_test = [
    'app/views/dashboard/index.php',
    'app/views/payroll/index.php',
    'app/views/attendance/index.php',
    'app/views/layout/main.php',
    'app/views/layout/sidebar.php'
];

foreach ($views_to_test as $view) {
    if (file_exists($view)) {
        echo "<p style='color: green;'>✓ {$view} exists</p>";
    } else {
        echo "<p style='color: red;'>✗ {$view} missing</p>";
    }
}

// Test 4: Check model files
echo "<h2>4. Testing Model Files</h2>";

$models_to_test = [
    'app/models/User.php',
    'app/models/Employee.php',
    'app/models/Payroll.php',
    'app/models/Attendance.php'
];

foreach ($models_to_test as $model) {
    if (file_exists($model)) {
        echo "<p style='color: green;'>✓ {$model} exists</p>";
    } else {
        echo "<p style='color: red;'>✗ {$model} missing</p>";
    }
}

// Test 5: Check database tables
echo "<h2>5. Testing Database Tables</h2>";

$tables_to_check = ['users', 'departments', 'employees', 'payroll_periods', 'attendance', 'roles'];

foreach ($tables_to_check as $table) {
    try {
        $result = $db->fetch("SELECT COUNT(*) as count FROM {$table}");
        echo "<p style='color: green;'>✓ Table '{$table}' exists with {$result['count']} records</p>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>✗ Table '{$table}' error: " . $e->getMessage() . "</p>";
    }
}

// Test 6: Check authentication
echo "<h2>6. Testing Authentication</h2>";

try {
    $user = $db->fetch("SELECT * FROM users WHERE username = 'admin'");
    if ($user) {
        echo "<p style='color: green;'>✓ Admin user exists</p>";
        
        // Test password verification
        if (password_verify('admin123', $user['password'])) {
            echo "<p style='color: green;'>✓ Admin password is correct</p>";
        } else {
            echo "<p style='color: red;'>✗ Admin password is incorrect</p>";
        }
    } else {
        echo "<p style='color: red;'>✗ Admin user not found</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Authentication test error: " . $e->getMessage() . "</p>";
}

// Test 7: Check file permissions
echo "<h2>7. Testing File Permissions</h2>";

$directories_to_check = ['uploads', 'logs', 'cache'];
foreach ($directories_to_check as $dir) {
    if (is_dir($dir)) {
        if (is_writable($dir)) {
            echo "<p style='color: green;'>✓ {$dir} is writable</p>";
        } else {
            echo "<p style='color: red;'>✗ {$dir} is not writable</p>";
        }
    } else {
        echo "<p style='color: red;'>✗ {$dir} directory doesn't exist</p>";
    }
}

// Test 8: Check configuration
echo "<h2>8. Testing Configuration</h2>";

if (defined('BASE_URL')) {
    echo "<p style='color: green;'>✓ BASE_URL is defined: " . BASE_URL . "</p>";
} else {
    echo "<p style='color: red;'>✗ BASE_URL is not defined</p>";
}

if (defined('APP_DEBUG')) {
    echo "<p style='color: green;'>✓ APP_DEBUG is defined: " . (APP_DEBUG ? 'true' : 'false') . "</p>";
} else {
    echo "<p style='color: red;'>✗ APP_DEBUG is not defined</p>";
}

// Test 9: Check session
echo "<h2>9. Testing Session</h2>";

session_start();
if (session_status() === PHP_SESSION_ACTIVE) {
    echo "<p style='color: green;'>✓ Sessions are working</p>";
} else {
    echo "<p style='color: red;'>✗ Sessions are not working</p>";
}

// Test 10: Simulate a request
echo "<h2>10. Simulating Request</h2>";

try {
    // Simulate what happens when accessing /dashboard
    $_SERVER['REQUEST_URI'] = '/dashboard';
    $_SERVER['REQUEST_METHOD'] = 'GET';
    
    // Test if we can load the router
    require_once 'index.php';
    echo "<p style='color: green;'>✓ Router can be loaded</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Router error: " . $e->getMessage() . "</p>";
}

echo "<h2>Debug Summary</h2>";
echo "<p>If you see any red X marks above, those are the issues that need to be fixed.</p>";
echo "<p><a href='complete_fix.php' style='color: blue; text-decoration: underline;'>Run Complete Fix Script</a></p>";
echo "<p><a href='index.php' style='color: blue; text-decoration: underline;'>Test Main Application</a></p>";
?> 