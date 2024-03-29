<?php
/**
 * Application Settings
 */

$session_lifetime = 86400*1;

$config = array(
    // Application name
    'name'              => 'FurryDigital',
    'analytics_code'    => 'UA-37359273-8',
    
    // Primary application web address
    'base_url'          => 'https://furry.digital',

    // Path the cookie should use.
    'cookie_domain'     => '.furry.digital',

    // Subfolder for the application (if applicable)
    'base_uri'          => '/',

    // Base of the static URL.
    'static_uri'        => '/static/',

    // Web address for API calls.
    // TODO: No API yet!
    // 'api_url'           => (APP_IS_SECURE ? 'https://' : 'http://').'api.furry.digital',

    // Local path for art.
    'art_path'          => 's3://art.furry.digital',

    // Web URL for art.
    'art_url'           => 'https://art.furry.digital',

    // List of folders that should exist in any user's art folder.
    'art_folders'       => array('avatars', 'images', 'text', 'audio', 'video'),

    // Local path for avatars.
    'avatars_path'      => 's3://avatars.furry.digital',

    // Web URL for avatars.
    'avatars_url'       => 'https://avatars.furry.digital',
    
    // Messenger mail settings
    'mail'              => array(
        'templates'         => APP_INCLUDE_BASE.'/messages',
        'from_addr'         => 'noreply@furry.digital',
        'from_name'         => 'FurryDigital',
        'use_smtp'          => true,
    ),

    'phpSettings'       => array(
        'display_startup_errors' => 0,
        'display_errors'        => 0,
        'log_errors'            => 1,
        'error_log'             => APP_INCLUDE_TEMP.'/php_errors.log',
        'error_reporting'       => E_ALL & ~E_NOTICE & ~E_WARNING & ~E_STRICT,
        'session' => array(
            'use_only_cookies'  => 1,
            'gc_maxlifetime'    => $session_lifetime,
            'gc_probability'    => 1,
            'gc_divisor'        => 100,
            'cookie_lifetime'   => $session_lifetime,
            'hash_function'     => 'sha512',
            'hash_bits_per_character' => 4,
        ),
    ),
    
    'includePaths'      => array(
        APP_INCLUDE_LIB.'/ThirdParty',
    ),
    
    'autoload'          => array(
        'psr0'      => array(
            'App'       => APP_INCLUDE_LIB,
            'Entity'    => APP_INCLUDE_MODELS,
        ),
        'psr4'      => array(
            '\\Proxy\\'     => APP_INCLUDE_TEMP.'/proxies',
        ),
    ),

    'resources'         => array(
        /* RESOURCES: Locale */
        'locale'            => array(
            'default'           => 'en_US',
        ),

        /* RESOURCES: Doctrine ORM Layer */
        'doctrine'          => array(
            'autoGenerateProxies' => (APP_APPLICATION_ENV == "development"),
            'proxyNamespace'    => 'Proxy',
            'proxyPath'         => APP_INCLUDE_TEMP.'/proxies',
            'modelPath'         => APP_INCLUDE_MODELS,
        ),
    ),
);

/**
 * Development mode changes.
 */

if (APP_APPLICATION_ENV != 'production')
{
    $config['art_path'] = APP_INCLUDE_WEB.'/uploads/art';
    $config['art_url'] = '/uploads/art';

    $config['avatars_path'] = APP_INCLUDE_WEB.'/uploads/avatars';
    $config['avatars_url'] = '/uploads/avatars';

    $config['phpSettings']['display_startup_errors'] = 1;
    $config['phpSettings']['display_errors'] = 1;

    // Update if your local configuration differs.
    $config['base_url'] = 'http://localhost:8080';
    $config['cookie_domain'] = '';

    unset($config['api_url']);
    unset($config['upload_url']);
}

if (APP_APPLICATION_ENV == 'staging')
{
    $config['base_url'] = 'http://staging.furry.digital/';
}

return $config;
