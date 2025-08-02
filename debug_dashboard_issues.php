<?php
/**
 * Debug Dashboard, Payroll, and Attendance Issues
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Debug Dashboard, Payroll, and Attendance Issues</h1>";

// Load config
require_once 'config/config.php';

// Step 1: Check database connection and tables
echo "<h2>Step 1: Database Connection and Tables</h2>";

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p style='color: green;'>✓ Database connection successful</p>";
    
    // Check required tables
    $required_tables = [
        'employees',
        'departments', 
        'designations',
        'payroll_periods',
        'payroll_transactions',
        'salary_components',
        'attendance',
        'audit_logs',
        'users'
    ];
    
    foreach ($required_tables as $table) {
        try {
            $stmt = $pdo->query("SELECT COUNT(*) FROM {$table}");
            $count = $stmt->fetchColumn();
            echo "<p style='color: green;'>✓ Table '{$table}' exists ({$count} records)</p>";
        } catch (Exception $e) {
            echo "<p style='color: red;'>✗ Table '{$table}' missing or inaccessible</p>";
        }
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Database connection failed: " . $e->getMessage() . "</p>";
    exit;
}

// Step 2: Check if required data exists
echo "<h2>Step 2: Required Data Check</h2>";

// Check for departments
$dept_count = $pdo->query("SELECT COUNT(*) FROM departments")->fetchColumn();
if ($dept_count == 0) {
    echo "<p style='color: orange;'>⚠ No departments found - creating sample departments</p>";
    $pdo->exec("INSERT INTO departments (name, code, status, created_at) VALUES 
                ('Human Resources', 'HR', 'active', NOW()),
                ('Information Technology', 'IT', 'active', NOW()),
                ('Finance', 'FIN', 'active', NOW()),
                ('Operations', 'OPS', 'active', NOW())");
    echo "<p style='color: green;'>✓ Sample departments created</p>";
} else {
    echo "<p style='color: green;'>✓ {$dept_count} departments found</p>";
}

// Check for designations
$desig_count = $pdo->query("SELECT COUNT(*) FROM designations")->fetchColumn();
if ($desig_count == 0) {
    echo "<p style='color: orange;'>⚠ No designations found - creating sample designations</p>";
    $pdo->exec("INSERT INTO designations (name, code, status, created_at) VALUES 
                ('Manager', 'MGR', 'active', NOW()),
                ('Senior Executive', 'SE', 'active', NOW()),
                ('Executive', 'EXE', 'active', NOW()),
                ('Assistant', 'AST', 'active', NOW())");
    echo "<p style='color: green;'>✓ Sample designations created</p>";
} else {
    echo "<p style='color: green;'>✓ {$desig_count} designations found</p>";
}

// Check for employees
$emp_count = $pdo->query("SELECT COUNT(*) FROM employees")->fetchColumn();
if ($emp_count == 0) {
    echo "<p style='color: orange;'>⚠ No employees found - creating sample employees</p>";
    
    // Get first department and designation
    $dept_id = $pdo->query("SELECT id FROM departments LIMIT 1")->fetchColumn();
    $desig_id = $pdo->query("SELECT id FROM designations LIMIT 1")->fetchColumn();
    
    if ($dept_id && $desig_id) {
        $pdo->exec("INSERT INTO employees (emp_code, first_name, last_name, email, phone, 
                    join_date, department_id, designation_id, status, created_at) VALUES 
                    ('EMP001', 'John', 'Doe', 'john.doe@example.com', '1234567890', 
                     CURDATE(), {$dept_id}, {$desig_id}, 'active', NOW()),
                    ('EMP002', 'Jane', 'Smith', 'jane.smith@example.com', '0987654321', 
                     CURDATE(), {$dept_id}, {$desig_id}, 'active', NOW())");
        echo "<p style='color: green;'>✓ Sample employees created</p>";
    }
} else {
    echo "<p style='color: green;'>✓ {$emp_count} employees found</p>";
}

// Check for payroll periods
$period_count = $pdo->query("SELECT COUNT(*) FROM payroll_periods")->fetchColumn();
if ($period_count == 0) {
    echo "<p style='color: orange;'>⚠ No payroll periods found - creating sample periods</p>";
    
    $current_month = date('Y-m-01');
    $next_month = date('Y-m-01', strtotime('+1 month'));
    $period_name = date('F Y');
    
    $pdo->exec("INSERT INTO payroll_periods (period_name, start_date, end_date, 
                financial_year, status, created_at) VALUES 
                ('{$period_name}', '{$current_month}', '{$next_month}', 
                 '" . date('Y') . "-" . (date('Y')+1) . "', 'open', NOW())");
    echo "<p style='color: green;'>✓ Sample payroll period created</p>";
} else {
    echo "<p style='color: green;'>✓ {$period_count} payroll periods found</p>";
}

// Check for salary components
$comp_count = $pdo->query("SELECT COUNT(*) FROM salary_components")->fetchColumn();
if ($comp_count == 0) {
    echo "<p style='color: orange;'>⚠ No salary components found - creating sample components</p>";
    
    $pdo->exec("INSERT INTO salary_components (name, code, type, is_taxable, 
                display_order, status, created_at) VALUES 
                ('Basic Salary', 'BASIC', 'earning', 1, 1, 'active', NOW()),
                ('House Rent Allowance', 'HRA', 'earning', 0, 2, 'active', NOW()),
                ('Dearness Allowance', 'DA', 'earning', 1, 3, 'active', NOW()),
                ('Provident Fund', 'PF', 'deduction', 0, 4, 'active', NOW()),
                ('Professional Tax', 'PT', 'deduction', 0, 5, 'active', NOW())");
    echo "<p style='color: green;'>✓ Sample salary components created</p>";
} else {
    echo "<p style='color: green;'>✓ {$comp_count} salary components found</p>";
}

// Step 3: Test Dashboard Controller
echo "<h2>Step 3: Test Dashboard Controller</h2>";

// Simulate session
session_start();
$_SESSION['user_id'] = 1;
$_SESSION['full_name'] = 'Admin User';
$_SESSION['role'] = 'admin';

try {
    require_once 'app/core/Controller.php';
    require_once 'app/controllers/DashboardController.php';
    
    $dashboard = new DashboardController();
    echo "<p style='color: green;'>✓ DashboardController loaded successfully</p>";
    
    // Test employee stats
    require_once 'app/models/Employee.php';
    $employeeModel = new Employee();
    $stats = $employeeModel->getEmployeeStats();
    
    echo "<p style='color: green;'>✓ Employee stats retrieved:</p>";
    echo "<ul>";
    echo "<li>Total employees: " . ($stats['total'] ?? 0) . "</li>";
    echo "<li>Departments: " . count($stats['by_department'] ?? []) . "</li>";
    echo "<li>Designations: " . count($stats['by_designation'] ?? []) . "</li>";
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ DashboardController error: " . $e->getMessage() . "</p>";
}

// Step 4: Test Payroll Controller
echo "<h2>Step 4: Test Payroll Controller</h2>";

try {
    require_once 'app/controllers/PayrollController.php';
    $payroll = new PayrollController();
    echo "<p style='color: green;'>✓ PayrollController loaded successfully</p>";
    
    // Test payroll summary
    require_once 'app/models/Payroll.php';
    $payrollModel = new Payroll();
    
    $current_period = $pdo->query("SELECT id FROM payroll_periods WHERE status = 'open' LIMIT 1")->fetchColumn();
    if ($current_period) {
        $summary = $payrollModel->getPayrollSummary($current_period);
        echo "<p style='color: green;'>✓ Payroll summary retrieved for period {$current_period}</p>";
    } else {
        echo "<p style='color: orange;'>⚠ No active payroll period found</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ PayrollController error: " . $e->getMessage() . "</p>";
}

// Step 5: Test Attendance Controller
echo "<h2>Step 5: Test Attendance Controller</h2>";

try {
    require_once 'app/controllers/AttendanceController.php';
    $attendance = new AttendanceController();
    echo "<p style='color: green;'>✓ AttendanceController loaded successfully</p>";
    
    // Test attendance model
    require_once 'app/models/Attendance.php';
    $attendanceModel = new Attendance();
    echo "<p style='color: green;'>✓ Attendance model loaded successfully</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ AttendanceController error: " . $e->getMessage() . "</p>";
}

// Step 6: Check views
echo "<h2>Step 6: Check Views</h2>";

$views_to_check = [
    'app/views/dashboard/index.php',
    'app/views/payroll/index.php',
    'app/views/attendance/index.php',
    'app/views/layout/main.php',
    'app/views/layout/sidebar.php'
];

foreach ($views_to_check as $view) {
    if (file_exists($view)) {
        $size = filesize($view);
        echo "<p style='color: green;'>✓ {$view} exists ({$size} bytes)</p>";
    } else {
        echo "<p style='color: red;'>✗ {$view} missing</p>";
    }
}

// Step 7: Test routing
echo "<h2>Step 7: Test Routing</h2>";

$routes_to_test = [
    '/dashboard' => 'Dashboard',
    '/payroll' => 'Payroll',
    '/attendance' => 'Attendance'
];

foreach ($routes_to_test as $route => $name) {
    echo "<p>Testing {$name} route...</p>";
    echo "<p><a href='" . BASE_URL . $route . "' target='_blank'>Test {$name}</a></p>";
}

// Step 8: Create test data for better functionality
echo "<h2>Step 8: Create Test Data</h2>";

// Create sample attendance records
$today = date('Y-m-d');
$attendance_count = $pdo->query("SELECT COUNT(*) FROM attendance WHERE attendance_date = '{$today}'")->fetchColumn();

if ($attendance_count == 0) {
    echo "<p style='color: orange;'>⚠ No attendance records for today - creating sample records</p>";
    
    $employees = $pdo->query("SELECT id FROM employees WHERE status = 'active' LIMIT 5")->fetchAll(PDO::FETCH_COLUMN);
    
    foreach ($employees as $emp_id) {
        $pdo->exec("INSERT INTO attendance (employee_id, attendance_date, status, 
                    check_in_time, check_out_time, created_at) VALUES 
                    ({$emp_id}, '{$today}', 'present', 
                     '09:00:00', '18:00:00', NOW())");
    }
    echo "<p style='color: green;'>✓ Sample attendance records created</p>";
} else {
    echo "<p style='color: green;'>✓ {$attendance_count} attendance records found for today</p>";
}

// Create sample payroll transactions
$transaction_count = $pdo->query("SELECT COUNT(*) FROM payroll_transactions")->fetchColumn();

if ($transaction_count == 0) {
    echo "<p style='color: orange;'>⚠ No payroll transactions found - creating sample transactions</p>";
    
    $period_id = $pdo->query("SELECT id FROM payroll_periods WHERE status = 'open' LIMIT 1")->fetchColumn();
    $components = $pdo->query("SELECT id, type FROM salary_components WHERE status = 'active'")->fetchAll();
    $employees = $pdo->query("SELECT id FROM employees WHERE status = 'active' LIMIT 3")->fetchAll(PDO::FETCH_COLUMN);
    
    if ($period_id && !empty($components) && !empty($employees)) {
        foreach ($employees as $emp_id) {
            foreach ($components as $comp) {
                $amount = $comp['type'] === 'earning' ? rand(5000, 15000) : rand(500, 2000);
                $pdo->exec("INSERT INTO payroll_transactions (employee_id, period_id, component_id, 
                           amount, created_at) VALUES 
                           ({$emp_id}, {$period_id}, {$comp['id']}, {$amount}, NOW())");
            }
        }
        echo "<p style='color: green;'>✓ Sample payroll transactions created</p>";
    }
} else {
    echo "<p style='color: green;'>✓ {$transaction_count} payroll transactions found</p>";
}

// Step 9: Summary and next steps
echo "<h2>Step 9: Summary and Next Steps</h2>";

echo "<h3>Test Links:</h3>";
echo "<ul>";
echo "<li><a href='" . BASE_URL . "/dashboard' target='_blank'>Test Dashboard</a></li>";
echo "<li><a href='" . BASE_URL . "/payroll' target='_blank'>Test Payroll Overview</a></li>";
echo "<li><a href='" . BASE_URL . "/attendance' target='_blank'>Test Attendance</a></li>";
echo "<li><a href='" . BASE_URL . "/alternative_layout.php' target='_blank'>Test Alternative Layout</a></li>";
echo "</ul>";

echo "<h3>Common Issues and Solutions:</h3>";
echo "<ul>";
echo "<li><strong>500 Errors:</strong> Check if all required tables exist and have data</li>";
echo "<li><strong>Blank Pages:</strong> Check if views are loading correctly</li>";
echo "<li><strong>Missing Data:</strong> Ensure sample data is created</li>";
echo "<li><strong>Permission Errors:</strong> Check user session and permissions</li>";
echo "<li><strong>CSS Issues:</strong> Use the alternative CSS solution</li>";
echo "</ul>";

echo "<h3>Manual Fixes:</h3>";
echo "<ol>";
echo "<li>If dashboard shows no data, run this script again to create sample data</li>";
echo "<li>If payroll shows errors, check if payroll periods exist</li>";
echo "<li>If attendance doesn't work, ensure attendance table has proper structure</li>";
echo "<li>If CSS is broken, use the alternative CSS solution</li>";
echo "<li>Clear browser cache and reload pages</li>";
echo "</ol>";

echo "<p style='color: green; font-weight: bold;'>Debug completed! Test the links above to verify functionality.</p>";
?> 