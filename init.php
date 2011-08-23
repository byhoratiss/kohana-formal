<?php defined('SYSPATH') or die('No direct script access.');

/* All routes below is only needed for examples and documentation, you can 
 * remove them for production applications.
 */
Route::set('formal', 'formal(/<controller>(/<action>))')
        ->defaults(array(
            'directory' => 'formal',
            'controller' => 'home',
            'action' => 'index'
        ));

Route::set('formal/media', 'formal/media/<file>', array('file' => '.+'))
        ->defaults(array(
            'directory' => 'formal',
            'controller' => 'media',
            'action' => 'media'
        ));