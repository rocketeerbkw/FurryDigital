<?php

return array(
    'method'        => 'post',
    'id'            => 'comment_form',
    'name'          => 'myform',
    'elements'      => array(
        'parent_id'      => array('hidden', array(
            'value' => ''
        )),
        
        'JSMessage'      => array('textarea', array(
            'class' => 'textarea textarearesize',
            'placeholder' => 'Click here to leave a comment.',
            'required' => true,
            'minlength' => 3,
            'maxlength' => 65535,
        )),

        // CSRF forgery prevention.
        'csrf' => array('csrf'),

        'submit'        => array('submit', array(
            'type'  => 'submit',
            'name'  => 'submit',
            'helper' => 'formButton',
            'label' => 'Post Your Comment',
            'class' => 'button centerthis floatright',
        )),
    ),
);