<?php
namespace Modules\Frontend\Controllers;

use \App\Debug;
use \App\Utilities;

class UtilController extends BaseController
{
    public function permissions()
    {
        return true;
        // return $this->acl->isAllowed('administer all');
    }

    public function testAction()
    {
        $this->doNotRender();

        set_time_limit(0);
        ini_set('memory_limit', '-1');

        Debug::setEchoMode();

        // -------- START HERE -------- //

        $crypto = $this->di->get('crypto');

        $encrypted = $crypto->encrypt('TEST MESSAGE');

        var_dump($encrypted);

        $decrypted = $crypto->decrypt($encrypted);

        var_dump($decrypted);

        // -------- END HERE -------- //

        Debug::log('Done!');

        exit;
    }
}