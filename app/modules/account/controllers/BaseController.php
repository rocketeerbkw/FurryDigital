<?php
namespace Modules\Account\Controllers;

class BaseController extends \FA\Phalcon\Controller
{
    protected function permissions()
    {
        return $this->acl->isAllowed('is logged in');
    }
}