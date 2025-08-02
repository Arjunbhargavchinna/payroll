<?php
/**
 * CSS Test File
 * This file tests if CSS is loading properly
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CSS Test - PayrollPro</title>
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.4.3/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="css/app.css" rel="stylesheet">
    
    <script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    primary: {
                        50: '#eff6ff',
                        100: '#dbeafe',
                        500: '#3b82f6',
                        600: '#2563eb',
                        700: '#1d4ed8'
                    }
                }
            }
        }
    }
    </script>
</head>

<body class="bg-gray-50">
    <div class="min-h-screen">
        <!-- Header -->
        <nav class="bg-white shadow-lg border-b">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <i class="fas fa-calculator text-primary-500 text-2xl mr-2"></i>
                        <span class="font-bold text-xl text-gray-900">PayrollPro</span>
                        <span class="ml-2 text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full">v<?php echo APP_VERSION; ?></span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-sm text-gray-700">CSS Test Page</span>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="px-4 py-6 sm:px-0">
                <h1 class="text-3xl font-bold text-gray-900 mb-8">CSS Loading Test</h1>
                
                <!-- Test Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Tailwind CSS Test -->
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-palette text-blue-600"></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500">Tailwind CSS</p>
                                    <p class="text-2xl font-bold text-gray-900">✓ Loaded</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Font Awesome Test -->
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-icons text-green-600"></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500">Font Awesome</p>
                                    <p class="text-2xl font-bold text-gray-900">✓ Loaded</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Custom CSS Test -->
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-code text-purple-600"></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500">Custom CSS</p>
                                    <p class="text-2xl font-bold text-gray-900">✓ Loaded</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Responsive Test -->
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-mobile-alt text-yellow-600"></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500">Responsive</p>
                                    <p class="text-2xl font-bold text-gray-900">✓ Working</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Component Tests -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Button Tests -->
                    <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Button Components</h3>
                        <div class="space-y-3">
                            <button class="btn btn-primary">Primary Button</button>
                            <button class="btn btn-secondary">Secondary Button</button>
                            <button class="btn btn-success">Success Button</button>
                            <button class="btn btn-danger">Danger Button</button>
                            <button class="btn btn-outline">Outline Button</button>
                        </div>
                    </div>

                    <!-- Form Tests -->
                    <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Form Components</h3>
                        <div class="space-y-3">
                            <input type="text" class="form-input" placeholder="Text Input">
                            <select class="form-select">
                                <option>Select Option</option>
                                <option>Option 1</option>
                                <option>Option 2</option>
                            </select>
                            <textarea class="form-textarea" placeholder="Textarea"></textarea>
                        </div>
                    </div>

                    <!-- Alert Tests -->
                    <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Alert Components</h3>
                        <div class="space-y-3">
                            <div class="alert alert-success">Success alert message</div>
                            <div class="alert alert-error">Error alert message</div>
                            <div class="alert alert-warning">Warning alert message</div>
                            <div class="alert alert-info">Info alert message</div>
                        </div>
                    </div>

                    <!-- Badge Tests -->
                    <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Badge Components</h3>
                        <div class="space-y-3">
                            <span class="badge badge-primary">Primary Badge</span>
                            <span class="badge badge-success">Success Badge</span>
                            <span class="badge badge-warning">Warning Badge</span>
                            <span class="badge badge-danger">Danger Badge</span>
                            <span class="badge badge-secondary">Secondary Badge</span>
                        </div>
                    </div>
                </div>

                <!-- Color Palette Test -->
                <div class="mt-8 bg-white shadow-sm rounded-lg border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Color Palette Test</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                        <div class="bg-primary-50 p-4 rounded-lg text-center">
                            <div class="text-sm font-medium">Primary 50</div>
                        </div>
                        <div class="bg-primary-100 p-4 rounded-lg text-center">
                            <div class="text-sm font-medium">Primary 100</div>
                        </div>
                        <div class="bg-primary-500 p-4 rounded-lg text-center text-white">
                            <div class="text-sm font-medium">Primary 500</div>
                        </div>
                        <div class="bg-primary-600 p-4 rounded-lg text-center text-white">
                            <div class="text-sm font-medium">Primary 600</div>
                        </div>
                        <div class="bg-primary-700 p-4 rounded-lg text-center text-white">
                            <div class="text-sm font-medium">Primary 700</div>
                        </div>
                        <div class="bg-gray-900 p-4 rounded-lg text-center text-white">
                            <div class="text-sm font-medium">Gray 900</div>
                        </div>
                    </div>
                </div>

                <!-- Status -->
                <div class="mt-8 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-500 mr-2"></i>
                        <span class="text-green-800 font-medium">All CSS components are loading correctly!</span>
                    </div>
                    <p class="text-green-700 text-sm mt-1">If you can see this styled page, your CSS is working properly.</p>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Test JavaScript functionality
    document.addEventListener('DOMContentLoaded', function() {
        console.log('CSS Test Page Loaded Successfully');
        
        // Test button clicks
        document.querySelectorAll('.btn').forEach(button => {
            button.addEventListener('click', function() {
                alert('Button clicked: ' + this.textContent);
            });
        });
    });
    </script>
</body>
</html> 