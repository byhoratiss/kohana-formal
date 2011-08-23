<?php defined('SYSPATH') or die('No direct script access.');

class Request extends Kohana_Request {
    var $_formal;
    
    public static function factory($uri = TRUE, HTTP_Cache $cache = NULL, $injected_routes = array()) {
        if($_SERVER['REQUEST_METHOD'] !== 'POST') {
            // This isn't a POST request, Kohana should handle the request as normal
            return parent::factory($uri, $cache, $injected_routes);
        }
        
        $config =  Kohana::$config->load('formal');
        $rules = Kohana::$config->load('formal/rules');
        
        $key = Arr::get($_POST, 'formal_key');
        
        // create the validation instance and attach it to the request
        $_formal = Formal_Validation::instance();
        
        
        if(!$_formal->registered()) {
            // something wrong...
            if($config['strict'] === false) {
                // Ah, you want some control yourself?
                return parent::factory($uri = TRUE, $cache = NULL, $injected_routes = array());
            } else {
                echo $_formal->report();
                exit;
            }
        }
        
        // fetch the settings
        $rules = $rules->get($key);
        
        // we've got a key, load the settings!
        $form_settings = isset($rules['settings'])?$rules['settings']:array();
        
        // Let's validate the form!
        if($_formal->validate() === true) {
            // create the request (but not execute it yet!
            $request = parent::factory($uri, $cache, $injected_routes);
            $request->_formal = $_formal;
            
            if($request->is_ajax()) {
                if($form_settings['afterSubmit'] == 'callback' ||
                        $form_settings['afterSubmit'] == 'report') {
                    return $request;
                } else {
                    echo $_formal->report();
                    exit;
                }
            } else {
                // form has been validated, or the client wants to handle the
                // response
                // pass through this request!
                return $request;
            }
        }
        
        // not validated, report and exit
        echo $_formal->report();
        exit;
    }
    
    public function execute() {
        $response = parent::execute();
        
        if(isset($this->_formal)) {
            // hey! we've got a formal object! Let's see if we should do
            // something with it!
            
            if($this->_formal->validate() === true) {
                $form_settings = Kohana::$config->load('formal/rules.'. $this->_formal->key() .'.settings');
                if(isset($form_settings['afterSubmit'])) {
                    if($form_settings['afterSubmit'] == 'callback' ||
                            $form_settings['afterSubmit'] == 'report') {
                        $response->body($this->_formal->report());
                    }
                }
            }
        }
        
        return $response;
    }
}