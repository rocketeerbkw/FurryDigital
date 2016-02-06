<?php
namespace Modules\Account\Controllers;

use Entity\User;
use Entity\UserExternal;
use OAuth\Common\Http\Uri\UriFactory;
use OAuth\Common\Storage\Session;
use OAuth\Common\Consumer\Credentials;
use OAuth\ServiceFactory;

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

        return $this->renderForm($form, 'edit', 'Login');
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

        $storage = new Session();

        // Setup the credentials for the requests
        $uriFactory = new UriFactory();
        $currentUri = $uriFactory->createFromSuperGlobalArray($_SERVER);
        $currentUri->setQuery('');

        $credentials = new Credentials(
            $provider_info['key'],
            $provider_info['secret'],
            $currentUri->getAbsoluteUri()
        );

        if (!empty($provider_info['scopes']))
            $scopes = (array)$provider_info['scopes'];
        else
            $scopes = array();

        $serviceFactory = new ServiceFactory();
        $oauth_service = $serviceFactory->createService($provider_name, $credentials, $storage, $scopes);

        if ($this->hasParam('code'))
        {
            $state = isset($_GET['state']) ? $_GET['state'] : null;
            $oauth_service->requestAccessToken($_GET['code'], $state);

            $user = UserExternal::processExternal($provider_name, $oauth_service);
            $this->auth->setUser($user);

            $this->alert('<b>Logged in via ' . $provider_info['name'] . '!</b>', 'green');

            return $this->redirectToStoredReferrer('login', $this->di['url']->route());
        }
        elseif ($this->hasParam('oauth_token'))
        {
            $token = $storage->retrieveAccessToken($provider_name);

            // This was a callback request from the provider, get the token.
            $oauth_service->requestAccessToken(
                $_GET['oauth_token'],
                $_GET['oauth_verifier'],
                $token->getRequestTokenSecret()
            );

            $user = UserExternal::processExternal($provider_name, $oauth_service);
            $this->auth->setUser($user);

            $this->alert('<b>Logged in via '.$provider_info['name'].'!</b>', 'green');

            return $this->redirectToStoredReferrer('login', $this->di['url']->route());
        }
        else
        {
            if (method_exists($oauth_service, 'requestRequestToken'))
            {
                $token = $oauth_service->requestRequestToken();
                $url = $oauth_service->getAuthorizationUri(array('oauth_token' => $token->getRequestToken()));
            }
            else
            {
                $url = $oauth_service->getAuthorizationUri();
            }

            return $this->response->redirect($url->getAbsoluteUri());
        }
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