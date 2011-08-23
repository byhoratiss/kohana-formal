<?php defined('SYSPATH') or die('No direct script access.'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml"> 
    <head> 
        <title>Formal - Kohana Form handling module</title>
        <link rel="stylesheet" type="text/css" href="<?php echo URL::base() . Route::get('formal/media')->uri(array('file' => 'css/style.css')); ?>" />
        <link rel="stylesheet" type="text/css" href="<?php echo URL::base() . Route::get('formal/media')->uri(array('file' => 'css/formal.css')); ?>" />
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
        <script type="text/javascript">
            function trim(value) {
                value = value.replace(/^\s+/,''); 
                value = value.replace(/\s+$/,'');
                return value;
            }
            
            function customCallback(result) {
                alert('The server has got your data and want\'s to say hi!');
                alert(result.data.message);
            }
            
            jQuery(document).ready(function() {
                $('input[type=text]').each(function() {
                    $(this).val($(this).attr('rel'));
                });
                
                $('input[type=text]').focus(function(e) {
                    if($(this).val() == $(this).attr('rel')) {
                        $(this).val('');
                    }
                });
                
                $('input[type=text]').blur(function(e) {
                    if(trim($(this).val()) == '') {
                        $(this).val($(this).attr('rel'));
                    }
                });
            });
        </script>
    </head>
    
    <body>
        <div class="container">
            <h1>Formal</h1>
            <p>
                Refer to <a href="https://github.com/wgiog/kohana-formal/">GitHub</a>
                more info.
            </p>
        
            <h1>Examples</h1>
            
            <?php $validation = Formal_Validation::instance(); ?>
            <?php if($validation->validate() == true): ?>
            <p>You have succesfully submitted the form with key [<?php echo $validation->key(); ?>]</p>
            <?php endif; ?>
            
            <h2>Basic</h2>
            <?php echo Formal_Tools::form_open(Route::get('formal')->uri(array(
                    'controller' => 'examples', 
                    'action' => 'process')),
                'basic_report'); ?>
            
                <fieldset>
                    <div class="formal-messages paneBasicReport"></div>
                    
                    <div class="row">
                        <input type="text" name="input_field" rel="Just a string" class="required" />
                    </div>
                    <div class="row">
                        <input type="text" name="email" rel="E-mail address" class="required" />
                    </div>
            
                    <button type="submit" class="button"><span>Validate and submit!</span></button>
                    <div class="clear"></div>
                </fieldset>
            </form>
            
            <h2>Basic with a custom javascript callback</h2>
            <?php echo Formal_Tools::form_open(Route::get('formal')->uri(array(
                    'controller' => 'examples', 
                    'action' => 'process')),
                'basic_callback'); ?>
            
                <fieldset>
                    <div class="formal-messages paneBasicCallback"></div>
                    
                    <div class="row">
                        <input type="text" name="input_field" rel="Just a string" class="required" />
                    </div>
                    <div class="row">
                        <input type="text" name="email" rel="E-mail address" class="required" />
                    </div>
            
                    <button type="submit" class="button"><span>Validate and submit!</span></button>
                    <div class="clear"></div>
                </fieldset>
            </form>
            
            
            <h2>Full</h2>
            <?php echo Formal_Tools::form_open(Route::get('formal')->uri(array('controller' => 'examples', 'action' => 'process')), 'full'); ?>
                <fieldset>
                    <div class="formal-messages paneFull"></div>
                    
                    <div class="row">
                        <input type="text" name="input1" rel="Just a string" class="required" />
                    </div>
                    <div class="row">
                        <input type="text" name="input2" rel="E-mail address" class="required" />
                    </div>
                    <div class="row">
                        <input type="text" name="input3" rel="Between 3 and 8" class="required" />
                    </div>
                    <div class="row">
                        <input type="text" name="input4" rel="A number" class="required" />
                    </div>
                    <div class="row">
                        <input type="text" name="input5" rel="Choose between 'one', 'two' and 'three'" class="required" />
                    </div>
                    <div class="row">
                        <input type="text" name="input6" rel="A valid date (string)" class="required" />
                    </div>

                    <button type="submit" class="button"><span>Validate and submit!</span></button>
                    <div class="clear"></div>
                </fieldset>
            </form>            
        </div> <!-- eo. container -->
    </body>
</html>