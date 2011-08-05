<?php defined('SYSPATH') or die('No direct script access.'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml"> 
    <head> 
        <title>Formal - Kohana Form handling module</title>
        <script type="text/javascript" src="/assets/jquery.js"></script> 
    </head>
    
    <body>
        <h1>Examples</h1>
        
        <h2>Basic</h2>
        <div class="formal-messages"></div>
        <?php echo Formal_Tools::form_open(Route::get('formal')->uri(), 'examples'); ?>
            <fieldset>
                Input 1: <input type="text" name="input_1" /> <br />
                Input 2: <input type="text" name="input_1" /> <br />
                Input 3: <input type="text" name="input_1" /> <br />
                Input 4: <input type="text" name="input_1" /> <br />
                Input 5: <input type="text" name="input_1" /> <br />
                Input 6: <input type="text" name="input_1" /> <br />
                
                <button type="submit">Validate and submit!</button>
            </fieldset>
        </form>
    </body>
</html>