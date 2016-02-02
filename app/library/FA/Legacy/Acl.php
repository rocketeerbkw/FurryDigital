<?php
/**
 * Access Control List (ACL) manager
 */

namespace FA\Legacy;

use \Entity\User;

class Acl
{
    protected $_em;
    protected $_auth;

    protected $_actions = NULL;

    public function __construct(\Doctrine\ORM\EntityManager $em, \FA\Auth $auth)
    {
        $this->_em = $em;
        $this->_auth = $auth;

        // Hard code actions for legacy types.
        $this->_actions = array(
            User::LEGACY_ACL_REGULAR => array(),
            User::LEGACY_ACL_ADMINISTRATOR => array('administer all', 'moderate all'),
            User::LEGACY_ACL_DEVELOPER => array('administer all', 'moderate all'),
            User::LEGACY_ACL_TESTER => array(),
        );
    }

    public function userAllowed($action, User $user = null)
    {
        static $roles;
        static $cache;

        $action = array_map('strtolower', (array)$action);
        asort($action);
        $memoize = md5(serialize($action));

        $user_id = ($user instanceof User) ? $user->id : 'anonymous';

        if( !isset($cache[$user_id][$memoize]) )
        {
            if($user instanceof User)
            {
                if(!isset($roles[$user_id]))
                {
                    $roles[$user_id] = array($user->access_level);
                }

                $cache[$user_id][$memoize] = $this->roleAllowed($roles[$user_id], $action);
            }
            else
            {
                $cache[$user_id][$memoize] = $this->roleAllowed(array('Unauthenticated'), $action);
            }
        }

        return $cache[$user_id][$memoize];
    }

    public function isAllowed($action)
    {
        static $is_logged_in, $user;

        if ($is_logged_in === NULL)
        {
            $user = $this->_auth->getLoggedInUser();
            $is_logged_in = ($user instanceof User);
        }

        if ($action == "is logged in")
            return ($is_logged_in);
        elseif ($action == "is not logged in")
            return (!$is_logged_in);
        elseif ($is_logged_in)
            return $this->userAllowed($action, $user);
        else
            return false;
    }

    public function roleAllowed($role_id, $action, $exact_only = FALSE)
    {
        if(is_array($role_id))
        {
            foreach($role_id as $r)
            {
                if($this->roleAllowed($r, $action)) //Once we've gotten a true, move forward
                    return true;
            }
            return false;
        }
        else if(is_array($action))
        {
            foreach($action as $a)
            {
                if($this->roleAllowed($role_id, $a))
                    return true;
            }
            return false;
        }
        else
        {
            // Without "exact_only" flag, matches based on root-level access are permitted.
            if (!$exact_only)
            {
                if (in_array('administer all', (array)$this->_actions[$role_id]))
                    return true;
            }

            if (isset($this->_actions[$role_id]) && in_array($action, $this->_actions[$role_id]))
                return true;

            return false;
        }
    }

    /**
     * Pretty wrapper around the 'isAllowed' function that throws a UI-friendly exception upon failure.
     *
     * @param $action
     * @throws \FA\Exception\NotLoggedIn
     * @throws \FA\Exception\PermissionDenied
     */
    public function checkPermission($action)
    {
        if (!$this->isAllowed($action))
        {
            if (!$this->_auth->isLoggedIn())
                throw new \FA\Exception\NotLoggedIn();
            else
                throw new \FA\Exception\PermissionDenied();
        }
    }
}