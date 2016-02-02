<?php
namespace Modules\Profile\Controllers;

use Entity\User;

class BaseController extends \FA\Phalcon\Controller
{
    /**
     * @var \Entity\User The owner of the page being viewed.
     *                   (NOT the same as $di['user'], the current viewing user)
     */
    protected $owner;

    /**
     * @var boolean Current logged in user is watching.
     */
    protected $is_watching;

    protected function preDispatch()
    {
        parent::preDispatch();
        return true;
    }

    protected function permissions()
    {
        return true;
    }

    /**
     * Check the user's "no guests" setting.
     * @return bool
     * @throws \FA\Exception\NotLoggedIn
     */
    protected function _enforceNoGuests()
    {
        if (!$this->auth->isLoggedIn())
        {
            if ($this->owner->getVariable('no_guests'))
            {
                $this->alert('<b>This user has has elected to make their content available to registered users only.</b><br />Please log in or register to continue.', 'red');
                throw new \FA\Exception\NotLoggedIn;
            }
        }

        return true;
    }

    /**
     * Get the user referenced in the "username" parameter.
     *
     * @return \Entity\User|null
     * @throws \FA\Exception
     */
    protected function _getUser(User $record = null)
    {
        if (!($record instanceof User))
        {
            if (!$this->hasParam('username'))
                throw new \FA\Exception('No username provided!');

            $lower = $this->getParam('username');

            $record = $this->em->createQuery('SELECT us, uv
            FROM Entity\User us
            LEFT JOIN us.vars uv
            WHERE us.lower = :user_lower')
                ->setParameter('user_lower', $lower)
                ->execute();

            if (count($record) == 0)
                throw new \FA\Exception('User not found!');

            $record = $record[0];
        }

        // Check for a disabled account.
        if ($this->dispatcher->getActionName() !== 'unwatch')
        {
            if ($record->getVariable('account_disabled') && !$this->acl->isAllowed('administer all'))
            {
                $settings_url = $this->url->route(array('module' => 'account', 'controller' => 'settings'));
                $unwatch_url = $this->url->routeFromHere(array('action' => 'unwatch', 'username' => $lower));

                $output = 'User "'.htmlspecialchars($record['username']).'" has voluntarily disabled access to their account and all of its contents.<br><br>If this is your userpage and you would like to re-enable it, you may do so by logging in and re-enabling it in your <a href="'.$settings_url.'">Account Settings</a>.<br><br>If you came here to unwatch this user you may do so by <a href="'.$unwatch_url.'">clicking here</a>.';
                throw new \FA\Exception($output);
            }
        }

        $this->owner = $record;
        $this->view->owner = $record;

        // Detect if the user is currently watching this person.
        $is_watching_raw = $this->em->createQuery('SELECT 1 FROM Entity\Watch w WHERE w.target_id = :target_id AND w.user_id = :user_id')
            ->setParameter('target_id', $record->id)
            ->setParameter('user_id', $this->user->id)
            ->getOneOrNullResult();

        $this->is_watching = !empty($is_watching_raw);
        $this->view->is_watching = $this->is_watching;

        return $record;
    }

    protected function _logPageView()
    {
        $session = $this->session->get('pageviews');
        $session_key = 'u'.$this->owner->id;

        $do_increment = false;

        if (!isset($session[$session_key]))
        {
            $last_visit = $session[$session_key];

            if ($last_visit > 86400)
                $do_increment = true;
        }
        else
        {
            $last_visit = 0;
            $do_increment = true;
        }

        if ($do_increment)
        {
            $this->owner->pageviews++;
            $this->owner->save();

            $session[$session_key] = time();
        }
    }
}