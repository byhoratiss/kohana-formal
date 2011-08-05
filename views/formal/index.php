<?php defined('SYSPATH') or die('No direct script access.'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml"> 
    <head> 
        <title>Formal - Kohana Form handling module</title>
        <script type="text/javascript" src="/assets/jquery.js"></script> 
    </head>
    
    <body>
        <h1>Content</h1>
        <ul>
            <li>
                <a href="<?php echo Route::get('formal')->
                        uri(array('controller' => 'examples')); ?>">
                    Examples
                </a>
            </li>
        </ul>
    </body>
</html>