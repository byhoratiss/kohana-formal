<?php defined('SYSPATH') or die('No direct script access.');
 class Controller_Formal_Home extends Controller {
     function action_index() {
         $this->response->body(View::factory('formal/index'));
     }
 }