<?php defined('SYSPATH') or die('No direct script access.');

Route::set('formal/validate', 'formal/validate')->defaults(array(
    'controller' => 'formal',
    'action' => 'validate'
));

Route::set('formal/examples', 'formal/examples(/<action>)')
        ->defaults(array(
            'directory' => 'formal',
            'controller' => 'examples',
            'action' => 'index'
        ));