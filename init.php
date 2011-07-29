<?php defined('SYSPATH') or die('No direct script access.');

Route::set('formal/validate', 'formal/validate')->defaults(array(
    'controller' => 'formal',
    'action' => 'validate'
));