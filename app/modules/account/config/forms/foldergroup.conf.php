<?php
return array(
    'method' => 'post',
    'elements' => array(

        'name' => array('text', array(
            'label' => 'Group Name',
            'class' => 'full-width',
            'required' => true,
            'maxLength' => 60,
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