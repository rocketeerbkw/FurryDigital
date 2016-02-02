<?php
return array(
    'method' => 'post',
    'groups' => array(
        'journal_info' => array(
            'legend' => 'Journal Details',
            'elements' => array(

                'subject'      => array('text', array(
                    'label' => 'Journal Subject',
                    'class' => 'full-width',
                    'required' => true,
                    'maxLength' => 60,
                )),

                'message' => array('textarea', array(
                    'label' => 'Journal Message',
                    'class' => 'full-width full-height',
                    'required' => true,
                )),

            ),
        ),

        'site_settings' => array(
            'legend' => 'Customize Settings',
            'elements' => array(

                'disable_comments' => array('radio', array(
                    'label' => 'Allow Comments',
                    'default' => 0,
                    'options' => array(0 => 'Enable Comments', 1 => 'Disable Comments'),
                )),

                'is_featured' => array('radio', array(
                    'label' => 'Make Featured Journal',
                    'description' => 'This will add the journal\'s contents to your profile page.',
                    'default' => 0,
                    'options' => array(0 => 'No', 1 => 'Yes'),
                )),

            ),
        ),

        'submit_grp' => array(
            'elements' => array(

                // CSRF forgery prevention.
                'csrf' => array('csrf'),

                'submit'        => array('submit', array(
                    'type'  => 'submit',
                    'label' => 'Save Changes',
                    'helper' => 'formButton',
                    'class' => 'btn btn-default',
                )),

            ),
        ),
    ),
);