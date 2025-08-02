<?php
/**
 * Simple Layout Test
 * Tests the layout system without complex dependencies
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
    'message' => 'Layout system is working!',
    'timestamp' => date('Y-m-d H:i:s')
];

// Test the layout system
renderView('app/views/dashboard/index.php', $testData, 'Layout Test - PayrollPro');
?> 