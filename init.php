<?php defined('SYSPATH') or die('No direct script access.');

Route::set('formal/validate', 'formal/validate')->defaults(array(
    'controller' => 'formal',
    'action' => 'validate'
));

/* All routes below is only needed for examples and documentation, you can 
 * remove them for production applications.
 */
Route::set('formal', 'formal(/<controller>(/<action>))')
        ->defaults(array(
            'directory' => 'formal',
            'controller' => 'home',
            'action' => 'index'
        ));