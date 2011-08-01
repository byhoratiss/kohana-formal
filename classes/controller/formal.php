<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Formal extends Controller {
    // is this a differed or direct request?
    private $differed = false;
    
    protected function before() {
        parent::before();
        
        // check if a request to this controller was made only by Formal itself
        if(!isset($this->request->differed) || $this->request->differed !== true) {
            throw new HTTP_EXCEPTION_404();
        }
        
        // determine if request is internal, just an extra test...
        if(! $this->request->is_initial()) {
            $this->request->action('internal_'. $this->request->action());
        }
    }
    
    protected function internal_validate() {
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