<?php
namespace Modules\Account\Controllers;

use \Entity\User;

class LoginController extends BaseController
{
    public function permissions()
    {
        return true;
    }

    public function indexAction()
    {
        if (!$_POST)
        {
            $this->storeReferrer('login', false);
            $this->forceSecure();
        }

        $form = new \FA\Form($this->current_module_config->forms->login);

        if ($_POST && $form->isValid($_POST))
        {
            $login_success = $this->auth->authenticate($form->getValues());

            if ($login_success)
            {
                $user = $this->auth->getLoggedInUser();

                $this->alert('<b>Logged in successfully. Welcome back, ' . $user->username . '!</b><br>For security purposes, log off when your session is complete.', 'green');

                return $this->redirectToStoredReferrer('login', $this->di['url']->route());
            }
            else
            {
                $form->addError('username', 'Your username and password could not be authenticated. Please try again!');
            }
        }

        // Auto-bounce back if logged in.
        if ($this->auth->isLoggedIn())
            return $this->redirectToStoredReferrer('login', $this->di['url']->route());

        return $this->renderForm($form, 'edit', 'Login');
    }
}