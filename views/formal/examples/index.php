<?php defined('SYSPATH') or die('No direct script access.'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml"> 
    <head> 
        <title>Formal - Kohana Form handling module</title>
        <link rel="stylesheet" type="text/css" href="<?php echo URL::base() . 'formal/assets/css/style.css'; ?>" />
        <link rel="stylesheet" type="text/css" href="<?php echo URL::base() . 'formal/assets/css/formal.css'; ?>" />
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
        <script type="text/javascript" src="<?php echo URL::base() . 'formal/media/js/formal.js'; ?>"></script>
        <script type="text/javascript">
            function trim(value) {
                value = value.replace(/^\s+/,''); 
                value = value.replace(/\s+$/,'');
                return value;
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
            
            <script type="text/javascript">
                jQuery(document).ready(function() {
                    var bscb = function(form) {
                        alert('submitting!');
                        alert(form.attr('id'));
                        return form.serializeArray();
                    }
                    $('#example').formal({
                        'reportMethod': ['field', 'pane'],
                        //'beforeSubmitCallback': bscb,
                        'afterSubmit': 'report'
                    });
                });
            </script>
            <form action="" method="post" id="example">
                <fieldset>
                    <div class="formal-messages"></div>
                    
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