<?php
/**
 * Gravatar - Globally Recognized Avatars Connector
 */
namespace App\Service;

class Gravatar
{
    public static function get($email, $size=50, $default='mm')
    {
        $grav_prefix = (APP_IS_SECURE) ? 'https://secure.gravatar.com' : 'http://www.gravatar.com';
        
        $url_params = array(
            'd'     => $default,
            'r'     => 'g',
            'size'  => $size,
        );
        $grav_url = $grav_prefix.'/avatar/'.md5(strtolower($email)).'?'.http_build_query($url_params);
        return htmlspecialchars($grav_url);
    }
}