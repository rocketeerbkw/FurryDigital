<?php
/**
 * Master Submission Edit Form
 */

use Entity\Upload;
use FA\Legacy\Utilities as FAUtils;

$di = \Phalcon\Di::getDefault();
$config = $di['config'];

$vars_config = $config->fa->user_variables->toArray();

$order_by_select = array();
foreach($vars_config['search_order']['allowed'] as $order_name)
    $order_by_select[$order_name] = ucfirst($order_name);

$direction_select = array();
foreach($vars_config['search_direction']['allowed'] as $direction_name)
    $direction_select[$direction_name] = strtoupper($direction_name);

$perpage_select = array();
foreach($vars_config['perpage']['allowed'] as $perpage_name)
    $perpage_select[$perpage_name] = $perpage_name;

$upload_types_config = $config->fa->upload_types->toArray();

$upload_type_select = array();
foreach($upload_types_config as $upload_type_key => $upload_type_info)
    $upload_type_select[$upload_type_key] = $upload_type_info['name'];

return array(
    'id' => 'search_filters',
    'method' => 'post',
    'elements' => array(

        'q' => array('text', array(
            'label' => 'Search for',
            'class' => 'full-width',
        )),

        'order_by' => array('select', array(
            'label' => 'Sort by',
            'options' => $order_by_select,
            'default' => $vars_config['search_order']['default'],
        )),

        'order_direction' => array('select', array(
            'label' => 'Order results',
            'options' => $direction_select,
            'default' => $vars_config['search_direction']['default'],
        )),

        'perpage' => array('select', array(
            'label' => 'Results per page',
            'options' => $perpage_select,
            'default' => $vars_config['perpage']['default'],
        )),

        'range' => array('radio', array(
            'label' => 'Show results since',
            'options' => array(
                0       => 'All time',
                86400   => 'One day ago',
                259200  => 'Three days ago',
                604800  => 'One week ago',
                2592000 => 'One month ago',
            ),
            'default' => 0,
        )),

        'rating' => array('multiCheckbox', array(
            'label' => 'Rating',
            'options' => array(
                Upload::RATING_GENERAL => 'General',
                Upload::RATING_MATURE  => 'Mature',
                Upload::RATING_ADULT   => 'Adult',
            ),
            'default' => Upload::RATING_GENERAL,
        )),

        'type' => array('multiCheckbox', array(
            'label' => 'Type',
            'options' => $upload_type_select,
        )),

        'mode' => array('radio', array(
            'label' => 'Match keywords',
            'options' => array(
                'all' => 'ALL of the words',
                'any' => 'ANY of the words',
                'extended' => 'Extended (see help)',
            ),
        )),

        'page' => array('hidden', array(
            'default' => 1,
        )),

        'submit' => array('submit', array(
            'type'  => 'submit',
            'label' => 'Search',
            'helper' => 'formButton',
            'class' => 'btn btn-default'
        )),

    ),
);