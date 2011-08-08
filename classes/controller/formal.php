<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Formal extends Controller {
    // is this a differed or direct request?
    private $differed = false;
    
    function before() {
        parent::before();
        
        // check if a request to this controller was made only by Formal itself
        if(!$this->request->_validation->registered()) {
            throw new HTTP_EXCEPTION_404('No form found');
        }
        
        // determine if request is internal, just an extra test...
        if(! $this->request->is_initial()) {
            $this->request->action('internal_'. $this->request->action());
        }
    }
    
    function action_validate() {        
        // do the validation and print out the result
        echo $this->request->_validation->validate();
    }
}