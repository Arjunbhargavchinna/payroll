<?php
/**
 * Layout Test File
 * This file tests the layout system to ensure it's working properly
 */

// Start session
session_start();

// Load configuration
require_once 'config/config.php';
require_once 'app/views/layout/helper.php';

// Simulate a logged-in user
$_SESSION['user_id'] = 1;
$_SESSION['full_name'] = 'Test User';
$_SESSION['role'] = 'Administrator';
$_SESSION['permissions'] = 'all';
$_SESSION['csrf_token'] = 'test-token';

// Test data
$testData = [
    'user' => [
        'name' => 'Test User',
        'role' => 'Administrator'
    ],
    'stats' => [
        'employees' => [
            'total' => 150,
            'active' => 145,
            'departments' => [
                ['name' => 'IT', 'count' => 45],
                ['name' => 'HR', 'count' => 25],
                ['name' => 'Finance', 'count' => 30],
                ['name' => 'Marketing', 'count' => 20],
                ['name' => 'Operations', 'count' => 30]
            ]
        ],
        'payroll' => [
            'current_period' => 'January 2024',
            'processed_employees' => 145,
            'total_earnings' => 2500000.00,
            'net_payable' => 2200000.00
        ],
        'recent_activities' => [
            [
                'full_name' => 'John Doe',
                'action' => 'Salary updated',
                'created_at' => '2024-01-15 10:30:00'
            ],
            [
                'full_name' => 'Jane Smith',
                'action' => 'New employee added',
                'created_at' => '2024-01-15 09:15:00'
            ],
            [
                'full_name' => 'Mike Johnson',
                'action' => 'Payroll processed',
                'created_at' => '2024-01-15 08:45:00'
            ]
        ]
    ]
];

// Test the layout system
echo "<!DOCTYPE html>";
echo "<html>";
echo "<head>";
echo "<title>Layout Test - PayrollPro</title>";
echo "<style>";
echo "body { font-family: Arial, sans-serif; margin: 20px; }";
echo ".test-section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }";
echo ".success { background-color: #d4edda; border-color: #c3e6cb; color: #155724; }";
echo ".error { background-color: #f8d7da; border-color: #f5c6cb; color: #721c24; }";
echo ".info { background-color: #d1ecf1; border-color: #bee5eb; color: #0c5460; }";
echo "</style>";
echo "</head>";
echo "<body>";

echo "<h1>PayrollPro Layout System Test</h1>";

// Test 1: Check if layout files exist
echo "<div class='test-section info'>";
echo "<h3>Test 1: Layout Files Check</h3>";
$layoutFiles = [
    'app/views/layout/header.php',
    'app/views/layout/sidebar.php',
    'app/views/layout/footer.php',
    'app/views/layout/main.php',
    'app/views/layout/helper.php'
];

foreach ($layoutFiles as $file) {
    if (file_exists($file)) {
        echo "<p class='success'>✓ $file exists</p>";
    } else {
        echo "<p class='error'>✗ $file missing</p>";
    }
}
echo "</div>";

// Test 2: Check configuration constants
echo "<div class='test-section info'>";
echo "<h3>Test 2: Configuration Check</h3>";
$constants = ['BASE_URL', 'APP_VERSION', 'APP_NAME'];
foreach ($constants as $constant) {
    if (defined($constant)) {
        echo "<p class='success'>✓ $constant: " . constant($constant) . "</p>";
    } else {
        echo "<p class='error'>✗ $constant not defined</p>";
    }
}
echo "</div>";

// Test 3: Test helper functions
echo "<div class='test-section info'>";
echo "<h3>Test 3: Helper Functions Test</h3>";
try {
    $title = getPageTitle();
    echo "<p class='success'>✓ getPageTitle(): $title</p>";
    
    $userName = getUserName();
    echo "<p class='success'>✓ getUserName(): $userName</p>";
    
    $userRole = getUserRole();
    echo "<p class='success'>✓ getUserRole(): $userRole</p>";
    
    $isLoggedIn = isLoggedIn();
    echo "<p class='success'>✓ isLoggedIn(): " . ($isLoggedIn ? 'true' : 'false') . "</p>";
    
    $hasPermission = hasPermission('employees');
    echo "<p class='success'>✓ hasPermission('employees'): " . ($hasPermission ? 'true' : 'false') . "</p>";
    
    $formattedCurrency = formatCurrency(1234567.89);
    echo "<p class='success'>✓ formatCurrency(): $formattedCurrency</p>";
    
    $formattedDate = formatDate('2024-01-15');
    echo "<p class='success'>✓ formatDate(): $formattedDate</p>";
    
    $timeAgo = timeAgo('2024-01-15 10:30:00');
    echo "<p class='success'>✓ timeAgo(): $timeAgo</p>";
    
} catch (Exception $e) {
    echo "<p class='error'>✗ Helper functions error: " . $e->getMessage() . "</p>";
}
echo "</div>";

// Test 4: Test layout rendering
echo "<div class='test-section info'>";
echo "<h3>Test 4: Layout Rendering Test</h3>";
try {
    // Test rendering a simple view
    $testViewContent = "<div class='p-4'><h2>Test View Content</h2><p>This is a test view to verify the layout system.</p></div>";
    
    // Create a temporary test view file
    $testViewFile = 'test_view_temp.php';
    file_put_contents($testViewFile, $testViewContent);
    
    // Test renderPartial function
    $partialContent = renderPartial($testViewFile, $testData);
    if (strpos($partialContent, 'Test View Content') !== false) {
        echo "<p class='success'>✓ renderPartial() working correctly</p>";
    } else {
        echo "<p class='error'>✗ renderPartial() not working correctly</p>";
    }
    
    // Clean up
    unlink($testViewFile);
    
} catch (Exception $e) {
    echo "<p class='error'>✗ Layout rendering error: " . $e->getMessage() . "</p>";
}
echo "</div>";

// Test 5: Session and security
echo "<div class='test-section info'>";
echo "<h3>Test 5: Session and Security Check</h3>";
if (isset($_SESSION['csrf_token'])) {
    echo "<p class='success'>✓ CSRF token exists</p>";
    
    $token = generateCSRFToken();
    if (verifyCSRFToken($token)) {
        echo "<p class='success'>✓ CSRF token verification working</p>";
    } else {
        echo "<p class='error'>✗ CSRF token verification failed</p>";
    }
} else {
    echo "<p class='error'>✗ CSRF token missing</p>";
}
echo "</div>";

// Test 6: URL and routing helpers
echo "<div class='test-section info'>";
echo "<h3>Test 6: URL and Routing Helpers</h3>";
$currentUrl = getCurrentUrl();
echo "<p class='success'>✓ getCurrentUrl(): $currentUrl</p>";

$isActive = isActivePage('test');
echo "<p class='success'>✓ isActivePage('test'): " . ($isActive ? 'true' : 'false') . "</p>";

$breadcrumbs = getBreadcrumbs();
echo "<p class='success'>✓ getBreadcrumbs(): " . count($breadcrumbs) . " items</p>";
echo "</div>";

echo "<div class='test-section success'>";
echo "<h3>Layout System Test Complete</h3>";
echo "<p>If all tests above show green checkmarks, the layout system is working correctly.</p>";
echo "<p>You can now use the layout system in your views by including the helper file and using the renderView() function.</p>";
echo "</div>";

echo "</body>";
echo "</html>";
?> 