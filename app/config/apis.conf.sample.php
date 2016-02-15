<?php
/**
 * Configuration for Third-Party APIs.
 */

return array(

    // Used to encrypt sensitive database content at rest.
    'crypto_key' => 'def00000784902fd08d9aac696a918dcfb1eb8b019a502814b742aa16d5c8c0dc62b39b25e1a7d6695d8c3b56fc295db434fc0d8f5c8abcbc5edb8ce860b6dcfea37e477',

    // Amazon AWS Management
    'amazon_aws' => array(
        'access_key_id'     => '',
        'secret_access_key' => '',
        's3_bucket'         => '',
    ),

    // Mandrill SMTP service.
    'smtp' => array(
        'server'        => 'smtp.mandrillapp.com',
        'port'          => '587',
        'auth'          => 'login',
        'username'      => 'loobalightdark@gmail.com',
        'password'      => 'i4t3dB83dEl4LnREt1w5Vg', // Test key, replace with production key!
    ),

    // CloudFlare API.
    'cloudflare' => array(
        'domain'        => 'furry.digital',
        'email'         => '',
        'api_key'       => '',
    ),

    // Google Common APIs server key (get from https://console.developers.google.com/)
    'google_apis_key' => '',

    // ReCAPTCHA Service keys.
    'recaptcha' => array(
        'public_key' => '6LekzxETAAAAAHq1415fNHkg4UqBTAQ_3gY2BmES',
        'private_key' => '6LekzxETAAAAAJHF8npHcbx5DB6gOAwe2Ut0Stsh',
    ),

    // Hybrid/oAuth API settings.
    'oauth' => array(
        'providers' => array(
            'google' => array(
                'enabled'   => false,

                'name'      => 'Google+',
                'class'     => 'googleplus',
                'icon'      => 'icon-google-plus',

                'key'       => '',
                'secret'    => '',
                'scopes'    => array('userinfo_email', 'userinfo_profile'),
            ),
            'facebook' => array(
                'enabled'   => false,

                'name'      => 'Facebook',
                'class'     => 'facebook',
                'icon'      => 'icon-facebook',

                'key'       => '',
                'secret'    => '',
            ),
            'twitter' => array(
                'enabled'   => false,

                'name'      => 'Twitter',
                'class'     => 'twitter',
                'icon'      => 'icon-twitter',

                'key'       => '',
                'secret'    => '',
            ),
            'tumblr' => array(
                'enabled'   => false,

                'name'      => 'Tumblr',
                'class'     => 'tumblr',
                'icon'      => 'icon-tumblr',

                'key'       => '',
                'secret'    => '',
            ),
            'deviantart' => array(
                'enabled'   => false,

                'name'      => 'DeviantArt',
                'class'     => 'deviantart',
                'icon'      => 'icon-deviantart',

                'key'       => '',
                'secret'    => '',
            ),
        ),
    ),

);