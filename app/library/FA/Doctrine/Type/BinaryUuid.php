<?php
namespace FA\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\BinaryType;

class BinaryUuid extends BinaryType
{
    const TYPENAME = 'binary_uuid';

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return self::uuidToBin($value);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null)
            return null;

        $value = (is_resource($value)) ? stream_get_contents($value, -1) : $value;

        return self::binToUuid($value);
    }

    public function getName()
    {
        return self::TYPENAME;
    }

    /**
     * Force all fields to be the BINARY type, length 16 (the UUID binary length).
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        $fieldDeclaration['length'] = 16;
        $fieldDeclaration['fixed'] = true;

        return parent::getSQLDeclaration($fieldDeclaration, $platform);
    }

    public static function uuidToBin($uuid)
    {
        return pack("H*" , str_replace('-', '', $uuid));
    }

    public static function binToUuid($bin)
    {
        return preg_replace('/^([0-9a-f]{8})([0-9a-f]{4})([0-9a-f]{4})([0-9a-f]{4})([0-9a-f]{12})$/', '$1-$2-$3-$4-$5', bin2hex($bin));
    }
}