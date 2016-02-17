<?php
namespace App\Phalcon;

class Controller extends \Phalcon\Mvc\Controller
{
    /* Phalcon Initialization */

    public function beforeExecuteRoute()
    {
        $this->init();

        $this->assets->collection('header_css')
            ->addCss('css/default.css');

        $this->assets->collection('header_js')
            ->addJs('//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.min.js', false);

        $this->assets->collection('footer_js')
            ->addJs('//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js', false, false, array('async' => 'async'))
            ->addJs('//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/js/bootstrap.min.js', false)
            ->addJs('//cdnjs.cloudflare.com/ajax/libs/jquery-form-validator/2.2.43/jquery.form-validator.min.js', false)
            ->addJs('js/layout.js');

        return $this->preDispatch();
    }

    public function init()
    {
        $isAllowed = $this->permissions();
        if (!$isAllowed)
        {
            if (!$this->auth->isLoggedIn())
                throw new \App\Exception\NotLoggedIn;
            else
                throw new \App\Exception\PermissionDenied;
        }
    }

    protected function preDispatch()
    {
        $is_ajax = ($this->isAjax());
        $this->view->is_ajax = $is_ajax;

        if ($is_ajax)
        {
            $this->view->cleanTemplateAfter();
            $this->view->setLayout(null);
        }

        if ($this->hasParam('debug') && $this->getParam('debug') === 'true' && APP_APPLICATION_ENV != 'production')
        {
            error_reporting(E_ALL & ~E_STRICT);
            ini_set('display_errors', 1);
        }

        // NewRelic Logging.
        if (function_exists('newrelic_name_transaction')) {
            $app_url = '/'.$this->dispatcher->getModuleName().'/'.$this->dispatcher->getControllerName().'/'.$this->dispatcher->getActionName();
            newrelic_name_transaction($app_url);
        }

        return true;
    }

    public function afterExecuteRoute()
    {
        $this->postDispatch();
        $this->handleCache();
    }

    /**
     * Overridable function called after page handling is complete.
     */
    protected function postDispatch()
    {}

    /**
     * Overridable permissions check. Return false to generate "access denied" message.
     * @return bool
     */
    protected function permissions()
    {
        return true;
    }

    /* HTTP Cache Handling */

    protected $_cache_privacy = null;
    protected $_cache_lifetime = 0;

    /**
     * Set new HTTP cache "privacy" level, used by intermediate caches.
     *
     * @param $new_privacy "private" or "public"
     */
    public function setCachePrivacy($new_privacy)
    {
        $this->_cache_privacy = strtolower($new_privacy);
    }

    /**
     * Set new HTTP cache "lifetime", expressed as seconds after current time.
     *
     * @param $new_lifetime
     */
    public function setCacheLifetime($new_lifetime)
    {
        $this->_cache_lifetime = (int)$new_lifetime;
    }

    /**
     * Internal cache handling after page handling is complete.
     */
    protected function handleCache()
    {
        // Set default caching parameters for pages that do not customize it.
        if ($this->_cache_privacy === null)
        {
            $auth = $this->di->get('auth');

            if ($auth->isLoggedIn())
            {
                $this->_cache_privacy = 'private';
                $this->_cache_lifetime = 0;
            }
            else
            {
                $this->_cache_privacy = 'public';
                $this->_cache_lifetime = 30;
            }
        }

        if ($this->_cache_privacy == 'private')
        {
            // $this->response->setHeader('Cache-Control', 'must-revalidate, private, max-age=' . $this->_cache_lifetime);
            $this->response->setHeader('X-Accel-Expires', 'off');
        }
        else
        {
            // $this->response->setHeader('Cache-Control', 'public, max-age=' . $this->_cache_lifetime);
            $this->response->setHeader('X-Accel-Expires', $this->_cache_lifetime);
        }
    }

    /* URL Parameter Handling */

    /**
     * Retrieve parameter from request.
     *
     * @param $param_name
     * @param null $default_value
     * @return mixed|null
     */
    public function getParam($param_name, $default_value = NULL)
    {
        $params = $this->dispatcher->getParams();

        if (isset($params[$param_name]))
            return $params[$param_name];
        elseif ($this->request->has($param_name))
            return $this->request->get($param_name);
        else
            return $default_value;
    }

    /**
     * Alias for getParam()
     *
     * @deprecated Use getParam() instead.
     * @param $param_name
     * @param null $default_value
     * @return mixed|null
     */
    public function _getParam($param_name, $default_value = NULL)
    {
        return $this->getParam($param_name, $default_value);
    }

    /**
     * Detect if parameter is present in request.
     *
     * @param $param_name
     * @return bool
     */
    public function hasParam($param_name)
    {
        return ($this->getParam($param_name) !== null);
    }

    /**
     * Alias for hasParam()
     *
     * @deprecated Use hasParam() instead.
     * @param $param_name
     * @return bool
     */
    public function _hasParam($param_name)
    {
        return $this->hasParam($param_name);
    }

    /**
     * Trigger rendering of template.
     *
     * @param null $template_name
     */
    public function render($template_name = NULL)
    {
        if ($template_name === null)
            $new_view = $this->dispatcher->getControllerName().'/'.$this->dispatcher->getActionName();
        elseif (stristr($template_name, '/') !== false)
            $new_view = $template_name;
        else
            $new_view = $this->dispatcher->getControllerName().'/'.$template_name;

        $this->view->pick(array($new_view));
    }

    /**
     * Disable rendering of template for this page view.
     */
    public function doNotRender()
    {
        $this->view->disable();
    }

    /**
     * Render the page output as the supplied JSON.
     *
     * @param $json_data
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
     */
    public function renderJson($json_data)
    {
        $this->doNotRender();
        return $this->response->setJsonContent($json_data);
    }

    /**
     * Determines if a request is sent using the XMLHTTPRequest (AJAX) method.
     *
     * @return mixed
     */
    public function isAjax()
    {
        return $this->request->isAjax();
    }

    /* URL Redirection */

    /**
     * Redirect to the URL specified.
     *
     * @param $new_url
     * @param int $code
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
     */
    public function redirect($new_url, $code = 302)
    {
        $this->doNotRender();

        return $this->response->redirect($new_url, $code);
    }

    /**
     * Redirect to the route specified.
     *
     * @param $route
     * @param int $code
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
     */
    public function redirectToRoute($route, $code = 302)
    {
        $this->doNotRender();

        return $this->response->redirect($this->di['url']->route($route, $this->di), $code);
    }

    /**
     * Redirect with parameters from the current URL.
     *
     * @param string $route
     * @param int $code
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
     */
    public function redirectFromHere($route, $code = 302)
    {
        $this->doNotRender();

        return $this->response->redirect($this->di['url']->routeFromHere($route), $code);
    }

    /**
     * Redirect to the current page.
     *
     * @param int $code
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
     */
    public function redirectHere($code = 302)
    {
        $this->doNotRender();

        return $this->response->redirect($this->request->getUri(), $code);
    }

    /**
     * Redirect to the homepage.
     *
     * @param int $code
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
     */
    public function redirectHome($code = 302)
    {
        $this->doNotRender();

        return $this->response->redirect($this->di['url']->get(''), $code);
    }
    
    /**
     * Redirect with parameters to named route.
     *
     * @param string $route
     * @param int $code
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
     */
    public function redirectToName($name, $route_params = array(), $code = 302)
    {
        $this->doNotRender();

        return $this->response->redirect($this->di['url']->named($name, $route_params), $code);
    }

    /**
     * Force redirection to a HTTPS secure URL.
     */
    protected function forceSecure()
    {
        if (APP_APPLICATION_ENV == 'production' && !APP_IS_SECURE)
        {
            $this->doNotRender();

            $url = 'https://'.$this->request->getHttpHost().$this->request->getURI();
            return $this->response->redirect($url, 301);
        }
    }

    /**
     * Force redirection to a non-HTTPS URL for content reasons.
     */
    protected function forceInsecure()
    {
        if (APP_APPLICATION_ENV == 'production' && APP_IS_SECURE)
        {
            $this->doNotRender();

            $url = 'http://'.$this->request->getHttpHost().$this->request->getURI();
            return $this->response->redirect($url, 301);
        }
    }

    /* Referrer storage */
    protected function storeReferrer($namespace = 'default', $loose = true)
    {
        $session = $this->di['session']->get('referrer_'.$namespace);

        if( !isset($session->url) || ($loose && isset($session->url) && $this->di['url']->current() != $this->di['url']->referrer()) )
            $session->url = $this->di['url']->referrer();
    }

    protected function getStoredReferrer($namespace = 'default')
    {
        $session = $this->di['session']->get('referrer_'.$namespace);
        return $session->url;
    }

    protected function clearStoredReferrer($namespace = 'default')
    {
        $session = $this->di['session']->get('referrer_'.$namespace);
        unset($session->url);
    }

    protected function redirectToStoredReferrer($namespace = 'default', $default_url = false)
    {
        $referrer = $this->getStoredReferrer($namespace);
        $this->clearStoredReferrer($namespace);

        if( trim($referrer) == '' )
            if( $default_url )
                $referrer = $default_url;
            else
                $referrer = $this->di['url']->baseUrl();

        return $this->redirect($referrer);
    }

    protected function redirectToReferrer($default = false)
    {
        if( !$default )
            $default = $this->di['url']->baseUrl();

        return $this->redirect($this->di['url']->referrer($default));
    }

    /* Notifications */

    public function flash($message, $level = \App\Flash::INFO)
    {
        $this->alert($message, $level);
    }
    public function alert($message, $level = \App\Flash::INFO)
    {
        $this->di['flash']->addMessage($message, $level, TRUE);
    }

    /* Form Rendering */

    protected function renderForm(\App\Form $form, $mode = 'edit', $form_title = NULL)
    {
        $this->view->hide_title = false;
        $this->view->setViewsDir(APP_INCLUDE_BASE.'/modules/frontend/views/scripts/');

        // Show visible title.
        if ($form_title)
            $this->view->title = $form_title;

        $this->view->form = $form;
        $this->view->render_mode = $mode;

        return $this->view->pick('system/form');
    }

    /* Parameter Handling */

    protected function convertGetToParam()
    {
        return $this->redirectFromHere($_GET);
    }
}