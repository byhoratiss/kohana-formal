<?php defined('SYSPATH') or die('No direct script access.');

/**
 * You do not need this controller on a production server
 */
class Controller_Formal_Assets extends Controller {
    function action_show() {
        if(strpos($this->request->param('file'), '..') !== false || strpos($this->request->param('file'), '/') === 0) {
            throw new HTTP_Exception_500();
            exit;
        }
        
        $view = View::factory('assets/'. $this->request->param('file'));
        if(!$view) {
            throw new HTTP_Exception_404();
            exit;
        }
        
        $this->response->body($view);
    }
}