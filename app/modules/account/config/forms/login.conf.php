<?php
$di = \Phalcon\Di::getDefault();
$url = $di['url'];

return array(
    'method'        => 'post',
    'elements'      => array(

        'username'      => array('text', array(
            'label' => 'Username',
            'class' => 'half-width',
            'spellcheck' => 'false',
            'required' => true,
        )),

        'password'      => array('password', array(
            'label' => 'Password',
            'description' => '<a href="'.$url->route(array('module' => 'account', 'controller' => 'recover')).'">Forgot your password?</a>',
            'class' => 'half-width',
            'required' => true,
        )),

        // CSRF forgery prevention.
        'csrf' => array('csrf'),

        'submit'        => array('submit', array(
            'type'  => 'submit',
            'label' => 'Log in',
            'helper' => 'formButton',
            'class' => 'btn btn-default',
        )),
    ),
);