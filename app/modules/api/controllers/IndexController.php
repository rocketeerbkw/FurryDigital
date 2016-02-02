<?php
namespace Modules\Api\Controllers;

class IndexController extends BaseController
{
    /**
     * Public index for API.
     */
    public function indexAction()
    {
        return $this->returnSuccess('The API is online and functioning.');
    }

    /**
     * Heartbeat function, returns the current UNIX timestamp.
     */
    public function statusAction()
    {
        return $this->returnSuccess(array(
            'online' => 'true',
            'timestamp' => time(),
        ));
    }
}