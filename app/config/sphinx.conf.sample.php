<?php
/**
 * Sphinx search engine configuration
 */

return array(
    'host'      => '127.0.0.1', // 10.0.0.50
    'port'      => 3312,

    'max_query_time' => 10*1000,
    'connection_timeout' => 5,
    'limits'    => 2000,
);