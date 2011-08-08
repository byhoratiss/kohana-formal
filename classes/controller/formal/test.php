<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Formal_Test extends Controller {
    function action_index() {
        $key = 'example';
        $post_data = array(
            //'field1' => ''
        );
        
        $formal = Formal_Validation::instance()->register($key, $post_data);
        $formal->validate();
        
        echo '<pre>';
        print_r($formal->report(false));
        echo '</pre>';
    }
}