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

        $username = 'Buster_ñeeCE TEST!-Test';
        \App\Utilities::print_r(\Entity\User::getLowerCase($username));

        $user = \Entity\User::findByLower('SlvrEagle23');
        echo $user->username;

        // -------- END HERE -------- //

        Debug::log('Done!');

        exit;
    }
}