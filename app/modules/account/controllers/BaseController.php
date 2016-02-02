<?php
namespace Modules\Account\Controllers;

class BaseController extends \App\Phalcon\Controller
{
    protected function permissions()
    {
        return $this->acl->isAllowed('is logged in');
    }
}