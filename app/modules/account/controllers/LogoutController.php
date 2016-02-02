<?php
namespace Modules\Account\Controllers;

use \Entity\User;

class LogoutController extends BaseController
{
    public function indexAction()
    {
        $csrf_result = $this->di['csrf']->verify($this->getParam('csrf'), 'login');
        if (!$csrf_result['is_valid'])
            throw new \App\Exception('CSRF Error: '.$csrf_result['message']);

        $this->auth->logout();

        $this->di['session']->destroy();

        $this->redirectHome();
    }
}