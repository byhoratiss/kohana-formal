<?php defined('SYSPATH') or die('No direct script access.');

class Request extends Kohana_Request {
    const _formal = true; // yep, now this is a formal request!
    protected $_validation;
    
    public static function factory($uri = TRUE, HTTP_Cache $cache = NULL, $injected_routes = array()) {
        $config =  Kohana::$config->load('formal'); // we're gonna need them
        $rules = Kohana::$config->load('rules');

        if($_SERVER['REQUEST_METHOD'] !== 'POST' || !array_key_exists('form_name', $_POST)) {
            // This isn't a POST request, Kohana should handle the request as normal
            return parent::factory($uri, $cache, $injected_routes);
        }
        
        if(empty($_POST['formal_key']) || !array_key_exists($_POST['formal_key'], $rules)) {
            // Formal needs at least the key of the form to be able to handle it!
            if($config['strict'] !== false) {
                // Houston we've got a problem
                echo 'This request cannot safely be handled';
                exit;
            } else {
                // Ah, you want some control yourself?
                return parent::factory($uri, $cache, $injected_routes);
            }
        }
        
        // Let's validate the form!
        $this->_validation = Formal_Validation::instance()
                    ->register($_POST['formal_key'], $_POST);
        if($this->_validation->validate() === true) {
            // form has been validated, pass through this request!!
            return parent::factory($uri, $cache, $injected_routes);
        }
        
        // some user input is wrong, let formal handle this!
        $uri = 'formal/validate';
        return parent::factory($uri, $cache, $injected_routes);
    }
}