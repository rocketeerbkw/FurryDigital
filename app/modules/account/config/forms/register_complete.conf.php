<?php
return array(
    'method' => 'post',
    'groups' => array(

        'account' => array(
            'legend' => 'Account Information',
            'elements' => array(

                // Put in by registration request
                'username' => array('text', array(
                    'label' => 'Desired Username',
                    'disabled' => 'disabled',
                )),

                // Put in by registration request
                'email' => array('text', array(
                    'label' => 'E-mail Address',
                    'class' => 'half-width',
                    'disabled' => 'disabled',
                )),

                'password' => array('password', array(
                    'label' => 'Password',
                    'description' => 'Keep your account safe! Choose a strong and unique password, at least 6 characters long.',
                    'minLength' => 6,
                    'required' => true,
                )),

                'password2' => array('password', array(
                    'label' => 'Confirm Password',
                    'confirm' => 'password',
                    'minLength' => 6,
                    'required' => true,
                )),

            ),
        ),

        'profile' => array(
            'legend' => 'Profile Details',
            'elements' => array(

                'fullname' => array('text', array(
                    'label' => 'Display Name',
                    'class' => 'half-width',
                    'required' => true,
                )),

                'birthday' => array('date', array(
                    'label' => 'Birthday',
                    'min'   => date('Y-m-d', strtotime('-100 years')),
                    'max'   => date('Y-m-d', strtotime('-5 years')),
                    'required' => true,
                )),

            ),
        ),

        'submit' => array(
            'elements' => array(
                'terms' => array('markup', array(
                    'markup' => '
                        <p>FurryDigital values your privacy and will not share this information. See our <a href="/tos">privacy policy</a> for more information.</p>
                        <p>By clicking on \'I accept\' below you are agreeing to the <a href="/tos">Terms of Service</a> and the <a href="/aup">Submission Policy</a>.</p>
                        <p>Yes, we know, we already displayed this message once. It is here as a reminder just so you can\'t say "I haven\'t read it" later, because you really should.</p>',
                )),

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