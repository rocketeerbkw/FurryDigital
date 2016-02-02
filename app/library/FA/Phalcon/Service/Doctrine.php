<?php
namespace FA\Phalcon\Service;

use \Doctrine\Common\ClassLoader;
use \Doctrine\DBAL\Types\Type;

class Doctrine
{
    public static function init($options)
    {
        if(empty($options))
            return false;

        // Register custom data types.
        Type::addType('json', 'FA\Doctrine\Type\Json');
        Type::addType('unixdatetime', 'FA\Doctrine\Type\UnixDateTime');
        Type::addtype('binary_uuid', 'FA\Doctrine\Type\BinaryUuid');
        Type::addtype('ip_integer', 'FA\Doctrine\Type\IpAddrInteger');

        Type::overrideType('array', 'FA\Doctrine\Type\SoftArray');
        Type::overrideType('datetime', 'FA\Doctrine\Type\UTCDateTime');

        // Fetch and store entity manager.
        $em = self::getEntityManager($options);

        $conn = $em->getConnection();
        $platform = $conn->getDatabasePlatform();

        $platform->markDoctrineTypeCommented(Type::getType('json'));
        $platform->markDoctrineTypeCommented(Type::getType('unixdatetime'));
        $platform->markDoctrineTypeCommented(Type::getType('binary_uuid'));
        $platform->markDoctrineTypeCommented(Type::getType('ip_integer'));

        return $em;
    }

    protected static function getEntityManager($options)
    {
        $config = new \Doctrine\ORM\Configuration;

        // Handling for class names specified as platform types.
        if ($options['conn']['platform'])
        {
            $class_obj = new \ReflectionClass($options['conn']['platform']);
            $options['conn']['platform'] = $class_obj->newInstance();
        }

        // Special handling for the utf8mb4 type.
        if ($options['conn']['driver'] == 'pdo_mysql' && $options['conn']['charset'] == 'utf8mb4')
        {
            $options['conn']['platform'] = new \FA\Doctrine\Platform\MysqlUnicode;
        }

        $metadata_driver = $config->newDefaultAnnotationDriver($options['modelPath']);
        $config->setMetadataDriverImpl($metadata_driver);

        $cache = new \FA\Doctrine\Cache;
        // $cache->setNamespace('doctrine_');

        $config->setMetadataCacheImpl($cache);
        $config->setQueryCacheImpl($cache);
        $config->setResultCacheImpl($cache);

        $config->setProxyDir($options['proxyPath']);
        $config->setProxyNamespace($options['proxyNamespace']);

        if (isset($options['conn']['debug']) && $options['conn']['debug'])
            $config->setSQLLogger(new \FA\Doctrine\Logger\EchoSQL);

        $config->addFilter('softdelete', '\FA\Doctrine\Filter\SoftDelete');
        $config->addCustomNumericFunction('RAND', '\FA\Doctrine\Functions\Rand');

        $config->addCustomStringFunction('FIELD', 'DoctrineExtensions\Query\Mysql\Field');
        $config->addCustomStringFunction('IF', 'DoctrineExtensions\Query\Mysql\IfElse');

        $evm = new \Doctrine\Common\EventManager();
        $em = \Doctrine\ORM\EntityManager::create($options['conn'], $config, $evm);

        $em->getFilters()->enable("softdelete");

        // Workaround to allow ENUM types to exist as strings in Doctrine.
        $platform = $em->getconnection()->getDatabasePlatform();
        $platform->registerDoctrineTypeMapping('enum', 'string');

        // Try the connection before rendering the page.
        $em->getConnection()->connect();

        return $em;
    }
}