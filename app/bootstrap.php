<?php
/**
 * Global bootstrap file.
 */

// Security settings
define("FA_IS_COMMAND_LINE", (PHP_SAPI == "cli"));
define("FA_IS_SECURE", (!FA_IS_COMMAND_LINE && (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on")) ? TRUE : FALSE);

// General includes
define("FA_INCLUDE_BASE", dirname(__FILE__));
define("FA_INCLUDE_ROOT", realpath(FA_INCLUDE_BASE.'/..'));
define("FA_INCLUDE_WEB", FA_INCLUDE_ROOT.'/web');
define("FA_INCLUDE_STATIC", FA_INCLUDE_WEB.'/static');

define("FA_INCLUDE_MODELS", FA_INCLUDE_BASE.'/models');
define("FA_INCLUDE_MODULES", FA_INCLUDE_BASE.'/modules');

define("FA_INCLUDE_TEMP", FA_INCLUDE_ROOT.'/../www_tmp');
define("FA_INCLUDE_CACHE", FA_INCLUDE_TEMP.'/cache');

define("FA_INCLUDE_LIB", FA_INCLUDE_BASE.'/library');
define("FA_INCLUDE_VENDOR", FA_INCLUDE_ROOT.'/vendor');

define("FA_UPLOAD_FOLDER", FA_INCLUDE_STATIC);

// Application environment.
if (isset($_SERVER['FA_APPLICATION_ENV']))
    define('FA_APPLICATION_ENV', $_SERVER['FA_APPLICATION_ENV']);
elseif (file_exists(FA_INCLUDE_BASE.'/.env'))
    define('FA_APPLICATION_ENV', include(FA_INCLUDE_BASE.'/.env'));
elseif (isset($_SERVER['X-FA-Dev-Environment']) && $_SERVER['X-FA-Dev-Environment'])
    define('FA_APPLICATION_ENV', 'development');
else
    define('FA_APPLICATION_ENV', 'development');

/**
 * CloudFlare Support:
 * Update internal IP and HTTPS status if CloudFlare is proxying the request.
 *
 * net-cat - 2011/05/16 - Requests that don't come from the load balancer won't
 * have these headers. Let the default values fall through in that case.
 *
 * yak - 2014/10.18 - Requests coming from Cloudflare set two headers containing original user IP
 */

if (isset($_SERVER['HTTP_CF_CONNECTING_IP']))
    $_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_CF_CONNECTING_IP'];
elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
    $_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_X_FORWARDED_FOR'];
elseif (isset($_SERVER['HTTP_X_FORWARDED_FROM']))
    $_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_X_FORWARDED_FROM'];

if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']))
    $_SERVER['HTTPS'] = (strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) == 'https');

// Composer autoload.
$autoloader = require(FA_INCLUDE_VENDOR . DIRECTORY_SEPARATOR . 'autoload.php');

// Save configuration object.
require(FA_INCLUDE_LIB . '/FA/Config.php');
require(FA_INCLUDE_LIB . '/FA/Config/Item.php');

$config = new \FA\Config(FA_INCLUDE_BASE.'/config');
$config->preload(array('application'));

// Set URL constants from configuration.
$app_cfg = $config->application;
if ($app_cfg->base_url)
    define('FA_BASE_URL', $app_cfg->base_url);

// Apply PHP settings.
$php_settings = $config->application->phpSettings->toArray();
foreach($php_settings as $setting_key => $setting_value)
{
    if (is_array($setting_value)) {
        foreach($setting_value as $setting_subkey => $setting_subval)
            ini_set($setting_key.'.'.$setting_subkey, $setting_subval);
    } else {
        ini_set($setting_key, $setting_value);
    }
}

// Loop through modules to find configuration files or libraries.
$module_config_dirs = array();
$modules = scandir(FA_INCLUDE_MODULES);

$module_config = array();
$phalcon_modules = array();

foreach($modules as $module)
{
    if ($module == '.' || $module == '..')
        continue;

    $config_directory = FA_INCLUDE_MODULES.DIRECTORY_SEPARATOR.$module.DIRECTORY_SEPARATOR.'config';
    if (file_exists($config_directory))
        $module_config[$module] = new \FA\Config($config_directory);

    $phalcon_modules[$module] = ucfirst($module);
}

$autoload_classes = $config->application->autoload->toArray();
foreach($autoload_classes['psr0'] as $class_key => $class_dir)
    $autoloader->add($class_key, $class_dir);

foreach($autoload_classes['psr4'] as $class_key => $class_dir)
    $autoloader->addPsr4($class_key, $class_dir);

// Set up Dependency Injection
if (FA_IS_COMMAND_LINE)
    $di = new \Phalcon\DI\FactoryDefault\CLI;
else
    $di = new \Phalcon\DI\FactoryDefault;

// Configs
$di->setShared('config', $config);
$di->setShared('module_config', function() use ($module_config) { return $module_config; });
$di->setShared('phalcon_modules', function() use ($phalcon_modules) { return $phalcon_modules; });

// Router
if (FA_IS_COMMAND_LINE) {
    $router = new \Phalcon\CLI\Router;
    $di->setShared('router', $router);
} else {
    $di->setShared('router', function () use ($di) {
        $router = new \FA\Phalcon\Router(false);
        $router->setUriSource(\FA\Phalcon\Router::URI_SOURCE_SERVER_REQUEST_URI);

        $router->setDi($di);

        $router_config = $di->get('config')->routes->toArray();

        $router->setDefaultModule($router_config['default_module']);
        $router->setDefaultController($router_config['default_controller']);
        $router->setDefaultAction($router_config['default_action']);
        $router->removeExtraSlashes(true);

        foreach ((array)$router_config['custom_routes'] as $route_path => $route_params)
        {
            $route = $router->add($route_path, $route_params);

            if (isset($route_params['name']))
                $route->setName($route_params['name']);
        }

        return $router;
    });
}

// Database
$di->setShared('em', function() use ($config) {
    try    {
        $db_conf = $config->application->resources->doctrine->toArray();
        $db_conf['conn'] = $config->db->toArray();

        $em = \FA\Phalcon\Service\Doctrine::init($db_conf);
        return $em;
    }
    catch(\Exception $e)
    {
        throw new \FA\Exception\Bootstrap($e->getMessage());
    }
});

$di->setShared('db', function() use ($config) {
    try
    {
        $db_conf = $config->application->resources->doctrine->toArray();
        $db_conf['conn'] = $config->db->toArray();

        $config = new \Doctrine\DBAL\Configuration;
        return \Doctrine\DBAL\DriverManager::getConnection($db_conf['conn'], $config);
    }
    catch(\Exception $e)
    {
        throw new \FA\Exception\Bootstrap($e->getMessage());
    }
});

// Auth and ACL
$di->setShared('auth', array(
    'className' => '\FA\Auth',
    'arguments' => array(
        array('type' => 'service', 'name' => 'session'),
    )
));

$di->setShared('acl', array(
    'className' => '\FA\Legacy\Acl',
    'arguments' => array(
        array('type' => 'service', 'name' => 'em'),
        array('type' => 'service', 'name' => 'auth'),
    )
));

// Caching
$di->setShared('cache_driver', function() use ($config) {
    $cache_config = $config->cache->toArray();

    switch($cache_config['cache'])
    {
        case 'redis':
            $cache_driver = new \Stash\Driver\Redis;
            $cache_driver->setOptions($cache_config['redis']);
        break;

        case 'memcached':
            $cache_driver = new \Stash\Driver\Memcache;
            $cache_driver->setOptions($cache_config['memcached']);
        break;

        case 'file':
            $cache_driver = new \Stash\Driver\FileSystem;
            $cache_driver->setOptions($cache_config['file']);
        break;

        default:
        case 'memory':
        case 'ephemeral':
            $cache_driver = new \Stash\Driver\Ephemeral;
        break;
    }

    // Register Stash as session handler if necessary.
    if (!($cache_driver instanceof \Stash\Driver\Ephemeral))
    {
        $pool = new \Stash\Pool($cache_driver);
        $pool->setNamespace(\FA\Cache::getSitePrefix('session'));

        $session = new \Stash\Session($pool);
        \Stash\Session::registerHandler($session);
    }

    return $cache_driver;
});

$di->set('cache', array(
    'className' => '\FA\Cache',
    'arguments' => array(
        array('type' => 'service', 'name' => 'cache_driver'),
        array('type' => 'parameter', 'value' => 'user'),
    )
));

// Register URL handler.
$di->setShared('url', array(
    'className' => '\FA\Url',
    'arguments' => array(
        array('type' => 'service', 'name' => 'config'),
        array('type' => 'service', 'name' => 'request'),
        array('type' => 'service', 'name' => 'dispatcher'),
    )
));

// Register session service.
$di->setShared('session', '\FA\Session');

// Register CSRF prevention security token service.
$di->setShared('csrf', array(
    'className' => '\FA\Csrf',
    'arguments' => array(
        array('type' => 'service', 'name' => 'session'),
    )
));

// Register view helpers.
$di->setShared('viewHelper', '\FA\Phalcon\Service\ViewHelper');

// Register Flash notification service.
$di->setShared('flash', array(
    'className' => '\FA\Flash',
    'arguments' => array(
        array('type' => 'service', 'name' => 'session'),
    )
));

// Register global text parsing helper.
$di->setShared('parser', array(
    'className' => '\FA\Parser',
    'arguments' => array(
        array('type' => 'service', 'name' => 'config'),
        array('type' => 'service', 'name' => 'url'),
    )
));

/*
// TODO: Re-enable support for locale for FA users.
// PVL-specific customization.
$system_tz = \PVL\Customization::get('timezone');
@date_default_timezone_set($system_tz);
*/

$di->setShared('fa', function () use ($di) {
    $fa = new \FA\Legacy($di);
    $fa->init();
    return $fa;
});

$di->setShared('user', function() use ($di) {
    $auth = $di['auth'];

    if ($auth->isLoggedIn())
        return $auth->getLoggedInUser();
    else
        return new \FA\Auth\AnonymousUser();
});