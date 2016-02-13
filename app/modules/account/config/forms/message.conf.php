<?php
return array(
    'method' => 'post',
    'elements' => array(

        'recipient' => array('text', array(
            'label' => 'Recipient Username',
            'class' => 'full-width',
            'required' => true,
        )),

        'subject' => array('text', array(
            'label' => 'Subject',
            'class' => 'full-width',
            'required' => true,
            'maxLength' => 60,
        )),

        'message' => array('textarea', array(
            'label' => 'Message',
            'class' => 'full-width full-height',
            'required' => true,
        )),

        // CSRF forgery prevention.
        'csrf' => array('csrf'),

        'submit'        => array('submit', array(
            'type'  => 'submit',
            'label' => 'Send Message',
            'helper' => 'formButton',
            'class' => 'btn btn-default',
        )),

    ),
);