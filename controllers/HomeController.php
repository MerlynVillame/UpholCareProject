<?php
/**
 * Home Controller
 * Handles the landing page
 */

require_once ROOT . DS . 'core' . DS . 'Controller.php';
require_once ROOT . DS . 'config' . DS . 'config.php';

class HomeController extends Controller {
    
    /**
     * Display landing page
     */
    public function index() {
        // Define constants if not already defined
        if (!defined('APP_NAME')) {
            define('APP_NAME', 'UphoCare');
        }
        if (!defined('APP_DESC')) {
            define('APP_DESC', 'Repair and Restoration Management System');
        }
        if (!defined('BASE_URL')) {
            define('BASE_URL', 'http://localhost/UphoCare/');
        }
        
        $data = [
            'title' => 'Welcome - ' . APP_NAME,
            'description' => APP_DESC
        ];
        
        $this->view('landing', $data);
    }
}
