<?php
/**
 * Application Settings
 */

$session_lifetime = 86400*1;

$config = array(
    // Application name
    'name'              => 'FloofClub',
    'analytics_code'    => 'UA-60038462-1',
    
    // Primary application web address
    'base_url'          => (FA_IS_SECURE ? 'https://' : 'http://').'floof.club',

    // Path the cookie should use.
    'cookie_domain'     => '.floof.club',

    // Subfolder for the application (if applicable)
    'base_uri'          => '/',

    // Base of the static URL.
    'static_uri'        => '/static/',

    // Web address for API calls.
    // TODO: No API yet!
    // 'api_url'           => (FA_IS_SECURE ? 'https://' : 'http://').'api.floof.club',

    // Local path for art.
    'art_path'          => FA_INCLUDE_WEB.'/uploads/art',

    // List of folders that should exist in any user's art folder.
    'art_folders'       => array('avatars', 'images', 'text', 'audio', 'video'),

    // Web URL for art.
    'art_url'           => '/uploads/art',

    // Local path for avatars.
    'avatars_path'      => FA_INCLUDE_WEB.'/uploads/avatars',

    // Web URL for avatars.
    'avatars_url'       => '/uploads/avatars',
    
    // FA Messenger mail settings
    'mail'              => array(
        'templates'         => FA_INCLUDE_BASE.'/messages',
        'from_addr'         => 'noreply@floof.club',
        'from_name'         => 'FloofClub',
        'use_smtp'          => true,
    ),

    'phpSettings'       => array(
        'display_startup_errors' => 0,
        'display_errors'        => 0,
        'log_errors'            => 1,
        'error_log'             => FA_INCLUDE_TEMP.'/php_errors.log',
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
        FA_INCLUDE_LIB.'/ThirdParty',
    ),
    
    'pluginpaths'       => array(
        'FA\Application\Resource\\' => 'FA/Application/Resource',
    ),
    
    'autoload'          => array(
        'psr0'      => array(
            'FA'        => FA_INCLUDE_LIB,
            'Entity'    => FA_INCLUDE_MODELS,
        ),
        'psr4'      => array(
            '\\Proxy\\'     => FA_INCLUDE_TEMP.'/proxies',
        ),
    ),

    'resources'         => array(
        /* RESOURCES: Locale */
        'locale'            => array(
            'default'           => 'en_US',
        ),

        /* RESOURCES: Doctrine ORM Layer */
        'doctrine'          => array(
            'autoGenerateProxies' => (FA_APPLICATION_ENV == "development"),
            'proxyNamespace'    => 'Proxy',
            'proxyPath'         => FA_INCLUDE_TEMP.'/proxies',
            'modelPath'         => FA_INCLUDE_MODELS,
        ),
    ),
);

/**
 * Development mode changes.
 */


if (FA_APPLICATION_ENV != 'production')
{
    $config['phpSettings']['display_startup_errors'] = 1;
    $config['phpSettings']['display_errors'] = 1;

    // Update if your local configuration differs.
    $config['base_url'] = 'http://localhost:8080';
    $config['cookie_domain'] = '';

    unset($config['api_url']);
    unset($config['upload_url']);
}

if (FA_APPLICATION_ENV == 'staging')
{
    $config['base_url'] = 'http://fa.dashdev.net/';
    $config['cookie_domain'] = '.dashdev.net';
}

return $config;
