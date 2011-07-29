<?php defined('SYSPATH') or die('No direct script access.');

class Request extends Kohana_Request {
    public static function factory($uri = TRUE, Cache $cache = NULL, $injected_routes = array()) {
        $method = $_SERVER['REQUEST_METHOD'];
        $formal_config =  Kohana::config('rules');

        if($method !== 'POST' || !array_key_exists('form_name', $_POST)) {
            // no post, just handle as normal
            return parent::factory($uri, $cache, $injected_routes);
        } else if(array_key_exists($_POST['form_name'], $formal_config) && array_key_exists('settings', $formal_config[$_POST['form_name']])) {
            // check if some special settings are provided.
            $settings = $formal_config[$_POST['form_name']]['settings'];
            if(array_key_exists('auto_validate', $settings) && $settings['auto_validate'] === false) {
                // should not automatically be validated, pass through to the controller
                return parent::factory($uri, $cache, $injected_routes);
            }
        } else if(array_key_exists('formal_validated', $_POST) && $_POST['formal_validated'] == 'true') {
            // we've been here before. Do one final check before we can send the request through
            
            // validate the form;
            $validation = Formal_Validation::instance();
            $validation->register($_POST['form_name'], $_POST);
            $validated = $validation->validate(true);
            if($validated === true) {
                // form has been validated, let the controller handle the data!
                return parent::factory($uri, $cache, $injected_routes);
            } else {
                // darn! someone is trying to fuck around with us...
                throw new HTTP_Exception_503('Are you trying to hack this?');
            }
        }
        
        // forward to formal, change uri
        $uri = 'formal/validate';
        $request = parent::factory($uri, $cache, $injected_routes);
        // set differed var so we can determine if it was a direct request (which isn't allowed)
        $request->differed = true;
        
        return $request;
    }
}