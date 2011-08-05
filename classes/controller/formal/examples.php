<?php defined('SYSPATH') or die('No direct script access.');
 class Controller_Formal_Examples extends Controller {
     function action_index() {
         $this->response->body(View::factory('formal/examples/index'));
     }
 }