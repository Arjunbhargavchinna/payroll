<?php
// debug-routes.php

// Include your Router class here if it's not autoloaded
require_once 'Router.php'; // Update this if needed

$router = new Router();

// Register your routes exactly as shared (truncated here for brevity)
$router->addRoute('GET', '/', 'Dashboard', 'index');
$router->addRoute('GET', '/dashboard', 'Dashboard', 'index');
$router->addRoute('GET', '/login', 'Auth', 'login');
$router->addRoute('POST', '/login', 'Auth', 'login');
$router->addRoute('GET', '/logout', 'Auth', 'logout');
// ... Add all routes as in your script

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