<?php
/**
 * Better URL Routing
 */

namespace FA\View\Helper;

class Route extends HelperAbstract
{
    public function route($params)
    {
        return $this->di['url']->route($params);
    }
}