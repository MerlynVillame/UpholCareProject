<?php
/**
 * Error Controller
 */

require_once ROOT . DS . 'core' . DS . 'Controller.php';

class ErrorController extends Controller {
    
    /**
     * 404 Not Found
     */
    public function notfound() {
        $data = [
            'title' => '404 - Page Not Found'
        ];
        
        $this->view('error/404', $data);
    }
    
    /**
     * 403 Unauthorized
     */
    public function unauthorized() {
        $data = [
            'title' => '403 - Unauthorized'
        ];
        
        $this->view('error/403', $data);
    }
}

