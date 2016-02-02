<?php
/**
 * Forgot Password Form
 */

return array(
    'method'        => 'post',
    'elements'      => array(

        'username' => array('text', array(
            'label' => 'Your Username',
            'required' => true,
        )),

        'email' => array('text', array(
            'label' => 'E-mail Address',
            'class' => 'half-width',
            'validators' => array('EmailAddress'),
            'required' => true,
        )),

        'captcha' => array('captcha', array(
            'label' => 'Prove You\'re a Human',
        )),

        // CSRF forgery prevention.
        'csrf' => array('csrf'),

        'submit'        => array('submit', array(
            'type'  => 'submit',
            'label' => 'Send Recovery Code',
            'helper' => 'formButton',
            'class' => 'btn btn-default',
        )),
    ),
);