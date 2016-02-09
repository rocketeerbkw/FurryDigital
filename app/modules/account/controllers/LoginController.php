<?php
namespace Modules\Account\Controllers;

use Entity\User;
use Entity\UserExternal;

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

        $form = new \App\Form($this->current_module_config->forms->login);

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

        $this->view->form = $form;

        // Get external login options.
        $oauth_providers = $this->config->apis->oauth->providers->toArray();
        $external = array_filter($oauth_providers, function($info) { return $info['enabled']; });

        $this->view->external = $external;

        $this->assets->collection('header_css')
            ->addCss('zocial/zocial.css');
    }

    public function oauthAction()
    {
        $oauth_providers = $this->config->apis->oauth->providers->toArray();
        $provider_name = $this->getParam('provider');

        if (!isset($oauth_providers[$provider_name]))
            throw new \App\Exception('Provider not found! Cannot log in with this provider.');

        $provider_info = $oauth_providers[$provider_name];

        if (!$provider_info['enabled'])
            throw new \App\Exception('Provider is not currently enabled!');

        $config = [
            'callback' => $this->url->callback(),
            'keys' => [
                'id' => $provider_info['key'],
                'key' => $provider_info['key'],
                'secret' => $provider_info['secret']
            ]
        ];

        if (!empty($provider_info['scope']))
            $config['scope'] = $provider_info['scope'];

        $provider_classes = [
            '\Hybridauth\Provider\\'.ucfirst($provider_name),
            '\App\Auth\Provider\\'.ucfirst($provider_name),
        ];

        foreach($provider_classes as $provider_class)
        {
            if (class_exists($provider_class))
            {
                $oauth = new $provider_class($config);
                break;
            }
        }

        if (empty($oauth))
            throw new \App\Exception('Provider not found!');

        // Attempt authentication.
        $oauth->authenticate();

        // Load profile into database, get/create record.
        $user_profile = $oauth->getUserProfile();
        $external_result = UserExternal::processExternal($provider_name, $user_profile);

        if ($external_result['new_account'])
            $this->alert('<b>A new account has been created with your e-mail address.</b><br>If you need to log in to this account directly, use the "Forgot My Password" function in login.', 'blue');

        // Log in the user.
        $this->auth->setUser($external_result['user']);

        $this->alert('<b>Logged in via ' . $provider_info['name'] . '!</b>', 'green');

        return $this->redirectToStoredReferrer('login', $this->di['url']->route());
    }

    /*
    public function linkAction()
    {
        $this->acl->checkPermission('is logged in');
        $this->doNotRender();

        // Link external account.
        $user = $this->auth->getLoggedInUser();

        $provider_name = $this->getParam('provider');

        $ha_config = $this->_getHybridConfig();
        $hybridauth = new \Hybrid_Auth($ha_config);

        // try to authenticate with the selected provider
        $adapter = $hybridauth->authenticate($provider_name);

        if ($hybridauth->isConnectedWith($provider_name))
        {
            $user_profile = $adapter->getUserProfile();
            UserExternal::processExternal($provider_name, $user_profile, $user);

            $this->alert('<b>Account successfully linked!</b>', 'green');

            $this->redirectToRoute(array('module' => 'default', 'controller' => 'profile'));
            return;
        }
    }

    public function unlinkAction()
    {
        $this->acl->checkPermission('is logged in');
        $this->doNotRender();

        // Unlink external account.
        $user = $this->auth->getLoggedInUser();

        $provider_name = $this->getParam('provider');

        foreach($user->external_accounts as $acct)
        {
            if ($acct->provider == $provider_name)
                $acct->delete();
        }

        $this->alert('<b>Account successfully unlinked!</b>', 'green');

        $this->redirectToRoute(array('module' => 'default', 'controller' => 'profile'));
        return;
    }
    */
}