<?php
/**
 * UphoCare - Entry Point
 * Web-Based Repair and Restoration Management System
 */

// Suppress warnings/notices for JSON endpoints to prevent breaking JSON
if (isset($_GET['ajax']) || 
    !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' ||
    strpos($_SERVER['REQUEST_URI'] ?? '', '/admin/get') !== false ||
    strpos($_SERVER['REQUEST_URI'] ?? '', '/admin/add') !== false ||
    strpos($_SERVER['REQUEST_URI'] ?? '', '/customer/get') !== false) {
    // For AJAX endpoints, suppress HTML errors
    error_reporting(E_ERROR | E_PARSE);
    ini_set('display_errors', 0);
}

session_start();


// Define constants
define('ROOT', dirname(__FILE__));
define('DS', DIRECTORY_SEPARATOR);

// Include composer autoloader if exists
if (file_exists(ROOT . DS . 'vendor' . DS . 'autoload.php')) {
    require_once ROOT . DS . 'vendor' . DS . 'autoload.php';
}

// Autoloader
spl_autoload_register(function ($class) {
    // Try controllers, models, core, helpers directories
    $directories = ['controllers', 'models', 'core', 'helpers'];
    
    foreach ($directories as $dir) {
        $file = ROOT . DS . $dir . DS . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
    
    // Fallback to root directory
    $file = ROOT . DS . str_replace('\\', DS, $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});


// Include configuration
require_once ROOT . DS . 'config' . DS . 'config.php';
require_once ROOT . DS . 'config' . DS . 'database.php';

// Preserve POST method through rewrite
// When using mod_rewrite, POST data should be preserved, but ensure it's available
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_POST) && empty(file_get_contents('php://input'))) {
    // This shouldn't happen, but log it if it does
    error_log("WARNING: POST request detected but no POST data available. REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD']);
}

// Get the URL
$url = isset($_GET['url']) ? $_GET['url'] : '';
$url = rtrim($url, '/');
$url = filter_var($url, FILTER_SANITIZE_URL);
$url = explode('/', $url);

// Determine controller and method
// Handle hyphenated controller names (e.g., control-panel -> ControlPanel)
$controllerBase = !empty($url[0]) ? $url[0] : 'home';
$controllerBase = str_replace('-', ' ', $controllerBase);
$controllerBase = str_replace(' ', '', ucwords(strtolower($controllerBase)));
$controllerName = $controllerBase . 'Controller';

$method = isset($url[1]) && !empty($url[1]) ? $url[1] : 'index';
$params = array_slice($url, 2);

// Load controller (config is already loaded above)
$controllerFile = ROOT . DS . 'controllers' . DS . $controllerName . '.php';

if (file_exists($controllerFile)) {
    require_once $controllerFile;
    $controller = new $controllerName();
    
    if (method_exists($controller, $method)) {
        call_user_func_array([$controller, $method], $params);
    } else {
        // Method not found
        header("Location: " . BASE_URL . "error/notfound");
        exit();
    }
} else {
    // Controller not found
    header("Location: " . BASE_URL . "error/notfound");
    exit();
}
