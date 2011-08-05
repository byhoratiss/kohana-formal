<?php defined('SYSPATH') or die('No direct script access.');

class Formal_Tools {
    static function form_open($target, $form_name, $settings=array()) {
        // convert settings to javascript object
        $formal_settings = '{';
        $first = true;
        foreach($settings as $setting => $value) {
            if($first !== true) {
                $formal_settings .= ',';
            }
            $formal_settings .= $setting.':' . $value;
            $first = false;            
        }
        $formal_settings .= '}';
        
        $js = html::script(Kohana::$config->load('formal.formal_js_url')) ."\n";
        $js .= '<script type="text/javascript">jQuery(document).ready(function($) { $("#'. $form_name .'").formal('.$formal_settings .'); });</script>';
        $js .= form::open($target, array('name' => $form_name, 'id' => $form_name));
        $js .= '<input type="hidden" name="form_name" value="'. $form_name .'" />';
        return $js;
    }
}