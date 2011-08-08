<?php defined('SYSPATH') or die('No direct script access.'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml"> 
    <head> 
        <title>Formal - Kohana Form handling module</title>
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
    </head>
    
    <body>
        <h1>Examples</h1>
        
        <h2>Basic</h2>
        <div class="formal-messages"></div>
        <?php echo Formal_Tools::form_open(Route::get('formal')->uri(), 'examples'); ?>
            <fieldset>
                Input 1: <input type="text" name="input1" /> <br />
                Input 2: <input type="text" name="input2" /> <br />
                Input 3: <input type="text" name="input3" /> <br />
                Input 4: <input type="text" name="input4" /> <br />
                Input 5: <input type="text" name="input5" /> <br />
                Input 6: <input type="text" name="input6" /> <br />
                
                <button type="submit">Validate and submit!</button>
            </fieldset>
        </form>
    </body>
</html>