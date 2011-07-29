<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Formal extends Controller {
    // is this a differed or direct request?
    private $differed = false;
    
    public function before() {
        parent::before();
        
        // determine if request is direct or not
        if(isset($this->request->differed) && $this->request->differed === true) {
            $this->differed = true;
        }
        
        // determine if request is internal
        if(! $this->request->is_initial()) {
            $this->request->action('internal_'. $this->request->action());
        }
        
        // load config, validation object etc.
    }
    
    public function action_validate() {
        if(!$this->differed) {
            throw new HTTP_Exception_404('');
            exit;
        }
        
        // do the validation and print out the result
        $validation = Formal_Validation::instance();
        
        $validation->register($this->request->post('form_name'), $_POST);
        echo $validation->validate();
    }
}