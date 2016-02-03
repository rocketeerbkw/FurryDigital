<?php
/**
 * Master Submission Edit Form
 */

use Entity\Upload;
use App\Legacy\Utilities as AppUtils;

$di = \Phalcon\Di::getDefault();
$config = $di['config'];

return array(
    'id' => 'browse_filters',
    'method' => 'post',
    'elements' => array(

        'category' => array('select', array(
            'label' => 'Category',
            'options' => AppUtils::reverseArray($config->fa->categories->toArray()),
        )),

        'theme' => array('select', array(
            'label' => 'Theme',
            'options' => AppUtils::reverseArray($config->fa->art_types->toArray()),
        )),

        'species' => array('select', array(
            'label' => 'Species',
            'options' => AppUtils::reverseArray($config->fa->species->toArray()),
        )),

        'gender' => array('select', array(
            'label' => 'Gender',
            'options' => AppUtils::reverseArray($config->fa->genders->toArray()),
        )),

        'rating' => array('multiCheckbox', array(
            'label' => 'Rating',
            'default' => Upload::RATING_GENERAL,
            'options' => array(
                Upload::RATING_GENERAL => 'General',
                Upload::RATING_MATURE  => 'Mature',
                Upload::RATING_ADULT   => 'Adult',
            ),
        )),

        'page' => array('hidden', array(
            'default' => 1,
        )),

        'submit' => array('submit', array(
            'type'  => 'submit',
            'label' => 'Apply Filters',
            'helper' => 'formButton',
            'class' => 'btn btn-default'
        )),

    ),
);