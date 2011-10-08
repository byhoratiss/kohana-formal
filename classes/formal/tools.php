<?php defined('SYSPATH') or die('No direct script access.');

class Formal_Tools {
    private static $js_included = false;
    
    static function form_open($target, $formal_key, $settings=array()) {
        $form_settings = Kohana::$config->load('formal/rules.'. $formal_key .'.settings');
        $form_settings = array_merge( (array)$form_settings, $settings );
        
        // convert settings to javascript object
        $form_settings = self::to_javascript_object($form_settings);
        
        $js = '';
        
        if(self::$js_included !== true) {
            // insert Formal client side engine (javascript)
            $js .= html::script(Kohana::$config->load('formal.formal_js_url')) ."\n";
            self::$js_included = true;
        }
        
        $js .= '<script type="text/javascript">jQuery(document).ready(function($) { $("#'. $formal_key .'").formal('.$form_settings .'); });</script>';
        $js .= form::open($target, array('name' => $formal_key, 'id' => $formal_key));
        $js .= '<input type="hidden" name="formal_key" value="'. $formal_key .'" />';
        return $js;
    }
    
    static function to_javascript_object(array $arr) {
        $obj = '{';
        
        $first = true;
        foreach($arr as $key => $value) {
            if($first !== true) {
                $obj .= ',';
            }
            
            if(is_array($value)) {
                $arrf = true;
                $ret_val = '[';
                foreach($value as $val) {
                    if($arrf !== true) $ret_val .= ',';
                    
                    $ret_val .= '\''. $val .'\'';
                    $arrf = false;
                }
                
                $value = $ret_val .']';
            } else if(strpos ($value, '&') === 0) {
                $value = substr($value, 1, strlen($value)-1); // a javascript callback, do not enclose
            } else {
                $value = "'". addslashes($value) ."'";
            }
            
            
            $obj .= $key.':' . $value;
            
            $first = false;            
        }
        $obj .= '}';
        
        return $obj;
    }
}