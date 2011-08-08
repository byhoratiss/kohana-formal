<?php defined('SYSPATH') or die('No direct script access.');

class Formal_Validation {
    // singleton instance
    private static $instance;
    
    /**
     * @var boolean Has the form been registered?
     */
    private $registered = false;
    
    /**
     * @var string Name of the form which we want to validate
     */
    private $form;
    
    /**
     * @var Array All data in one place; field values, rules, and labels
     */
    private $data;
    
    /**
     * @var array messages to parse for validation errors
     */
    private $messages;
    
    /**
     * @var Array list of array messages
     */
    private $errors = array();
    
    /**
     *
     * @var type 
     */
    private $user_messages = array();
    
    /**
     * @var Array generic settings for handling the form
     */
    private $settings = array();
    
    /**
     * List of custom data. Will be set and returned when reporting.
     */
    private $custom_data = array();
    
    private function __construct() {
        // load configuration
        $message_conf = Kohana::$config->load('messages');
        // load messages
        $messages = $message_conf['messages'];
        if(!is_array($messages) || empty($messages)) {
            log_message('DEBUG', '[Formal] No error messages found');
        } else {
            $this->messages = $messages;
            $this->messages['fields'] = array();
        }
    }
    
    public static function instance() {
        if(!isset(self::$instance)) {
            $c = __CLASS__;
            self::$instance = new $c;
        }
        
        return self::$instance;
    }
    
    /**
     * Load validation configuration and parse form data for the specific form.
     * 
     * @param string $form name of the form
     * @param array $post_data posted form data
     */
    public function register($form, $post_data) {
        // reset, might have been registered before.
        $this->registered = false;
        
        $this->form = ($form!==null)?$form:'noformfound';
        
        $rules_config = Kohana::$config->load('rules');
        if(!isset($rules_config[$this->form])) {
            $this->set_message('No config for form ['. $form .'] found!');
            return $this->report('error');
            // TODO handle error gracefully
        }
        
        if(array_key_exists('settings', $rules_config[$this->form])) {
            $this->settings = $rules_config[$this->form]['settings'];
        }
        
        // merge post-data into the configuration. We do not care about
        // post-items which are not defined in the config, ignore them.
        foreach($rules_config[$this->form] as $field => $config) {
            if($field == 'settings') {
                continue;
            }
            // set value
            if(!array_key_exists($field, $post_data) || empty($post_data[$field])) {
                // TODO add log
                $this->data[$field]['value'] = '';
            } else {
                if(!is_array($post_data[$field])) {
                    $this->data[$field]['value'] = trim($post_data[$field]);
                } else {
                    $this->data[$field]['value'] = $post_data[$field];
                }
            }
            
            // set label
            if(!array_key_exists('label', $config) || empty($config['label'])) {
                $this->data[$field]['label'] = $field;
            } else {
                $this->data[$field]['label'] = $config['label'];
            }
            
            // set specific messages
            if(array_key_exists('messages', $config)) {
                $this->data[$field]['messages'] = $config['messages'];
            }
            
            // set rules
            if(!array_key_exists('rules', $config)
                    || !is_array($config['rules']) || empty($config['rules'])) {
                // TODO add log
                echo 'no rules found for field '. $field;
                return false;
            }
            $this->data[$field]['rules'] = $config['rules'];
        }
        
        // loaded configuration and merged with post_data.
        $this->registered = true;

        return $this;
    }
    
    function registered() { 
        return ($this->registered === true);
    }
    
    /**
     * Main interface; validate the form. Field-validation is being done in
     * @method validate_field()
     *
     * @param boolean $return_boolean should a boolean or extended data be returned?
     * @return boolean|JSON true/false or extended JSON data
     */
    public function validate($return_boolean=false) {
        if($this->registered !== true) {
            $this->set_message('Form has not been registered...');
            return $this->report('error');
            // TODO handle gracefully
        }
        
        if(!is_array($this->data) || empty($this->data)) {
            // no data found, probably a registered form which should not be checked for some obscure reason...
            if($return_boolean === true) {
                return (count($this->errors) == 0);
            } else {
                return $this->report();
            }
        }
        // loop through the rules and call each check for the specific form field
        foreach($this->data as $field => $cfg) {
            $this->validate_field($field, $cfg['value'], $cfg['rules'], $cfg['label']);
        }

        if($return_boolean === true) {
            return (count($this->errors) == 0);
        } else {
            return $this->report();
        }
    }
    
    /**
     * Validate a field value against the rules given in the @param $rules
     * array(). Each rule name is equivalent to a method in this class, preceded
     * by a underscore (rule 'required' will be fullfiled by method
     * '_required').
     *
     * @param string $field
     * @param mixed $value
     * @param array $rules
     * @param string $label
     */
    private function validate_field($field, $value, array $rules, $label) {
        foreach($rules as $rule => $params) {
            if(method_exists($this, '_'.$rule)) {
                $passed = call_user_func_array(
                    array($this, '_'.$rule),
                    array($value, $params)
                );
                if(!$passed) {
                    $this->set_error($field, $label, $rule, $params);
                }
            } else {
                echo 'ERROR [Formal] No check found for rule ['.$rule.']';
                // TODO handle gracefully
                $this->set_error($field, $label, $rule, array());
                return false;
            }
        }
    }
    
    /**
     * If an error has been found, parse the error
     *
     * @param string $field the name attribute of the form field
     * @param string $label the user readable label
     * @param string $rule the name of the rule
     * @param mixed|array $params array of parameters passed to the rule
     */
    private function set_error($field, $label, $rule, $params) {
        if(!array_key_exists($rule, $this->messages)) {
            // TODO log_message('ERROR', '[Formal] No error message found for rule ['.$rule.']');
            $mssg = 'An unknown error occured for field ['.$label.'] while parsing rule ['.$rule.']';
        } else {
            if(array_key_exists('messages', $this->data[$field]) && array_key_exists($rule, $this->data[$field]['messages'])) {
                $mssg = sprintf($this->data[$field]['messages'][$rule], $label, $params);
            } else {
                $mssg = sprintf($this->messages[$rule], $label, $params);
            }
        }
        $this->errors[$field]['label'] = $label;
        $this->errors[$field]['messages'][] = $mssg;
    }
    
    /**
     * Save a message which will be sent back to the user after processing
     *
     * @param string $message
     */
    function set_message($message) {
        $this->user_messages[] = $message;
    }
    
    function report($override_status=null) {
        if($override_status == 'ok' || $override_status == 'error') {
            $report['status'] = $override_status;
        } else {
            $report['status'] = (count($this->errors) == 0)?'ok':'error';
        }
        $report['errors'] = $this->errors;
        $report['messages'] = $this->user_messages;
        $report['custom_data'] = $this->custom_data;
        return json_encode($report);
    }

    /**
     * If you want custom data to be passed back to the page, add it through
     * here
     *
     * @param string $key
     * @param string $value
     */
    function custom_data($key, $value) {
        $this->custom_data[$key] = $value;
    }
    
    /************************ VALIDATION RULE METHODS *************************/
    /*********************** (you may add your own...) ************************/
    
    function _required($val) {
        return (trim($val) != '');
    }
    
    function _numeric($val) {
        return is_numeric($val);
    }

    function _max($val, $length) {
        if($this->_numeric($val)) {
            return ($val <= $length);
        }
        return (strlen($val) <= $length);
    }

    function _min($val, $length) {
        if($this->_numeric($val)) {
            return ($val >= $length);
        }
        return (strlen($val) >= $length);
    }

    function _set($val, $set) {
        if(is_array($val)) {
            foreach($val as $value) {
                if(array_search($value, $set) === false)
                        return false;
            }
            return true;
        } else {
            return (array_search($val, $set) !== false);
        }
    }
    
    function _regex($val, $pattern) {
        return (! preg_match('~'. $pattern .'~', $val))?false:true;
    }

    function _email($val) {
        return ( ! preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $val)) ? false : true;
    }

    function _domain($val) {
        $count = preg_match('/^([a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,6}$/', $val);
        return ($count == 1);
    }

    function _dns_a($val) {
        $count = preg_match('/\b(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\b/', $val);
        return ($count == 1);
    }

    function _dns_mx($val) {
        $count = preg_match('/^[a-z0-9-.]{2,58}\.[a-z]{2,}$/', $val);
        return ($count == 1);
    }

    function _dns_cname($val) {
        $count = preg_match('/^[a-z0-9-.]{2,58}\.[a-z\.]{2,}$/', $val);
        return ($count == 1);
    }

    function _date($val) {
        return (date_create($val) !== false);
    } 
}