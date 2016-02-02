<?php

return array(
    'method'        => 'post',

    'elements'      => array(
        'shout' => array('textarea', array(
            'class' => 'full-width half-height',
            'placeholder' => 'Type here to leave a shout!',
            'required' => true,
            'minlength' => 3,
            'maxlength' => 222,
        )),

        // CSRF forgery prevention.
        'csrf' => array('csrf'),

        'submit'        => array('submit', array(
            'type'  => 'submit',
            'helper' => 'formButton',
            'label' => 'Submit Message',
            'class' => 'btn btn-sm btn-default',
        )),
    ),
);