<?php
$di = \Phalcon\Di::getDefault();
$config = $di['config'];

// Assemble dropdown for trouble ticket types.
$types_raw = $config->fa->trouble_ticket_types->toArray();
$type_select = array();
foreach($types_raw as $group_name => $group_items)
{
    foreach($group_items as $item_name => $item_info)
        $type_select[$group_name][$item_info['id']] = $item_name;
}

return array(
    'method' => 'post',
    'elements' => array(

        'issue_type' => array('select', array(
            'label' => 'Ticket Category',
            'options' => $type_select,
            'default' => 2,
        )),

        'other' => array('text', array(
            'label' => 'Ticket Subject',
            'class' => 'full-width',
            'required' => true,
            'maxLength' => 60,
        )),

        'message' => array('textarea', array(
            'label' => 'Ticket Details',
            'class' => 'full-width full-height',
            'required' => true,
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