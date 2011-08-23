<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Formal_Examples extends Controller {
    //function action_index() {
    //    $this->response->body(View::factory('formal/examples/index'));
    //}
    
    /**
     * Send you form posts to this controller to process it's data (login an
     * user, change an address in the database, or whatever...
     */
    function action_process() {
        if(!$this->request->_formal->validate()) {
            throw new HTTP_Exception_500;
            return;
        }
        
        if(method_exists($this, 'action_key_'. $this->request->_formal->key())) {
            return call_user_func_array(array($this, 'action_key_'.$this->request->_formal->key()), array());
        }
        
        throw new HTTP_Exception_500('Could not find a processor for this form');
    }
    
    
    function action_key_basic() {
        // say hi to your friendly visitor!
        echo 'Hi, thanks for posting the form. <br />';
        
        // let's find out if we have a validation object in the current request 
        if($this->request->_formal->validate()) {
            echo 'You did everything as requested, I\'m quite satisfied! :)';
        } else {
            echo 'Dude, you did not fill in proper data!';
        }
        
        echo '<br /><br /><a href="'. URL::base() . Route::get('formal')->uri() .'">Get back...</a>';
    }
    
    function action_key_basic_callback() {
        $this->request->_formal->data('message', "\n\nHi thanks for posting! I"
            . "now have your e-mail address: [" . $this->request->post('email') 
            ."]\nYours, the server"
        );
    }
    
    function action_key_basic_report() {
        // right now i'm doing something, like saving something in the database
        
        // you can set as many messages as you'd like (which implies even
        // override the current state!
        $this->request->_formal->set_message(0, 'Hi thanks for posting and all');
    }
}