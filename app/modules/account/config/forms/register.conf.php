<?php
return array(
    'method' => 'post',
    'groups' => array(

        'account' => array(
            'legend' => 'Account Information',
            'elements' => array(

                'username' => array('text', array(
                    'label' => 'Desired Username',
                    'required' => true,
                )),

                'email' => array('text', array(
                    'label' => 'E-mail Address',
                    'class' => 'half-width',
                    'required' => true,
                    'validators' => array('EmailAddress'),
                )),

                'email2' => array('text', array(
                    'label' => 'Confirm E-mail Address',
                    'class' => 'half-width',
                    'required' => true,
                    'confirm' => 'email',
                    'validators' => array('EmailAddress'),
                )),

            ),
        ),

        'captcha_grp' => array(
            'legend' => 'Spam Protection',
            'elements' => array(

                'captcha' => array('captcha', array(
                    'label' => 'Prove You\'re a Human',
                )),

            ),
        ),

        'submit' => array(
            'elements' => array(
                'terms' => array('markup', array(
                    'markup' => '<p>By clicking on "I Accept" below, you are agreeing to the <a href="/tos" target="_blank">Terms of Service</a> and <a href="/aup" target="_blank">Submission Policy</a>.',
                )),

                // CSRF forgery prevention.
                'csrf' => array('csrf'),

                'submit'        => array('submit', array(
                    'type'  => 'submit',
                    'label' => 'I Accept. Create My Account!',
                    'helper' => 'formButton',
                    'class' => 'btn btn-default',
                )),
            ),
        ),

    ),
);