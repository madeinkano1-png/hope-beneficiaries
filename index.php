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

// Enhanced routing logic
$pathParts = explode('/', $path);
$controller = $pathParts[0] ?? '';
$action = $pathParts[1] ?? 'index';

switch ($controller) {
    case '':
    case 'dashboard':
        require_once 'src/Controllers/DashboardController.php';
        $controllerObj = new DashboardController();
        if ($action === 'analytics') {
            $controllerObj->analytics();
        } else {
            $controllerObj->index();
        }
        break;
        
    case 'login':
        require_once 'src/Controllers/AuthController.php';
        $controllerObj = new AuthController();
        $controllerObj->login();
        break;
        
    case 'logout':
        require_once 'src/Controllers/AuthController.php';
        $controllerObj = new AuthController();
        $controllerObj->logout();
        break;
        
    case 'register':
        require_once 'src/Controllers/AuthController.php';
        $controllerObj = new AuthController();
        $controllerObj->register();
        break;
        
    case 'reset-password':
        require_once 'src/Controllers/AuthController.php';
        $controllerObj = new AuthController();
        $controllerObj->resetPassword();
        break;
        
    case 'beneficiaries':
        require_once 'src/Controllers/BeneficiaryController.php';
        $controllerObj = new BeneficiaryController();
        switch ($action) {
            case 'create':
                $controllerObj->create();
                break;
            case 'edit':
                $controllerObj->edit();
                break;
            case 'view':
                $controllerObj->view();
                break;
            case 'delete':
                $controllerObj->delete();
                break;
            case 'export':
                $controllerObj->export();
                break;
            default:
                $controllerObj->index();
                break;
        }
        break;
        
    case 'import':
        require_once 'src/Controllers/ImportController.php';
        $controllerObj = new ImportController();
        switch ($action) {
            case 'upload':
                $controllerObj->upload();
                break;
            case 'preview':
                $controllerObj->preview();
                break;
            case 'execute':
                $controllerObj->execute();
                break;
            case 'results':
                $controllerObj->results();
                break;
            case 'errors':
                $controllerObj->errors();
                break;
            case 'template':
                $controllerObj->downloadTemplate();
                break;
            case 'status':
                $controllerObj->status();
                break;
            case 'cancel':
                $controllerObj->cancel();
                break;
            default:
                $controllerObj->index();
                break;
        }
        break;
        
    default:
        http_response_code(404);
        echo "Page not found";
        break;
}
?>
