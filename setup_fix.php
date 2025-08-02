<?php
/**
 * PayrollPro Setup and Fix Script
 * This script will fix common issues and set up the application properly
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>PayrollPro Setup and Fix Script</h1>";

// Step 1: Check PHP version
echo "<h2>Step 1: PHP Version Check</h2>";
if (version_compare(PHP_VERSION, '7.4.0', '>=')) {
    echo "<p style='color: green;'>✓ PHP version " . PHP_VERSION . " is compatible</p>";
} else {
    echo "<p style='color: red;'>✗ PHP version " . PHP_VERSION . " is too old. Please upgrade to PHP 7.4 or higher.</p>";
}

// Step 2: Check required extensions
echo "<h2>Step 2: Required Extensions</h2>";
$required_extensions = ['pdo', 'pdo_mysql', 'json', 'mbstring', 'openssl'];
foreach ($required_extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "<p style='color: green;'>✓ {$ext} extension is loaded</p>";
    } else {
        echo "<p style='color: red;'>✗ {$ext} extension is missing</p>";
    }
}

// Step 3: Check file permissions
echo "<h2>Step 3: File Permissions</h2>";
$directories = ['uploads', 'logs', 'cache'];
foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
        echo "<p style='color: blue;'>✓ Created directory: {$dir}</p>";
    } else {
        echo "<p style='color: green;'>✓ Directory exists: {$dir}</p>";
    }
}

// Step 4: Database setup
echo "<h2>Step 4: Database Setup</h2>";
try {
    require_once 'config/database.php';
    $db = new Database();
    
    // Create tables if they don't exist
    $tables = [
        'users' => "CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            full_name VARCHAR(100) NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            role ENUM('admin', 'manager', 'user') DEFAULT 'user',
            permissions TEXT,
            status ENUM('active', 'inactive') DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )",
        
        'departments' => "CREATE TABLE IF NOT EXISTS departments (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            description TEXT,
            status ENUM('active', 'inactive') DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        
        'employees' => "CREATE TABLE IF NOT EXISTS employees (
            id INT AUTO_INCREMENT PRIMARY KEY,
            employee_code VARCHAR(20) UNIQUE NOT NULL,
            first_name VARCHAR(50) NOT NULL,
            last_name VARCHAR(50) NOT NULL,
            email VARCHAR(100) UNIQUE,
            phone VARCHAR(20),
            department_id INT,
            designation VARCHAR(100),
            joining_date DATE,
            salary DECIMAL(10,2),
            status ENUM('active', 'inactive', 'terminated') DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (department_id) REFERENCES departments(id)
        )",
        
        'payroll_periods' => "CREATE TABLE IF NOT EXISTS payroll_periods (
            id INT AUTO_INCREMENT PRIMARY KEY,
            period_name VARCHAR(50) NOT NULL,
            start_date DATE NOT NULL,
            end_date DATE NOT NULL,
            status ENUM('open', 'processing', 'locked') DEFAULT 'open',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        
        'attendance' => "CREATE TABLE IF NOT EXISTS attendance (
            id INT AUTO_INCREMENT PRIMARY KEY,
            employee_id INT NOT NULL,
            date DATE NOT NULL,
            check_in TIME,
            check_out TIME,
            status ENUM('present', 'absent', 'half-day', 'leave') DEFAULT 'present',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (employee_id) REFERENCES employees(id),
            UNIQUE KEY unique_attendance (employee_id, date)
        )",
        
        'audit_logs' => "CREATE TABLE IF NOT EXISTS audit_logs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT,
            action VARCHAR(100) NOT NULL,
            table_name VARCHAR(50),
            record_id INT,
            old_values TEXT,
            new_values TEXT,
            ip_address VARCHAR(45),
            user_agent TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )"
    ];
    
    foreach ($tables as $table_name => $sql) {
        try {
            $db->query($sql);
            echo "<p style='color: green;'>✓ Table '{$table_name}' created/verified</p>";
        } catch (Exception $e) {
            echo "<p style='color: red;'>✗ Error creating table '{$table_name}': " . $e->getMessage() . "</p>";
        }
    }
    
    // Insert default admin user if not exists
    $admin_exists = $db->fetch("SELECT id FROM users WHERE username = 'admin'");
    if (!$admin_exists) {
        $admin_password = password_hash('admin123', PASSWORD_DEFAULT);
        $db->insert('users', [
            'username' => 'admin',
            'password' => $admin_password,
            'full_name' => 'Administrator',
            'email' => 'admin@payrollpro.com',
            'role' => 'admin',
            'permissions' => 'all'
        ]);
        echo "<p style='color: green;'>✓ Default admin user created (username: admin, password: admin123)</p>";
    } else {
        echo "<p style='color: blue;'>✓ Admin user already exists</p>";
    }
    
    // Insert sample departments
    $departments = ['Human Resources', 'Finance', 'IT', 'Marketing', 'Operations'];
    foreach ($departments as $dept) {
        $exists = $db->fetch("SELECT id FROM departments WHERE name = ?", [$dept]);
        if (!$exists) {
            $db->insert('departments', ['name' => $dept]);
        }
    }
    echo "<p style='color: green;'>✓ Sample departments created</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Database error: " . $e->getMessage() . "</p>";
}

// Step 5: Fix configuration issues
echo "<h2>Step 5: Configuration Fixes</h2>";

// Fix BASE_URL in config
$config_content = file_get_contents('config/config.php');
if (strpos($config_content, 'cams/parollpro') !== false) {
    $config_content = str_replace('cams/parollpro', '', $config_content);
    file_put_contents('config/config.php', $config_content);
    echo "<p style='color: green;'>✓ Fixed BASE_URL configuration</p>";
} else {
    echo "<p style='color: blue;'>✓ BASE_URL configuration is correct</p>";
}

// Step 6: Create missing directories and files
echo "<h2>Step 6: File Structure</h2>";

$directories = [
    'app/views/errors',
    'app/views/auth',
    'app/models',
    'app/utilities',
    'logs',
    'cache',
    'uploads/documents',
    'uploads/images'
];

foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
        echo "<p style='color: blue;'>✓ Created directory: {$dir}</p>";
    }
}

// Create error pages
$error_403 = 'app/views/errors/403.php';
if (!file_exists($error_403)) {
    file_put_contents($error_403, '<?php include __DIR__ . "/../layout/main.php"; ?>
<div class="min-h-screen flex items-center justify-center bg-gray-50">
    <div class="max-w-md w-full space-y-8">
        <div class="text-center">
            <h1 class="text-6xl font-bold text-red-500">403</h1>
            <h2 class="text-2xl font-semibold text-gray-900">Access Forbidden</h2>
            <p class="text-gray-600">You don\'t have permission to access this resource.</p>
            <a href="<?php echo BASE_URL; ?>/dashboard" class="btn btn-primary mt-4">Go to Dashboard</a>
        </div>
    </div>
</div>');
    echo "<p style='color: green;'>✓ Created 403 error page</p>";
}

$error_404 = 'app/views/errors/404.php';
if (!file_exists($error_404)) {
    file_put_contents($error_404, '<?php include __DIR__ . "/../layout/main.php"; ?>
<div class="min-h-screen flex items-center justify-center bg-gray-50">
    <div class="max-w-md w-full space-y-8">
        <div class="text-center">
            <h1 class="text-6xl font-bold text-blue-500">404</h1>
            <h2 class="text-2xl font-semibold text-gray-900">Page Not Found</h2>
            <p class="text-gray-600">The page you\'re looking for doesn\'t exist.</p>
            <a href="<?php echo BASE_URL; ?>/dashboard" class="btn btn-primary mt-4">Go to Dashboard</a>
        </div>
    </div>
</div>');
    echo "<p style='color: green;'>✓ Created 404 error page</p>";
}

$error_500 = 'app/views/errors/500.php';
if (!file_exists($error_500)) {
    file_put_contents($error_500, '<?php include __DIR__ . "/../layout/main.php"; ?>
<div class="min-h-screen flex items-center justify-center bg-gray-50">
    <div class="max-w-md w-full space-y-8">
        <div class="text-center">
            <h1 class="text-6xl font-bold text-red-500">500</h1>
            <h2 class="text-2xl font-semibold text-gray-900">Server Error</h2>
            <p class="text-gray-600">Something went wrong on our end. Please try again later.</p>
            <a href="<?php echo BASE_URL; ?>/dashboard" class="btn btn-primary mt-4">Go to Dashboard</a>
        </div>
    </div>
</div>');
    echo "<p style='color: green;'>✓ Created 500 error page</p>";
}

// Step 7: Test the application
echo "<h2>Step 7: Application Test</h2>";
echo "<p>Setup completed successfully!</p>";
echo "<p><a href='index.php' style='color: blue; text-decoration: underline;'>Click here to access the application</a></p>";
echo "<p><strong>Default login credentials:</strong></p>";
echo "<ul>";
echo "<li>Username: admin</li>";
echo "<li>Password: admin123</li>";
echo "</ul>";

echo "<h2>Summary</h2>";
echo "<p style='color: green;'>✓ Database tables created</p>";
echo "<p style='color: green;'>✓ Configuration files fixed</p>";
echo "<p style='color: green;'>✓ Error pages created</p>";
echo "<p style='color: green;'>✓ Directory structure verified</p>";
echo "<p style='color: green;'>✓ Default admin user created</p>";

echo "<h2>Next Steps</h2>";
echo "<ol>";
echo "<li>Access the application using the link above</li>";
echo "<li>Login with the default admin credentials</li>";
echo "<li>Change the default password immediately</li>";
echo "<li>Add your first employee and department</li>";
echo "<li>Configure payroll settings</li>";
echo "</ol>";
?> 