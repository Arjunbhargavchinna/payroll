<?php
/**
 * Alternative Layout for PayrollPro
 * Uses local CSS instead of Tailwind CDN
 */

// Load config
require_once 'config/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PayrollPro - Alternative Layout</title>
    
    <!-- Alternative CSS instead of Tailwind CDN -->
    <link href="<?php echo BASE_URL; ?>/css/tailwind-alternative.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="<?php echo BASE_URL; ?>/css/app.css" rel="stylesheet">
    
    <style>
    /* Additional fallback styles */
    .fallback-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 1rem;
    }
    
    .fallback-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1rem 0;
        margin-bottom: 2rem;
    }
    
    .fallback-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .fallback-card {
        background: white;
        border-radius: 0.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        padding: 1.5rem;
        border: 1px solid #e5e7eb;
    }
    
    .fallback-button {
        display: inline-block;
        padding: 0.5rem 1rem;
        background-color: #3b82f6;
        color: white;
        text-decoration: none;
        border-radius: 0.375rem;
        font-weight: 500;
        transition: background-color 0.2s;
    }
    
    .fallback-button:hover {
        background-color: #2563eb;
    }
    
    .fallback-button.success {
        background-color: #10b981;
    }
    
    .fallback-button.success:hover {
        background-color: #059669;
    }
    
    .fallback-button.danger {
        background-color: #ef4444;
    }
    
    .fallback-button.danger:hover {
        background-color: #dc2626;
    }
    </style>
</head>
<body class="bg-gray-50">
    <div class="fallback-header">
        <div class="fallback-container">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-calculator text-2xl mr-3"></i>
                    <h1 class="text-2xl font-bold">PayrollPro</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm">Alternative Layout</span>
                    <a href="<?php echo BASE_URL; ?>/index.php" class="fallback-button">Back to Main App</a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="fallback-container">
        <div class="fallback-grid">
            <div class="fallback-card">
                <h2 class="text-xl font-semibold mb-4">Component Test</h2>
                
                <div class="mb-4">
                    <h3 class="text-lg font-medium mb-2">Button Styles</h3>
                    <div class="flex space-x-2">
                        <button class="fallback-button">Primary Button</button>
                        <button class="fallback-button success">Success Button</button>
                        <button class="fallback-button danger">Danger Button</button>
                    </div>
                </div>
                
                <div class="mb-4">
                    <h3 class="text-lg font-medium mb-2">Font Awesome Icons</h3>
                    <div class="flex space-x-4 text-2xl">
                        <i class="fas fa-home text-blue-500"></i>
                        <i class="fas fa-user text-green-500"></i>
                        <i class="fas fa-cog text-gray-500"></i>
                        <i class="fas fa-bell text-yellow-500"></i>
                    </div>
                </div>
                
                <div class="mb-4">
                    <h3 class="text-lg font-medium mb-2">Custom CSS Classes</h3>
                    <button class="btn btn-primary">Custom Button</button>
                    <div class="alert alert-success mt-2">Success alert with custom CSS</div>
                </div>
            </div>
            
            <div class="fallback-card">
                <h2 class="text-xl font-semibold mb-4">Layout Test</h2>
                
                <div class="mb-4">
                    <h3 class="text-lg font-medium mb-2">Grid Layout</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-4 bg-blue-100 rounded">Grid Item 1</div>
                        <div class="p-4 bg-green-100 rounded">Grid Item 2</div>
                        <div class="p-4 bg-yellow-100 rounded">Grid Item 3</div>
                        <div class="p-4 bg-red-100 rounded">Grid Item 4</div>
                    </div>
                </div>
                
                <div class="mb-4">
                    <h3 class="text-lg font-medium mb-2">Status Indicators</h3>
                    <div class="flex space-x-4">
                        <span class="flex items-center">
                            <span class="status-dot status-active mr-2"></span>
                            Active
                        </span>
                        <span class="flex items-center">
                            <span class="status-dot status-inactive mr-2"></span>
                            Inactive
                        </span>
                        <span class="flex items-center">
                            <span class="status-dot status-terminated mr-2"></span>
                            Terminated
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="fallback-card">
                <h2 class="text-xl font-semibold mb-4">JavaScript Test</h2>
                
                <div class="mb-4">
                    <button onclick="testAlternativeJS()" class="fallback-button">
                        Test JavaScript
                    </button>
                    <div id="js-test-result" class="mt-2 text-sm"></div>
                </div>
                
                <div class="mb-4">
                    <h3 class="text-lg font-medium mb-2">Loading Spinner</h3>
                    <div class="loading-spinner"></div>
                    <span class="ml-2">Loading...</span>
                </div>
            </div>
        </div>
        
        <div class="fallback-card">
            <h2 class="text-xl font-semibold mb-4">Implementation Instructions</h2>
            
            <div class="mb-4">
                <h3 class="text-lg font-medium mb-2">To use this alternative CSS:</h3>
                <ol class="list-decimal list-inside space-y-2">
                    <li>Replace the Tailwind CDN link in your layout files</li>
                    <li>Use the alternative CSS file: <code>css/tailwind-alternative.css</code></li>
                    <li>Keep your existing custom CSS: <code>css/app.css</code></li>
                    <li>Test all components to ensure they work properly</li>
                </ol>
            </div>
            
            <div class="mb-4">
                <h3 class="text-lg font-medium mb-2">Files to update:</h3>
                <ul class="list-disc list-inside space-y-1">
                    <li><code>app/views/layout/main.php</code> - Replace Tailwind CDN</li>
                    <li><code>app/views/layout/sidebar.php</code> - Update if needed</li>
                    <li>Any other layout files using Tailwind classes</li>
                </ul>
            </div>
        </div>
    </div>
    
    <!-- JavaScript -->
    <script src="<?php echo BASE_URL; ?>/js/app.js"></script>
    <script>
    function testAlternativeJS() {
        document.getElementById("js-test-result").innerHTML = 
            "<span style=\"color: #059669;\">✓ JavaScript is working with alternative CSS!</span>";
    }
    
    // Test if PayrollApp is loaded
    document.addEventListener("DOMContentLoaded", function() {
        if (typeof PayrollApp !== "undefined") {
            console.log("✓ PayrollApp loaded successfully with alternative CSS");
        } else {
            console.log("✗ PayrollApp not loaded");
        }
    });
    </script>
</body>
</html> 