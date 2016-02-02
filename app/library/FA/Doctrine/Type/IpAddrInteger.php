<?php
namespace FA\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\IntegerType;

/**
 * Substitutes for MySQL's INET_NTOA and INET_ATON functions to handle IPs as unsigned integers.
 *
 * Class IpAddrInteger
 * @package FA\Doctrine\Type
 */
class IpAddrInteger extends IntegerType
{
    const TYPENAME = 'ip_integer';

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return self::inetAtoN($value);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null)
            return null;

        $value = (is_resource($value)) ? stream_get_contents($value, -1) : $value;

        return self::inetNtoA($value);
    }

    public function getName()
    {
        return self::TYPENAME;
    }

    /**
     * Force all fields to use unsigned integers (if DB layer supports it).
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        $fieldDeclaration['length'] = 11;
        $fieldDeclaration['unsigned'] = true;

        return parent::getSQLDeclaration($fieldDeclaration, $platform);
    }

    public static function inetAtoN($ip)
    {
        $ip = trim($ip);
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) return 0;
        return sprintf("%u", ip2long($ip));
    }

    public static function inetNtoA($num)
    {
        $num = trim($num);
        if ($num == "0") return "0.0.0.0";
        return long2ip(-(4294967295 - ($num - 1)));
    }
}