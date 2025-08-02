<!-- <?php
/**
 * Debug Information Script
 * Use this to troubleshoot routing and configuration issues
 */

echo "<h1>PayrollPro Debug Information</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .info { background: #e3f2fd; padding: 10px; margin: 10px 0; border-radius: 5px; }
    .error { background: #ffebee; padding: 10px; margin: 10px 0; border-radius: 5px; }
    .success { background: #e8f5e8; padding: 10px; margin: 10px 0; border-radius: 5px; }
</style>";

echo "<div class='info'>";
echo "<h2>Server Information</h2>";
echo "<strong>Document Root:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "<strong>Script Name:</strong> " . $_SERVER['SCRIPT_NAME'] . "<br>";
echo "<strong>Request URI:</strong> " . $_SERVER['REQUEST_URI'] . "<br>";
echo "<strong>HTTP Host:</strong> " . $_SERVER['HTTP_HOST'] . "<br>";
echo "<strong>Server Name:</strong> " . $_SERVER['SERVER_NAME'] . "<br>";
echo "<strong>PHP Version:</strong> " . PHP_VERSION . "<br>";
echo "</div>";

echo "<div class='info'>";
echo "<h2>Path Information</h2>";
echo "<strong>Current Directory:</strong> " . __DIR__ . "<br>";
echo "<strong>File Path:</strong> " . __FILE__ . "<br>";

// Calculate expected paths
$scriptName = $_SERVER['SCRIPT_NAME'];
$basePath = str_replace('/debug_info.php', '', $scriptName);
echo "<strong>Detected Base Path:</strong> " . $basePath . "<br>";

$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$baseUrl = $protocol . '://' . $_SERVER['HTTP_HOST'] . $basePath;
echo "<strong>Calculated Base URL:</strong> " . $baseUrl . "<br>";
echo "</div>";

echo "<div class='info'>";
echo "<h2>File Structure Check</h2>";
$requiredFiles = [
    'config/config.php',
    'config/database.php',
    'app/core/Controller.php',
    'app/core/Model.php',
    'app/controllers/AuthController.php',
    'app/controllers/DashboardController.php',
    'public/index.php',
    'public/css/app.css',
    'public/js/app.js'
];

foreach ($requiredFiles as $file) {
    if (file_exists($file)) {
        echo "<span style='color: green;'>✓</span> {$file}<br>";
    } else {
        echo "<span style='color: red;'>✗</span> {$file} - Missing<br>";
    }
}
echo "</div>";

echo "<div class='info'>";
echo "<h2>Configuration Test</h2>";
if (file_exists('config/config.php')) {
    require_once 'config/config.php';
    echo "<strong>BASE_URL:</strong> " . (defined('BASE_URL') ? BASE_URL : 'Not defined') . "<br>";
    echo "<strong>APP_NAME:</strong> " . (defined('APP_NAME') ? APP_NAME : 'Not defined') . "<br>";
    echo "<strong>APP_DEBUG:</strong> " . (defined('APP_DEBUG') ? (APP_DEBUG ? 'true' : 'false') : 'Not defined') . "<br>";
} else {
    echo "<span style='color: red;'>Config file not found</span><br>";
}
echo "</div>";

echo "<div class='info'>";
echo "<h2>Database Connection Test</h2>";
if (file_exists('config/database.php')) {
    try {
        require_once 'config/database.php';
        $db = new Database();
        $connection = $db->getConnection();
        if ($connection) {
            echo "<span style='color: green;'>✓ Database connection successful</span><br>";
            
            // Test a simple query
            $result = $db->fetch("SELECT COUNT(*) as count FROM users");
            echo "<span style='color: green;'>✓ Database query test passed. Users count: " . ($result['count'] ?? 0) . "</span><br>";
        }
    } catch (Exception $e) {
        echo "<span style='color: red;'>✗ Database connection failed: " . $e->getMessage() . "</span><br>";
    }
} else {
    echo "<span style='color: red;'>Database config file not found</span><br>";
}
echo "</div>";

echo "<div class='info'>";
echo "<h2>URL Testing</h2>";
echo "<strong>Test URLs:</strong><br>";
echo "• Login: <a href='{$baseUrl}/public/login'>{$baseUrl}/public/login</a><br>";
echo "• Dashboard: <a href='{$baseUrl}/public/dashboard'>{$baseUrl}/public/dashboard</a><br>";
echo "• Employees: <a href='{$baseUrl}/public/employees'>{$baseUrl}/public/employees</a><br>";
echo "</div>";

echo "<div class='info'>";
echo "<h2>Recommended Actions</h2>";
echo "1. Ensure all files are uploaded to the correct directory<br>";
echo "2. Update database credentials in config/database.php<br>";
echo "3. Import the database.sql file<br>";
echo "4. Set proper file permissions (755 for directories, 644 for files)<br>";
echo "5. Access the system via: <strong>{$baseUrl}/public/</strong><br>";
echo "</div>";

?> -->

<?php
/**
 * Front Controller - Entry point for the PayrollPro application
 */

// Start session
session_start();

// Enable full error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define paths
define('ROOT_PATH', __DIR__);
define('APP_PATH', ROOT_PATH . '/app');
define('CONFIG_PATH', ROOT_PATH . '/config');

// Load configuration and core
require_once CONFIG_PATH . '/config.php';
require_once CONFIG_PATH . '/database.php';
require_once APP_PATH . '/core/Controller.php';
require_once APP_PATH . '/core/Model.php';

// Router class
class Router {
    private $routes = [];
    private $basePath = '';

    public function __construct() {
        $scriptName = $_SERVER['SCRIPT_NAME'];
        $this->basePath = str_replace('/index.php', '', $scriptName);
        error_log("Detected base path: " . $this->basePath);
    }

    public function addRoute($method, $pattern, $controller, $action) {
        $this->routes[] = compact('method', 'pattern', 'controller', 'action');
    }

    public function dispatch($requestUri, $requestMethod) {
        $uri = parse_url($requestUri, PHP_URL_PATH);

        if ($this->basePath && strpos($uri, $this->basePath) === 0) {
            $uri = substr($uri, strlen($this->basePath));
        }

        $uri = trim($uri, '/');
        error_log("Final URI for routing: /$uri");

        if ($uri === '') {
            $uri = '/';
        } else {
            $uri = '/' . $uri;
        }

        foreach ($this->routes as $route) {
            if ($route['method'] !== $requestMethod && $route['method'] !== 'ANY') {
                continue;
            }

            $pattern = '#^' . preg_replace('/\{(\w+)\}/', '([^/]+)', $route['pattern']) . '$#';

            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches);

                $controllerName = $route['controller'] . 'Controller';
                $controllerFile = APP_PATH . "/controllers/{$controllerName}.php";

                if (file_exists($controllerFile)) {
                    require_once $controllerFile;

                    if (class_exists($controllerName)) {
                        $controller = new $controllerName();
                        $action = $route['action'];

                        if (method_exists($controller, $action)) {
                            call_user_func_array([$controller, $action], $matches);
                            return;
                        }
                    }
                }
            }
        }

        $this->show404();
    }

    private function show404() {
        http_response_code(404);
        if (file_exists(APP_PATH . '/views/errors/404.php')) {
            include APP_PATH . '/views/errors/404.php';
        } else {
            echo '<h1>404 - Page Not Found</h1>';
        }
    }

    public function getBasePath() {
        return $this->basePath;
    }
}

// Initialize router
$router = new Router();

// Define routes
$router->addRoute('GET', '/', 'Dashboard', 'index');
$router->addRoute('GET', '/dashboard', 'Dashboard', 'index');

// Authentication routes
$router->addRoute('GET', '/login', 'Auth', 'login');
$router->addRoute('POST', '/login', 'Auth', 'login');
$router->addRoute('GET', '/logout', 'Auth', 'logout');
$router->addRoute('GET', '/profile', 'Auth', 'profile');
$router->addRoute('POST', '/profile', 'Auth', 'profile');
$router->addRoute('GET', '/change-password', 'Auth', 'changePassword');
$router->addRoute('POST', '/change-password', 'Auth', 'changePassword');

// Employee routes
$router->addRoute('GET', '/employees', 'Employee', 'index');
$router->addRoute('GET', '/employees/create', 'Employee', 'create');
$router->addRoute('POST', '/employees/create', 'Employee', 'create');
$router->addRoute('GET', '/employees/{id}', 'Employee', 'view');
$router->addRoute('GET', '/employees/{id}/edit', 'Employee', 'edit');
$router->addRoute('POST', '/employees/{id}/edit', 'Employee', 'edit');
$router->addRoute('POST', '/employees/{id}/delete', 'Employee', 'delete');
$router->addRoute('GET', '/employees/{id}/salary-structure', 'Employee', 'salaryStructure');
$router->addRoute('POST', '/employees/{id}/salary-structure', 'Employee', 'salaryStructure');
$router->addRoute('POST', '/employees/{id}/upload-document', 'Employee', 'uploadDocument');
$router->addRoute('GET', '/employees/export', 'Employee', 'export');

// Payroll routes
$router->addRoute('GET', '/payroll', 'Payroll', 'index');
$router->addRoute('POST', '/payroll', 'Payroll', 'index');

$router->addRoute('GET', '/payroll/payroll', 'Payroll', 'payroll');
$router->addRoute('POST', '/payroll/payroll', 'Payroll', 'payroll');


$router->addRoute('GET', '/payroll/periods', 'Payroll', 'periods');
$router->addRoute('POST', '/payroll/periods', 'Payroll', 'periods');
$router->addRoute('GET', '/payroll/process', 'Payroll', 'process');
$router->addRoute('POST', '/payroll/process', 'Payroll', 'process');
$router->addRoute('POST', '/payroll/lock-period', 'Payroll', 'lockPeriod');
$router->addRoute('GET', '/payroll/payslip/{employeeId}/{periodId}', 'Payroll', 'payslip');

$router->addRoute('GET', '/cost-centers', 'CostCenter', 'index');
$router->addRoute('POST', '/cost-centers', 'CostCenter', 'index');

// Settings routes
$router->addRoute('GET', '/settings', 'Settings', 'index');
$router->addRoute('POST', '/settings/general', 'Settings', 'updateGeneral');
$router->addRoute('POST', '/settings/payroll', 'Settings', 'updatePayroll');
$router->addRoute('POST', '/settings/email', 'Settings', 'updateEmail');
$router->addRoute('POST', '/settings/test-email', 'Settings', 'testEmail');
$router->addRoute('GET', '/settings/backup', 'Settings', 'backup');
$router->addRoute('POST', '/settings/backup', 'Settings', 'backup');

// Payslip management routes
$router->addRoute('GET', '/payroll/payslips', 'Payroll', 'payslips');
$router->addRoute('POST', '/payroll/email-payslip/{employeeId}/{periodId}', 'Payroll', 'emailPayslip');

// Tax slab routes
$router->addRoute('GET', '/tax-slabs', 'TaxSlab', 'index');
$router->addRoute('POST', '/tax-slabs', 'TaxSlab', 'index');

// Master data routes
$router->addRoute('GET', '/departments', 'Department', 'index');
$router->addRoute('POST', '/departments', 'Department', 'index');
$router->addRoute('GET', '/designations', 'Designation', 'index');
$router->addRoute('POST', '/designations', 'Designation', 'index');
$router->addRoute('GET', '/salary-components', 'SalaryComponent', 'index');
$router->addRoute('POST', '/salary-components', 'SalaryComponent', 'index');
$router->addRoute('GET', '/loan-types', 'LoanType', 'index');
$router->addRoute('POST', '/loan-types', 'LoanType', 'index');
$router->addRoute('GET', '/leave-types', 'LeaveType', 'index');
$router->addRoute('POST', '/leave-types', 'LeaveType', 'index');
$router->addRoute('GET', '/holidays', 'Holiday', 'index');
$router->addRoute('POST', '/holidays', 'Holiday', 'index');

$router->addRoute('GET', '/pf', 'PF', 'index');
$router->addRoute('GET', '/pf/ecr-generation', 'PF', 'ecrGeneration');
$router->addRoute('POST', '/pf/ecr-generation', 'PF', 'ecrGeneration');
$router->addRoute('GET', '/pf/reports', 'PF', 'pfReports');
$router->addRoute('POST', '/pf/reports', 'PF', 'pfReports');
$router->addRoute('GET', '/pf/contributions', 'PF', 'pfContributions');
$router->addRoute('GET', '/pf/reconciliation', 'PF', 'pfReconciliation');
$router->addRoute('POST', '/pf/reconciliation', 'PF', 'pfReconciliation');
$router->addRoute('GET', '/pf/settings', 'PF', 'pfSettings');
$router->addRoute('POST', '/pf/settings', 'PF', 'pfSettings');

// Report routes
$router->addRoute('GET', '/reports', 'Report', 'index');
$router->addRoute('GET', '/reports/salary-register', 'Report', 'salaryRegister');
$router->addRoute('POST', '/reports/salary-register', 'Report', 'salaryRegister');
$router->addRoute('GET', '/reports/component-report', 'Report', 'componentReport');
$router->addRoute('POST', '/reports/component-report', 'Report', 'componentReport');
$router->addRoute('GET', '/reports/bank-transfer', 'Report', 'bankTransfer');
$router->addRoute('POST', '/reports/bank-transfer', 'Report', 'bankTransfer');
$router->addRoute('GET', '/reports/payslip', 'Report', 'payslip');

// Attendance routes
$router->addRoute('GET', '/attendance', 'Attendance', 'index');
$router->addRoute('GET', '/attendance/mark', 'Attendance', 'mark');
$router->addRoute('POST', '/attendance/mark', 'Attendance', 'mark');
$router->addRoute('POST', '/attendance/bulk-mark', 'Attendance', 'bulkMark');
$router->addRoute('GET', '/attendance/report', 'Attendance', 'report');
$router->addRoute('POST', '/attendance/report', 'Attendance', 'report');

// Loan routes
$router->addRoute('GET', '/loans', 'Loan', 'index');
$router->addRoute('GET', '/loans/create', 'Loan', 'create');
$router->addRoute('POST', '/loans/create', 'Loan', 'create');
$router->addRoute('GET', '/loans/{id}', 'Loan', 'view');
$router->addRoute('GET', '/loans/{id}/payment', 'Loan', 'payment');
$router->addRoute('POST', '/loans/{id}/payment', 'Loan', 'payment');

// User management routes
$router->addRoute('GET', '/users', 'User', 'index');
$router->addRoute('GET', '/users/create', 'User', 'create');
$router->addRoute('POST', '/users/create', 'User', 'create');
$router->addRoute('GET', '/users/{id}/edit', 'User', 'edit');
$router->addRoute('POST', '/users/{id}/edit', 'User', 'edit');

// API routes for AJAX calls
$router->addRoute('GET', '/api/dashboard-widgets', 'Dashboard', 'getWidgetData');
$router->addRoute('GET', '/api/attendance-summary', 'Attendance', 'getSummary');
$router->addRoute('GET', '/api/current-period', 'Payroll', 'getCurrentPeriod');
$router->addRoute('GET', '/api/attendance-summary', 'Api', 'attendanceSummary');
$router->addRoute('GET', '/api/current-period', 'Api', 'currentPeriod');
$router->addRoute('GET', '/api/employee-search', 'Api', 'employeeSearch');
$router->addRoute('GET', '/api/salary-calculator', 'Api', 'salaryCalculator');

// Notification routes
$router->addRoute('GET', '/notifications', 'Notification', 'index');
$router->addRoute('POST', '/notifications/mark-read', 'Notification', 'markRead');
$router->addRoute('POST', '/notifications/mark-all-read', 'Notification', 'markAllRead');
$router->addRoute('POST', '/notifications/delete', 'Notification', 'delete');
$router->addRoute('POST', '/notifications/clear-all', 'Notification', 'clearAll');
$router->addRoute('GET', '/api/notifications/unread-count', 'Notification', 'getUnreadCount');

// Backup routes
$router->addRoute('GET', '/settings/backups', 'Backup', 'index');
$router->addRoute('POST', '/settings/backups', 'Backup', 'index');

// ESI routes
$router->addRoute('GET', '/esi', 'ESI', 'index');
$router->addRoute('GET', '/esi/contributions', 'ESI', 'esiContributions');
$router->addRoute('GET', '/esi/reports', 'ESI', 'esiReports');
$router->addRoute('POST', '/esi/reports', 'ESI', 'esiReports');
$router->addRoute('GET', '/esi/settings', 'ESI', 'esiSettings');
$router->addRoute('POST', '/esi/settings', 'ESI', 'esiSettings');

// Additional API routes
$router->addRoute('GET', '/api/generate-employee-code', 'Employee', 'generateEmployeeCode');
$router->addRoute('POST', '/employees/bulk-update-salary', 'Employee', 'bulkUpdateSalary');
$router->addRoute('GET', '/api/period-details', 'Payroll', 'getCurrentPeriod');
$router->addRoute('GET', '/api/employee-salary', 'Api', 'employeeSalary');
$router->addRoute('GET', '/api/employee-salary-structure', 'Api', 'employeeSalaryStructure');
$router->addRoute('GET', '/api/employee-salary-structure', 'Api', 'employeeSalaryStructure');
$router->addRoute('GET', '/api/employee-salary-structure', 'Api', 'employeeSalaryStructure');
$router->addRoute('GET', '/api/employee-salary-structure', 'Api', 'employeeSalaryStructure');

// DEBUG OUTPUT
$routes = $router->getAllRoutes(); // Assume your Router class has this

echo "<h1>Registered Routes Debug</h1>";
echo "<table border='1' cellpadding='8' cellspacing='0'>";
echo "<tr><th>Method</th><th>URI</th><th>Controller</th><th>Action</th><th>Test Link</th></tr>";
foreach ($routes as $route) {
    $method = $route['method'];
    $uri = $route['uri'];
    $controller = $route['controller'];
    $action = $route['action'];
    $link = ($method === 'GET' && strpos($uri, '{') === false)
        ? "<a href='{$uri}' target='_blank'>Test</a>"
        : "-";
    echo "<tr><td>{$method}</td><td>{$uri}</td><td>{$controller}</td><td>{$action}</td><td>{$link}</td></tr>";
}
echo "</table>";
?>