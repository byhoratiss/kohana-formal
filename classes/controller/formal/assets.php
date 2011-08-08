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
        
        // determine MIME type
        $parts = explode('.', $this->request->param('file'));
        $ext = array_pop($parts);
        $mime_type = File::mime_by_ext($ext);
        
        $this->response->headers('Content-type', $mime_type);
        
        $this->response->body($view);
    }
}