<?php
namespace Entity\Traits;

trait EncryptionTrait
{
    /**
     * Helper function that proxies the global crypto container's "encrypt" function.
     *
     * @param $message
     * @return mixed
     */
    protected static function encrypt($message)
    {
        $di = \Phalcon\Di::getDefault();
        $crypto = $di->get('crypto');

        return $crypto->encrypt($message);
    }

    /**
     * Helper function that proxies the global crypto container's "decrypt" function.
     *
     * @param $message
     * @return mixed
     */
    protected static function decrypt($message)
    {
        $di = \Phalcon\Di::getDefault();
        $crypto = $di->get('crypto');

        return $crypto->decrypt($message);
    }
}