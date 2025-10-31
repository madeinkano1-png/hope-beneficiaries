<?php
/**
 * HoPE Beneficiaries and Tranche Payment Management System
 * Main Entry Point
 */

// Start session
session_start();

// Error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define application constants
define('APP_ROOT', __DIR__);
define('APP_URL', 'http://localhost');
define('UPLOAD_PATH', APP_ROOT . '/uploads');

// Include autoloader
require_once 'src/autoload.php';

// Include configuration
require_once 'config/app.php';
require_once 'config/database.php';

// Simple routing
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);
$path = trim($path, '/');

// Remove query parameters
$path = explode('?', $path)[0];

// Basic routing logic
switch ($path) {
    case '':
    case 'dashboard':
        require_once 'src/Controllers/DashboardController.php';
        $controller = new DashboardController();
        $controller->index();
        break;
        
    case 'login':
        require_once 'src/Controllers/AuthController.php';
        $controller = new AuthController();
        $controller->login();
        break;
        
    case 'logout':
        require_once 'src/Controllers/AuthController.php';
        $controller = new AuthController();
        $controller->logout();
        break;
        
    case 'beneficiaries':
        require_once 'src/Controllers/BeneficiaryController.php';
        $controller = new BeneficiaryController();
        $controller->index();
        break;
        
    case 'import':
        require_once 'src/Controllers/ImportController.php';
        $controller = new ImportController();
        $controller->index();
        break;
        
    default:
        http_response_code(404);
        echo "Page not found";
        break;
}
?>
