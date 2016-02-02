<?php
return array(
    'method' => 'post',
    'elements' => array(

        'message' => array('textarea', array(
            'label' => 'Ticket Details',
            'class' => 'full-width full-height',
            'required' => true,
        )),

        'reopen' => array('radio', array(
            'label' => 'Reopen Ticket',
            'options' => array(0 => 'No', 1 => 'Yes'),
            'default' => 0,
        )),

        'share_notes' => array('radio', array(
            'label' => 'Grant staff temporary permission to view notes you link to',
            'description' => 'NOTE: This does not grant admin access to your inbox.',
            'options' => array(0 => 'No', 1 => 'Yes'),
            'default' => 0,
        )),

        // CSRF forgery prevention.
        'csrf' => array('csrf'),

        'submit'        => array('submit', array(
            'type'  => 'submit',
            'label' => 'Save Changes',
            'helper' => 'formButton',
            'class' => 'btn btn-default',
        )),

    ),
);