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
require_once APP_PATH . '/views/layout/helper.php';

// Router class
class Router
{
    private $routes = [];
    private $basePath = '';

    public function __construct()
    {
        $scriptName = $_SERVER['SCRIPT_NAME'];
        $this->basePath = str_replace('/index.php', '', $scriptName);
        error_log("Detected base path: " . $this->basePath);
    }

    public function addRoute($method, $pattern, $controller, $action)
    {
        $this->routes[] = compact('method', 'pattern', 'controller', 'action');
    }

    public function dispatch($requestUri, $requestMethod)
    {
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

    private function show404()
    {
        http_response_code(404);
        if (file_exists(APP_PATH . '/views/errors/404.php')) {
            include APP_PATH . '/views/errors/404.php';
        } else {
            echo '<h1>404 - Page Not Found</h1>';
        }
    }

    public function getBasePath()
    {
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

// Master controller routes (alternative routing)
$router->addRoute('GET', '/masters', 'Master', 'index');
$router->addRoute('GET', '/masters/departments', 'Master', 'departments');
$router->addRoute('POST', '/masters/departments', 'Master', 'departments');
$router->addRoute('GET', '/masters/designations', 'Master', 'designations');
$router->addRoute('POST', '/masters/designations', 'Master', 'designations');
$router->addRoute('GET', '/masters/salary-components', 'Master', 'salaryComponents');
$router->addRoute('POST', '/masters/salary-components', 'Master', 'salaryComponents');
$router->addRoute('GET', '/masters/loan-types', 'Master', 'loanTypes');
$router->addRoute('POST', '/masters/loan-types', 'Master', 'loanTypes');
$router->addRoute('GET', '/masters/leave-types', 'Master', 'leaveTypes');
$router->addRoute('POST', '/masters/leave-types', 'Master', 'leaveTypes');
$router->addRoute('GET', '/masters/holidays', 'Master', 'holidays');
$router->addRoute('POST', '/masters/holidays', 'Master', 'holidays');

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

// Advanced Payroll routes
$router->addRoute('GET', '/advanced-payroll', 'AdvancedPayroll', 'index');
$router->addRoute('POST', '/advanced-payroll', 'AdvancedPayroll', 'index');
$router->addRoute('GET', '/advanced-payroll/calculator', 'AdvancedPayroll', 'calculator');
$router->addRoute('POST', '/advanced-payroll/calculator', 'AdvancedPayroll', 'calculator');
$router->addRoute('GET', '/advanced-payroll/formula-editor', 'AdvancedPayroll', 'formulaEditor');
$router->addRoute('POST', '/advanced-payroll/formula-editor', 'AdvancedPayroll', 'formulaEditor');
$router->addRoute('GET', '/advanced-payroll/variable-components', 'AdvancedPayroll', 'variableComponents');
$router->addRoute('POST', '/advanced-payroll/variable-components', 'AdvancedPayroll', 'variableComponents');
$router->addRoute('GET', '/advanced-payroll/bulk-processing', 'AdvancedPayroll', 'bulkProcessing');
$router->addRoute('POST', '/advanced-payroll/bulk-processing', 'AdvancedPayroll', 'bulkProcessing');
$router->addRoute('GET', '/advanced-payroll/arrears-management', 'AdvancedPayroll', 'arrearsManagement');
$router->addRoute('POST', '/advanced-payroll/arrears-management', 'AdvancedPayroll', 'arrearsManagement');

// Advanced Report routes
$router->addRoute('GET', '/advanced-reports', 'AdvancedReport', 'index');
$router->addRoute('GET', '/advanced-reports/analytics-dashboard', 'AdvancedReport', 'analyticsDashboard');
$router->addRoute('POST', '/advanced-reports/analytics-dashboard', 'AdvancedReport', 'analyticsDashboard');
$router->addRoute('GET', '/advanced-reports/custom-builder', 'AdvancedReport', 'customBuilder');
$router->addRoute('POST', '/advanced-reports/custom-builder', 'AdvancedReport', 'customBuilder');
$router->addRoute('GET', '/advanced-reports/attendance-report', 'AdvancedReport', 'attendanceReport');
$router->addRoute('POST', '/advanced-reports/attendance-report', 'AdvancedReport', 'attendanceReport');
$router->addRoute('GET', '/advanced-reports/loan-report', 'AdvancedReport', 'loanReport');
$router->addRoute('POST', '/advanced-reports/loan-report', 'AdvancedReport', 'loanReport');

// Formula Editor routes
$router->addRoute('GET', '/formula-editor', 'FormulaEditor', 'index');
$router->addRoute('GET', '/formula-editor/builder', 'FormulaEditor', 'builder');
$router->addRoute('POST', '/formula-editor/builder', 'FormulaEditor', 'builder');
$router->addRoute('GET', '/formula-editor/query-builder', 'FormulaEditor', 'queryBuilder');
$router->addRoute('POST', '/formula-editor/query-builder', 'FormulaEditor', 'queryBuilder');

// Integration routes
$router->addRoute('GET', '/integrations', 'Integration', 'index');
$router->addRoute('GET', '/integrations/import', 'Integration', 'import');
$router->addRoute('POST', '/integrations/import', 'Integration', 'import');
$router->addRoute('GET', '/integrations/export', 'Integration', 'export');
$router->addRoute('POST', '/integrations/export', 'Integration', 'export');

// Mobile API routes
$router->addRoute('GET', '/mobile-api/attendance', 'MobileApi', 'attendance');
$router->addRoute('POST', '/mobile-api/attendance', 'MobileApi', 'attendance');
$router->addRoute('GET', '/mobile-api/payslip', 'MobileApi', 'payslip');
$router->addRoute('GET', '/mobile-api/profile', 'MobileApi', 'profile');
$router->addRoute('POST', '/mobile-api/profile', 'MobileApi', 'profile');

// Multi Company routes
$router->addRoute('GET', '/multi-company', 'MultiCompany', 'index');
$router->addRoute('GET', '/multi-company/companies', 'MultiCompany', 'companies');
$router->addRoute('POST', '/multi-company/companies', 'MultiCompany', 'companies');
$router->addRoute('GET', '/multi-company/switch/{companyId}', 'MultiCompany', 'switchCompany');
$router->addRoute('GET', '/multi-company/settings', 'MultiCompany', 'settings');
$router->addRoute('POST', '/multi-company/settings', 'MultiCompany', 'settings');

// Setup routes
$router->addRoute('GET', '/setup', 'Setup', 'index');
$router->addRoute('GET', '/setup/install', 'Setup', 'install');
$router->addRoute('POST', '/setup/install', 'Setup', 'install');
$router->addRoute('GET', '/setup/check-requirements', 'Setup', 'checkRequirements');
$router->addRoute('GET', '/setup/database', 'Setup', 'database');
$router->addRoute('POST', '/setup/database', 'Setup', 'database');

// System routes
$router->addRoute('GET', '/system', 'System', 'index');
$router->addRoute('GET', '/system/backup-manager', 'System', 'backupManager');
$router->addRoute('POST', '/system/backup-manager', 'System', 'backupManager');
$router->addRoute('GET', '/system/security-dashboard', 'System', 'securityDashboard');
$router->addRoute('GET', '/system/logs', 'System', 'logs');
$router->addRoute('GET', '/system/performance', 'System', 'performance');

// Tax routes
$router->addRoute('GET', '/tax', 'Tax', 'index');
$router->addRoute('GET', '/tax/calculator', 'Tax', 'calculator');
$router->addRoute('POST', '/tax/calculator', 'Tax', 'calculator');
$router->addRoute('GET', '/tax/reports', 'Tax', 'reports');
$router->addRoute('POST', '/tax/reports', 'Tax', 'reports');

// Workflow routes
$router->addRoute('GET', '/workflow', 'Workflow', 'index');
$router->addRoute('GET', '/workflow/approvals', 'Workflow', 'approvals');
$router->addRoute('POST', '/workflow/approvals', 'Workflow', 'approvals');
$router->addRoute('GET', '/workflow/settings', 'Workflow', 'settings');
$router->addRoute('POST', '/workflow/settings', 'Workflow', 'settings');

// Advanced Analytics routes
$router->addRoute('GET', '/analytics', 'AdvancedAnalytics', 'index');
$router->addRoute('GET', '/analytics/predictions', 'AdvancedAnalytics', 'predictions');
$router->addRoute('POST', '/analytics/predictions', 'AdvancedAnalytics', 'predictions');
$router->addRoute('GET', '/analytics/trends', 'AdvancedAnalytics', 'trends');
$router->addRoute('GET', '/analytics/anomalies', 'AdvancedAnalytics', 'anomalies');
$router->addRoute('GET', '/analytics/insights', 'AdvancedAnalytics', 'insights');
$router->addRoute('GET', '/analytics/forecasting', 'AdvancedAnalytics', 'forecasting');
$router->addRoute('POST', '/analytics/forecasting', 'AdvancedAnalytics', 'forecasting');
$router->addRoute('GET', '/analytics/optimization', 'AdvancedAnalytics', 'optimization');

// Blockchain routes
$router->addRoute('GET', '/blockchain', 'Blockchain', 'index');
$router->addRoute('GET', '/blockchain/verify', 'Blockchain', 'verifyPayroll');
$router->addRoute('POST', '/blockchain/verify', 'Blockchain', 'verifyPayroll');
$router->addRoute('GET', '/blockchain/audit-trail', 'Blockchain', 'auditTrail');
$router->addRoute('GET', '/blockchain/generate-hash', 'Blockchain', 'generateHash');
$router->addRoute('POST', '/blockchain/generate-hash', 'Blockchain', 'generateHash');
$router->addRoute('GET', '/blockchain/verify-integrity', 'Blockchain', 'verifyIntegrity');
$router->addRoute('GET', '/blockchain/export', 'Blockchain', 'exportBlockchain');
$router->addRoute('GET', '/blockchain/smart-contract', 'Blockchain', 'smartContract');
$router->addRoute('POST', '/blockchain/smart-contract', 'Blockchain', 'smartContract');

// API routes for advanced features
$router->addRoute('GET', '/api/analytics/dashboard', 'AdvancedAnalytics', 'getDashboardData');
$router->addRoute('GET', '/api/analytics/predictions', 'AdvancedAnalytics', 'getPredictions');
$router->addRoute('GET', '/api/blockchain/status', 'Blockchain', 'getBlockchainStatus');
$router->addRoute('GET', '/api/blockchain/verify-hash', 'Blockchain', 'verifyHash');

// Advanced Settings routes
$router->addRoute('GET', '/admin/settings', 'AdvancedSettings', 'index');
$router->addRoute('GET', '/admin/settings/system', 'AdvancedSettings', 'systemSettings');
$router->addRoute('POST', '/admin/settings/system', 'AdvancedSettings', 'systemSettings');
$router->addRoute('GET', '/admin/settings/payroll', 'AdvancedSettings', 'payrollSettings');
$router->addRoute('POST', '/admin/settings/payroll', 'AdvancedSettings', 'payrollSettings');
$router->addRoute('GET', '/admin/bulk-operations', 'AdvancedSettings', 'bulkOperations');
$router->addRoute('GET', '/admin/bulk-salary-update', 'AdvancedSettings', 'bulkSalaryUpdate');
$router->addRoute('POST', '/admin/bulk-salary-update', 'AdvancedSettings', 'bulkSalaryUpdate');
$router->addRoute('GET', '/admin/bulk-variable-entry', 'AdvancedSettings', 'bulkVariableEntry');
$router->addRoute('POST', '/admin/bulk-variable-entry', 'AdvancedSettings', 'bulkVariableEntry');
$router->addRoute('GET', '/admin/import-export', 'AdvancedSettings', 'importExport');
$router->addRoute('POST', '/admin/import-export', 'AdvancedSettings', 'importExport');
$router->addRoute('GET', '/admin/bulk-salary-processing', 'AdvancedSettings', 'bulkSalaryProcessing');
$router->addRoute('POST', '/admin/bulk-salary-processing', 'AdvancedSettings', 'bulkSalaryProcessing');
$router->addRoute('GET', '/admin/salary-structure-bulk', 'AdvancedSettings', 'salaryStructureBulk');
$router->addRoute('POST', '/admin/salary-structure-bulk', 'AdvancedSettings', 'salaryStructureBulk');
$router->addRoute('GET', '/admin/data-backup', 'AdvancedSettings', 'dataBackup');
$router->addRoute('POST', '/admin/data-backup', 'AdvancedSettings', 'dataBackup');
$router->addRoute('GET', '/admin/system-optimization', 'AdvancedSettings', 'systemOptimization');
$router->addRoute('POST', '/admin/system-optimization', 'AdvancedSettings', 'systemOptimization');
$router->addRoute('GET', '/admin/api-settings', 'AdvancedSettings', 'apiSettings');
$router->addRoute('POST', '/admin/api-settings', 'AdvancedSettings', 'apiSettings');

// API routes for bulk operations
$router->addRoute('GET', '/api/bulk/employees', 'AdvancedSettings', 'getEmployeesForBulk');
$router->addRoute('POST', '/api/bulk/process-salary', 'AdvancedSettings', 'processBulkSalary');
$router->addRoute('POST', '/api/bulk/process-variable', 'AdvancedSettings', 'processBulkVariable');
$router->addRoute('POST', '/api/bulk/assign-salary-structure', 'AdvancedSettings', 'assignBulkSalaryStructure');
$router->addRoute('GET', '/api/bulk/export-template', 'AdvancedSettings', 'exportTemplate');
$router->addRoute('POST', '/api/bulk/import-data', 'AdvancedSettings', 'importData');

// Audit Trail routes
$router->addRoute('GET', '/audit', 'Audit', 'index');
$router->addRoute('GET', '/audit/bulk-operations', 'Audit', 'bulkOperations');
$router->addRoute('GET', '/audit/user-trail', 'Audit', 'userTrail');
$router->addRoute('GET', '/audit/export', 'Audit', 'export');
$router->addRoute('GET', '/audit/clean-logs', 'Audit', 'cleanLogs');
$router->addRoute('POST', '/audit/clean-logs', 'Audit', 'cleanLogs');
$router->addRoute('GET', '/api/audit/statistics', 'Audit', 'getStatistics');
$router->addRoute('GET', '/api/audit/live-feed', 'Audit', 'getLiveFeed');

// Scheduled Operations routes
$router->addRoute('GET', '/scheduled-operations', 'ScheduledOperation', 'index');
$router->addRoute('GET', '/scheduled-operations/create', 'ScheduledOperation', 'create');
$router->addRoute('POST', '/scheduled-operations/create', 'ScheduledOperation', 'create');
$router->addRoute('GET', '/scheduled-operations/edit', 'ScheduledOperation', 'edit');
$router->addRoute('POST', '/scheduled-operations/edit', 'ScheduledOperation', 'edit');
$router->addRoute('GET', '/scheduled-operations/delete', 'ScheduledOperation', 'delete');
$router->addRoute('GET', '/scheduled-operations/pause', 'ScheduledOperation', 'pause');
$router->addRoute('GET', '/scheduled-operations/resume', 'ScheduledOperation', 'resume');
$router->addRoute('POST', '/scheduled-operations/execute', 'ScheduledOperation', 'execute');
$router->addRoute('GET', '/api/scheduled-operations/details', 'ScheduledOperation', 'getDetails');

// Template Management routes
$router->addRoute('GET', '/templates', 'Template', 'index');
$router->addRoute('GET', '/templates/create', 'Template', 'create');
$router->addRoute('POST', '/templates/create', 'Template', 'create');
$router->addRoute('GET', '/templates/edit', 'Template', 'edit');
$router->addRoute('POST', '/templates/edit', 'Template', 'edit');
$router->addRoute('GET', '/templates/delete', 'Template', 'delete');
$router->addRoute('GET', '/templates/export', 'Template', 'export');
$router->addRoute('POST', '/templates/import', 'Template', 'import');
$router->addRoute('POST', '/api/templates/apply', 'Template', 'applyTemplate');
$router->addRoute('GET', '/api/templates/by-type', 'Template', 'getTemplatesByType');

// Advanced Validation routes
$router->addRoute('GET', '/validation-rules', 'ValidationRule', 'index');
$router->addRoute('GET', '/validation-rules/create', 'ValidationRule', 'create');
$router->addRoute('POST', '/validation-rules/create', 'ValidationRule', 'create');
$router->addRoute('GET', '/validation-rules/edit', 'ValidationRule', 'edit');
$router->addRoute('POST', '/validation-rules/edit', 'ValidationRule', 'edit');
$router->addRoute('GET', '/validation-rules/delete', 'ValidationRule', 'delete');
$router->addRoute('POST', '/api/validation-rules/validate', 'ValidationRule', 'validateData');
$router->addRoute('GET', '/api/validation-rules/by-type', 'ValidationRule', 'getValidationRulesByType');

// Redirect base URL
$cleanUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$basePath = $router->getBasePath();
if ($cleanUri === $basePath || $cleanUri === $basePath . '/') {
    if (isset($_SESSION['user_id'])) {
        header('Location: ' . $basePath . '/dashboard');
    } else {
        header('Location: ' . $basePath . '/login');
    }
    exit;
}

// Dispatch
try {
    $router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
} catch (Exception $e) {
    error_log("Application error: " . $e->getMessage());
    http_response_code(500);
    if (file_exists(APP_PATH . '/views/errors/500.php')) {
        include APP_PATH . '/views/errors/500.php';
    } else {
        echo "<h1>500 - Internal Server Error</h1>";
        echo "<p>An error occurred while processing your request.</p>";
        if (defined('APP_DEBUG') && 'APP_DEBUG') {
            echo "<pre>" . $e->getMessage() . "</pre>";
        }
    }
}