<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Main validation interface
 *
 * @package    Kohana/Formal
 * @author     WGioG
 * @copyright  (c) 2011 WGioG
 * @license    http://www.gnu.org/licenses/gpl.txt
 */
class Formal_Validation {
    const FORMAL_STATE_OK = 0,
            FORMAL_STATE_NOT_INITIALIZED = 1,
            FORMAL_STATE_REGISTERED = 2,
            FORMAL_STATE_NOTICE = 3,
            FORMAL_STATE_WARNING = 4,
            FORMAL_STATE_ERROR = 5;
    
    // singleton instance
    private static $instance;
    
    /**
     * @var string Unique key of the currently loaded form
     */
    private $_key = null;
    
    /**
     * @var array array of form fields (only registered fields are added)
     */
    private $_field_data = array();
    
    /**
     * @var array form specific settings
     */
    private $_settings = array();
    
    /**
     * @var array error message template by rule
     */
    private $_tpl_error_messages = array();
    
    /**
     * @var array messages sent back to the frontend controller
     */
    private $_messages = array();
    
    /**
     * @var array custom data that will be sent back to the frontend controller
     */
    private $_custom_data = array();
    
    /**
     * @var boolean Has the form been registered?
     */
    private $_registered = false;
    
    /**
     * @var boolean Has the form been validated?
     */
    private $_validated = false;
    
    /**
     * @var int current error level
     */
    private $_status = null;
    
    private function __construct() {}
    
    public static function instance() {
        if(!isset(self::$instance)) {
            $c = __CLASS__;
            self::$instance = new $c;
            self::$instance->register();
        }
        
        return self::$instance;
    }
    
    /**
     * Load validation configuration and parse form data for the specific form.
     */
    private function register() {
        // reset, might have been registered before.
        $this->_registered = false;
        $this->_validated = false;
        
        // try to discover the key
        if(isset($_POST['formal_key']) && !empty($_POST['formal_key'])) {
            $this->_key = $_POST['formal_key'];
        } else {
            $this->set_message(self::FORMAL_STATE_ERROR, 'Key could not be discovered');
            return $this;
        }

        if(empty($_POST)) {
            $this->set_message(self::FORMAL_STATE_ERROR, 'No POST data found');
            return $this;
        }
        $post_data = $_POST;
        
        // try to fetch form configuration
        $form_configuration = Kohana::$config->load('formal/rules.'. $this->_key);
        if(empty($form_configuration)) {
            $this->set_message(self::FORMAL_STATE_ERROR, 'Configuration for form ['. $this->_key .'] could not be found');
            return $this;
        }
        
        // try to fetch error message templates
        $message_templates = Kohana::$config->load('formal/messages');
        if(empty($message_templates)) {
            $this->set_message(self::FORMAL_STATE_WARNING, 'Could not find error templates');
        } else {
            $this->_tpl_error_messages = $message_templates;
        }
        
        // merge post-data into the configuration. We do not care about
        // post-items which are not defined in the config, ignore them.
        foreach($form_configuration as $field => $field_config) {
            if($field == 'settings') {
                // form specific settings
                $this->_settings = $field;
                continue;
            }
            
            $field_data = array();
            
            // set the field value
            if(!array_key_exists($field, $post_data) || empty($post_data[$field])) {
                $field_data['value'] = '';
            } else {
                if(!is_array($post_data[$field])) {
                    $field_data['value'] = trim($post_data[$field]);
                } else {
                    $field_data['value'] = call_user_func('trim', $post_data[$field]);
                }
            }
            
            // set the label
            if(!array_key_exists('label', $field_config) || empty($field_config['label'])) {
                $field_data['label'] = $field;
            } else {
                $field_data['label'] = $field_config['label'];
            }
            
            // set field specific error messages
            if(!array_key_exists('messages', $field_config)) {
                $field_data['messages'] = array();
            } else {
                $field_data['messages'] = $field_config['messages'];
            }
            
            // set rules
            if(!array_key_exists('rules', $field_config)
                    || !is_array($field_config['rules']) || empty($field_config['rules'])) {
                $this->set_message(self::FORMAL_STATE_ERROR, 'no rules found for field ['. $field .'] of form ['. $this->_key .']');
                return $this;
            } else {
                $field_data['rules'] = $field_config['rules'];
            }
            
            // add field data to pool
            $this->_field_data[$field] = $field_data;
        }
        
        // loaded configuration and merged with post_data.
        $this->_registered = true;

        return $this;
    }
    
    /**
     * Main interface; validate the form. Field-validation is being done in
     * @method validate_field()
     *
     * @return boolean
     */
    public function validate($return_boolean=false) {
        if($this->_registered !== true) {
            $this->set_message(self::FORMAL_STATE_ERROR, 'Please first register the form');
            return false;
        }
        
        if($this->_validated === true) return $this->_validated;
        
        // if nothing happens, everything is allright
        $this->_status == self::FORMAL_STATE_OK;
        
        // loop through the rules and call each check for the specific form field
        foreach($this->_field_data as $field => $cfg) {
            $passed = $this->validate_field($field, $cfg['value'], $cfg['rules'], $cfg['label']);
        }
        
        if($this->_status == self::FORMAL_STATE_OK) {
            $this->_validated = true;
        }
        return ($this->_status == self::FORMAL_STATE_OK);
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
                    $this->set_error_message($field, $label, $rule, $params);
                    return false;
                }
            } else {
                $this->set_message(self::FORMAL_STATE_ERROR, 'Could not process rule ['. $rule .']: rule not known');
                return false;
            }
        }
        $this->set_message(self::FORMAL_STATE_OK, '', $field);
        return true;
    }
    
    /**
     * This function is used to create an error message in case a field fails
     * to validate.
     *
     * @param string $field the name attribute of the form field
     * @param string $label the user readable label
     * @param string $rule the name of the rule
     * @param mixed|array $params array of parameters passed to the rule
     */
    private function set_error_message($field, $label, $rule, $params) {
        if(array_key_exists('messages', $this->_field_data[$field]) 
                && array_key_exists($rule, $this->_field_data[$field]['messages'])) {
            return $this->set_message(
                    self::FORMAL_STATE_ERROR, 
                    sprintf($this->_field_data[$field]['messages'][$rule], $label, $params), 
                    $field
            );    
        } else if(array_key_exists($rule, $this->_tpl_error_messages)) {
            return $this->set_message(
                    self::FORMAL_STATE_ERROR, 
                    sprintf($this->_tpl_error_messages[$rule], $label, $params), 
                    $field
            );
        }
        
        return $this->set_message(
                self::FORMAL_STATE_ERROR, 
                'An unknown error occured for field ['.$label.'] while parsing rule ['.$rule.']',
                $field
        );
    }
    
    /**
     * Save a message which will be processed by the frontend controller
     *
     * @param string $status what kind of message is this [error, warning, notice]
     * @param string $message the message content
     * @param string (optional) the form field this message relates to
     */
    function set_message($status, $message, $field=null) {
        $record = array(
            'status' => $status,
            'message' => $message,
        );
        if(!is_null($field) && !empty($field)) {
            $record['field'] = $field;
        }
        
        $this->_messages[] = $record;
        
        // adjust error level if needed;
        $this->_status =($status > $this->_status)?$status:$this->_status;
        
        return $record;
    }
    
    function report($return_json=true) {
        if($this->_status==null) {
            if(!$this->_registered) {
                $report['status'] = self::FORMAL_STATE_NOT_INITIALIZED;
            } else if(!$this->_validated) {
                $report['status'] = self::FORMAL_STATE_REGISTERED;
            } else {
                // nothing wrong, or everything ok? :)
                $report['status'] = self::FORMAL_STATE_OK;
            }
        } else {
            $report['status'] = $this->_status;
        }
        
        $report['messages'] = $this->_messages;
        $report['data'] = $this->data();

        return (!$return_json)?$report:json_encode($report);
    }
    
    /**************************** HELPER FUNCTIONS ****************************/
    
    /**
     * Get or set the formal key. If the key is set, this instance will be
     * reregistered.
     * 
     * @param type $key
     * @return type 
     */
    function key($key=null) {
        if($key == null) {
            return $this->_key;
        }
        return $this->register($key);
    }
    
    /**
     * @param string $key
     * @param mixed $value
     * @return mixed 
     */
    function data($key=null, $value=null) {
        if(is_null($key)) {
            // return all
            return $this->_custom_data;
        }
        
        if(is_null($value)) {
            // act as a getter
            if(!array_key_exists($key, $this->_custom_data)) {
                return false;
            }
            return $this->_custom_data[$key];
        }
        
        return $this->_custom_data[$key] = $value;
    }
    
    /**
     * @return boolean 
     */
    function registered() {
        return ($this->_registered === true);
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