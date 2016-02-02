<?php
return array(
    'method' => 'post',
    'elements' => array(

        'name' => array('text', array(
            'label' => 'Folder Name',
            'class' => 'full-width',
            'required' => true,
            'maxLength' => 60,
        )),

        'description' => array('textarea', array(
            'label' => 'Folder Description',
            'class' => 'full-width full-height',
            'required' => true,
        )),

        'group_id' => array('select', array(
            'label' => 'Assign to Group',
            'options' => array(),
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