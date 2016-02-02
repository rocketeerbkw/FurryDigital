<?php
namespace FA;

class Csrf
{
    /**
     * @var \FA\Session\Instance
     */
    protected $_session;

    protected $_csrf_code_length = 10;

    public function __construct(Session $session)
    {
        $this->_session = $session->get('csrf');
    }

    public function generate($namespace = 'general')
    {
        $key = NULL;
        if (isset($this->_session[$namespace]))
        {
            $key = $this->_session[$namespace]['key'];
            if (strlen($key) !== $this->_csrf_code_length)
                $key = NULL;
        }

        if (!$key)
            $key = $this->randomString($this->_csrf_code_length);

        $this->_session[$namespace] = array(
            'key'       => $key,
            'timestamp' => time(),
        );

        return $key;
    }

    public function verify($key, $namespace = 'general')
    {
        if (empty($key))
            return array('is_valid' => false, 'message' => 'A CSRF token is required for this request.');

        if (strlen($key) !== $this->_csrf_code_length)
            return array('is_valid' => false, 'message' => 'Malformed CSRF token supplied.');

        if (!isset($this->_session[$namespace]))
            return array('is_valid' => false, 'message' => 'No CSRF token supplied for this namespace.');

        $namespace_info = $this->_session[$namespace];

        if (strcmp($key, $namespace_info['key']) !== 0)
            return array('is_valid' => false, 'message' => 'Invalid CSRF token supplied.');

        // Compare against time threshold (CSRF keys last 60 minutes).
        $threshold =  $namespace_info['timestamp']+60*60;

        if (time() >= $threshold)
            return array('is_valid' => false, 'message' => 'This CSRF token has expired!');

        return array('is_valid' => true);
    }

    /**
     * Generates a random string of given $length.
     *
     * @param Integer $length The string length.
     * @return String The randomly generated string.
     */
    public function randomString($length)
    {
        $seed = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijqlmnopqrtsuvwxyz0123456789';
        $max = strlen( $seed ) - 1;

        $string = '';
        for ( $i = 0; $i < $length; ++$i )
            $string .= $seed{intval( mt_rand( 0.0, $max ) )};

        return $string;
    }
}