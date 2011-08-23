<?php defined('SYSPATH') or die('No direct script access.');

return array(
    
    // just a simple example
    'basic' => array(
        'settings' => array(
            'messagePane' => 'paneBasic',
            'reportMethod' => array('field', 'pane')
        ),
        
        'input_field' => array(
            'label' => 'The first input field',
            'rules' => array('required' => true),
        ),
        'email' => array(
            'label' => 'E-mail address',
            'rules' => array('required' => true, 'email' => true)
        )
    ),
    
    'basic_callback' => array(
        'settings' => array(
            'messagePane' => 'paneBasicCallback',
            'afterSubmit' => 'callback',
            'afterSubmitCallback' => '&customCallback'
        ),
        
        'input_field' => array(
            'label' => 'The first input field',
            'rules' => array('required' => true)
        ),
        
        'email' => array(
            'label' => 'E-mail address',
            'rules' => array('required' => true, 'email' => true)
        )
    ),
    
    'basic_report' => array(
        'settings' => array(
            'messagePane' => 'paneBasicReport',
            'afterSubmit' => 'report'
        ),
        
        'input_field' => array(
            'label' => 'The first input field',
            'rules' => array('required' => true)
        ),
        
        'email' => array(
            'label' => 'E-mail address',
            'rules' => array('required' => true, 'email' => true)
        )
    ),
    
    'full' => array(
        'settings' => array(
            'messagePane' => 'paneFull'
        ),
        
        // validation rules
        'input1' => array(
            'label' => 'First input',
            'rules' => array('required' => true),
        ),
        'input2' => array(
            'label' => 'Second input',
            'rules' => array('required' => true, 'email' => true)
        ),
        'input3' => array(
            'label' => 'Third input',
            'rules' => array('required' => true, 'min' => '3', 'max' => '8')
        ),
        'input4' => array(
            'label' => 'Fourth',
            'rules' => array('required' => true, 'numeric' => 'true')
        ),
        'input5' => array(
            'label' => 'Fifth',
            'rules' => array('required' => true, 'set' => array('one', 'two', 'three'))
        ),
        'input6' => array(
            'label' => 'Sixth',
            'rules' => array('required' => true, 'date' => 'true')
        )
    )
);