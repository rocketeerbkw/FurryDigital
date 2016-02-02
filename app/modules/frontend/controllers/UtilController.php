<?php
namespace Modules\Frontend\Controllers;

use \FA\Debug;
use \FA\Utilities;

class UtilController extends BaseController
{
    public function permissions()
    {
        // return $this->acl->isAllowed('administer all');
    }

    public function testAction()
    {
        $this->doNotRender();

        set_time_limit(0);
        ini_set('memory_limit', '-1');

        Debug::setEchoMode();

        // -------- START HERE -------- //



        // -------- END HERE -------- //

        Debug::log('Done!');
    }
}