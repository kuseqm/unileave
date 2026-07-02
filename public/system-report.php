<?php
// Place this file at: public/system-report.php
// Then visit: http://127.0.0.1:8000/system-report.php

// Enable error reporting for this script only
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set path to Laravel root (one level up from public)
$laravelRoot = __DIR__ . '/../';

// Load Laravel's autoloader and bootstrap the application
require_once $laravelRoot . 'vendor/autoload.php';
$app = require_once $laravelRoot . 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

// Start collecting report
$report = [];

// 1. System Information
$report['system'] = [
    'laravel_version' => app()->version(),
    'php_version' => phpversion(),
    'environment' => app()->environment(),
    'debug_mode' => config('app.debug'),
    'timezone' => config('app.timezone'),
];

// 2. All Routes (with their real status)
$report['routes'] = [];
foreach (Route::getRoutes() as $route) {
    $routeName = $route->getName();
    if ($routeName && strpos($routeName, 'admin.') === 0) {
        $action = $route->getActionName();
        $controllerClass = null;
        $controllerExists = false;
        $methodExists = false;
        
        if (strpos($action, '@') !== false) {
            list($controllerClass, $method) = explode('@', $action);
            $controllerExists = class_exists($controllerClass);
            if ($controllerExists) {
                $methodExists = method_exists($controllerClass, $method);
            }
        }
        
        $report['routes'][$routeName] = [
            'uri' => $route->uri(),
            'methods' => $route->methods(),
            'controller' => $controllerClass,
            'method' => $method ?? null,
            'controller_exists' => $controllerExists,
            'method_exists' => $methodExists ?? false,
            'view_exists' => null,
        ];
    }
}

// 3. Check Controllers in Admin folder
$adminControllersPath = $laravelRoot . 'app/Http/Controllers/Admin';
$report['controllers'] = [];
if (is_dir($adminControllersPath)) {
    foreach (scandir($adminControllersPath) as $file) {
        if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
            $controllerName = 'App\\Http\\Controllers\\Admin\\' . pathinfo($file, PATHINFO_FILENAME);
            $report['controllers'][$file] = [
                'class_exists' => class_exists($controllerName),
                'methods' => class_exists($controllerName) ? get_class_methods($controllerName) : [],
            ];
        }
    }
} else {
    $report['controllers']['error'] = 'Admin controllers folder not found';
}

// 4. Check Views in admin folder
$adminViewsPath = $laravelRoot . 'resources/views/admin';
$report['views'] = [];
if (is_dir($adminViewsPath)) {
    $report['views']['files'] = [];
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($adminViewsPath));
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $relativePath = str_replace($adminViewsPath . '/', '', $file->getPathname());
            $report['views']['files'][] = $relativePath;
        }
    }
} else {
    $report['views']['error'] = 'Admin views folder not found';
}

// 5. Check if specific critical controllers exist
$criticalControllers = [
    'LeaveRequestController',
    'LeaveTypeController', 
    'StaffController',
    'DepartmentController',
    'SchoolController',
    'CampusController',
    'UniversityController',
    'ApprovalController',
    'DashboardController',
    'RoleController',
    'ImportController',
];

$report['critical_controllers'] = [];
foreach ($criticalControllers as $controller) {
    $fullClass = 'App\\Http\\Controllers\\Admin\\' . $controller;
    $report['critical_controllers'][$controller] = [
        'exists' => class_exists($fullClass),
        'file_exists' => file_exists($adminControllersPath . '/' . $controller . '.php'),
        'path' => $adminControllersPath . '/' . $controller . '.php',
    ];
}

// 6. Database tables
try {
    $tables = DB::select('SHOW TABLES');
    $report['database_tables'] = array_map(function($table) {
        return reset($table);
    }, $tables);
} catch (Exception $e) {
    $report['database_tables'] = ['error' => $e->getMessage()];
}

// 7. Summary of issues
$report['summary'] = [
    'total_admin_routes' => count($report['routes']),
    'broken_controller_routes' => 0,
    'missing_controllers' => [],
    'missing_views' => [],
];

foreach ($report['routes'] as $name => $route) {
    if (!$route['controller_exists']) {
        $report['summary']['broken_controller_routes']++;
        $report['summary']['missing_controllers'][] = $name . ' → ' . $route['controller'];
    }
}

// Output as formatted JSON
header('Content-Type: application/json');
echo json_encode($report, JSON_PRETTY_PRINT);