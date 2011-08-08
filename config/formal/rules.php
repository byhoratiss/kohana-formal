<?php defined('SYSPATH') or die('No direct script access.');

return array(
    'example' => array(
        'settings' => array(),
        
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
        ),
    )
);