<?php
/**
 * Configuration for Third-Party APIs.
 */

return array(

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

);