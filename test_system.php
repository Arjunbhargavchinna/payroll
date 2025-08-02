<?php
/**
 * System Test Script
 * Run this file to test all system components
 */

// Start session for testing
session_start();

// Include configuration
require_once 'config/config.php';
require_once 'config/database.php';

echo "<h1>PayrollPro System Test</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .success { color: green; }
    .error { color: red; }
    .info { color: blue; }
    .test-section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; }
</style>";

// Test 1: Database Connection
echo "<div class='test-section'>";
echo "<h2>1. Database Connection Test</h2>";
try {
    $db = new Database();
    $connection = $db->getConnection();
    if ($connection) {
        echo "<p class='success'>✓ Database connection successful</p>";
        
        // Test basic query
        $result = $db->fetch("SELECT COUNT(*) as count FROM users");
        echo "<p class='success'>✓ Database query test passed. Users count: " . $result['count'] . "</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>✗ Database connection failed: " . $e->getMessage() . "</p>";
}
echo "</div>";

// Test 2: File Structure
echo "<div class='test-section'>";
echo "<h2>2. File Structure Test</h2>";
$requiredFiles = [
    'app/core/Controller.php',
    'app/core/Model.php',
    'app/controllers/AuthController.php',
    'app/controllers/DashboardController.php',
    'app/controllers/EmployeeController.php',
    'app/models/User.php',
    'app/models/Employee.php',
    'app/views/layout/header.php',
    'app/views/layout/footer.php',
    'public/css/app.css',
    'public/js/app.js'
];

$missingFiles = [];
foreach ($requiredFiles as $file) {
    if (file_exists($file)) {
        echo "<p class='success'>✓ {$file}</p>";
    } else {
        echo "<p class='error'>✗ {$file} - Missing</p>";
        $missingFiles[] = $file;
    }
}

if (empty($missingFiles)) {
    echo "<p class='success'><strong>All required files present!</strong></p>";
} else {
    echo "<p class='error'><strong>" . count($missingFiles) . " files missing</strong></p>";
}
echo "</div>";

// Test 3: Database Tables
echo "<div class='test-section'>";
echo "<h2>3. Database Tables Test</h2>";
try {
    $db = new Database();
    $requiredTables = [
        'users', 'roles', 'departments', 'designations', 'employees',
        'salary_components', 'salary_structures', 'payroll_periods',
        'payroll_transactions', 'loan_types', 'employee_loans',
        'tax_slabs', 'attendance', 'leave_types', 'holidays', 'audit_logs'
    ];
    
    $missingTables = [];
    foreach ($requiredTables as $table) {
        $result = $db->fetch("SHOW TABLES LIKE '{$table}'");
        if ($result) {
            echo "<p class='success'>✓ Table: {$table}</p>";
        } else {
            echo "<p class='error'>✗ Table: {$table} - Missing</p>";
            $missingTables[] = $table;
        }
    }
    
    if (empty($missingTables)) {
        echo "<p class='success'><strong>All required tables present!</strong></p>";
    } else {
        echo "<p class='error'><strong>" . count($missingTables) . " tables missing</strong></p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>✗ Table check failed: " . $e->getMessage() . "</p>";
}
echo "</div>";

// Test 4: Sample Data
echo "<div class='test-section'>";
echo "<h2>4. Sample Data Test</h2>";
try {
    $db = new Database();
    
    // Check users
    $users = $db->fetch("SELECT COUNT(*) as count FROM users");
    echo "<p class='info'>Users: " . $users['count'] . "</p>";
    
    // Check employees
    $employees = $db->fetch("SELECT COUNT(*) as count FROM employees");
    echo "<p class='info'>Employees: " . $employees['count'] . "</p>";
    
    // Check departments
    $departments = $db->fetch("SELECT COUNT(*) as count FROM departments");
    echo "<p class='info'>Departments: " . $departments['count'] . "</p>";
    
    // Check salary components
    $components = $db->fetch("SELECT COUNT(*) as count FROM salary_components");
    echo "<p class='info'>Salary Components: " . $components['count'] . "</p>";
    
    if ($users['count'] > 0 && $employees['count'] > 0 && $departments['count'] > 0) {
        echo "<p class='success'><strong>Sample data loaded successfully!</strong></p>";
    } else {
        echo "<p class='error'><strong>Sample data missing or incomplete</strong></p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>✗ Sample data check failed: " . $e->getMessage() . "</p>";
}
echo "</div>";

// Test 5: Permissions and Security
echo "<div class='test-section'>";
echo "<h2>5. Security Test</h2>";

// Check uploads directory
if (is_dir('uploads') && is_writable('uploads')) {
    echo "<p class='success'>✓ Uploads directory exists and is writable</p>";
} else {
    echo "<p class='error'>✗ Uploads directory missing or not writable</p>";
}

// Check .htaccess
if (file_exists('.htaccess')) {
    echo "<p class='success'>✓ .htaccess file exists</p>";
} else {
    echo "<p class='error'>✗ .htaccess file missing</p>";
}

// Check config files
if (file_exists('config/database.php') && file_exists('config/config.php')) {
    echo "<p class='success'>✓ Configuration files exist</p>";
} else {
    echo "<p class='error'>✗ Configuration files missing</p>";
}
echo "</div>";

// Test 6: URL and Routing
echo "<div class='test-section'>";
echo "<h2>6. URL and Routing Test</h2>";
echo "<p class='info'>Current URL: " . $_SERVER['REQUEST_URI'] . "</p>";
echo "<p class='info'>Base URL: " . BASE_URL . "</p>";
echo "<p class='info'>Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
echo "<p class='info'>Script Name: " . $_SERVER['SCRIPT_NAME'] . "</p>";

// Test if constants are defined
$constants = ['BASE_URL', 'APP_NAME', 'APP_VERSION'];
foreach ($constants as $constant) {
    if (defined($constant)) {
        echo "<p class='success'>✓ Constant {$constant}: " . constant($constant) . "</p>";
    } else {
        echo "<p class='error'>✗ Constant {$constant} not defined</p>";
    }
}
echo "</div>";

// Test 7: Login Test
echo "<div class='test-section'>";
echo "<h2>7. Authentication Test</h2>";
try {
    require_once 'app/models/User.php';
    $db = new Database();
    $userModel = new User($db);
    
    // Test authentication with default credentials
    $user = $userModel->authenticate('admin', 'password');
    if ($user) {
        echo "<p class='success'>✓ Default admin login works</p>";
        echo "<p class='info'>Admin user: " . $user['full_name'] . " (Role: " . $user['role_name'] . ")</p>";
    } else {
        echo "<p class='error'>✗ Default admin login failed</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>✗ Authentication test failed: " . $e->getMessage() . "</p>";
}
echo "</div>";

// Test Summary
echo "<div class='test-section'>";
echo "<h2>Test Summary</h2>";
echo "<p><strong>System Status:</strong> ";
if (empty($missingFiles) && empty($missingTables ?? [])) {
    echo "<span class='success'>READY FOR PRODUCTION</span>";
    echo "<br><br>";
    echo "<strong>Next Steps:</strong><br>";
    echo "1. Access the system at: <a href='" . BASE_URL . "'>" . BASE_URL . "</a><br>";
    echo "2. Login with: admin / password<br>";
    echo "3. Change default password<br>";
    echo "4. Configure company details<br>";
    echo "5. Add your employees and start processing payroll<br>";
} else {
    echo "<span class='error'>NEEDS ATTENTION</span>";
    echo "<br><br>";
    echo "<strong>Issues to Fix:</strong><br>";
    if (!empty($missingFiles)) {
        echo "- Missing files: " . implode(', ', $missingFiles) . "<br>";
    }
    if (!empty($missingTables ?? [])) {
        echo "- Missing tables: " . implode(', ', $missingTables) . "<br>";
    }
}
echo "</p>";
echo "</div>";

echo "<hr>";
echo "<p><em>Test completed at: " . date('Y-m-d H:i:s') . "</em></p>";
?>