<?php

namespace Entity;

trait CommentTrait
{        
    /**
     * Returns if a admin comment
     *
     * @return boolean
     */
    public function isAdminComment()
    {
        $di = \Phalcon\Di::getDefault();
    
        return $di['acl']->userAllowed('administer all', $this->user);
    }
    
    /**
     * Returns the Level Width of the comment
     * LEGACY
     *
     * @return integer
     */
    public function getLevelWidth()
    {    
        return \FA\Legacy\Utilities::levpercent($this->getParentDepth());
    }
    
    /**
     * Returns if it was edited
     * If the difference of posted and update time is >0, then this has been edited!
     * LEGACY
     *
     * @return bool
     */
    public function hasBeenEdited()
    {
        return (date_posted - date_updated) > 0;
    }
    
    /**
     * Returns the creation date all nicely formatted and human-readable.
     * LEGACY
     *
     * @return string
     */
    public function getFormattedDate()
    {
        return \FA\Utilities::fa_date_format($this->date_posted, $this->user->getTimezoneDiff());
    }
    
    /**
     * Returns if the user can edit the comment
     *
     * @return bool
     */
    public function canEdit($user = NULL)
    {
        /*if (!($user instanceof User))
            return false;*/
    
        $em = self::getEntityManager();
        $child_comments = $em->createQuery('SELECT COUNT(u) FROM ' . get_class() . ' u WHERE u.parent_id = :parent_id')
                            ->setParameter('parent_id', $this->id)
                            ->getSingleScalarResult();
        
        // If more than one child_comment, then there have been replies!
        $has_replies = $child_comments > 0;
        
        return !$has_replies && $user->id == $this->user->id && $this->created_at > time() - self::getEditDuration();
    }
    
    /**
     * Returns if the comment is hidden
     *
     * @return bool
     */
    public function isHidden()
    {
        return !is_null($this->deleting_user);
    }
    
    /**
     * Returns if the user can hide the comment
     *
     * @param $user          The user we're testing against
     * @return bool
     */
    public function canHide($user = NULL)
    {
        $di = \Phalcon\Di::getDefault();
        $page_owner = $this->upload->user;
    
        return $user instanceof User && $page_owner instanceof User
            && ($di['acl']->userAllowed('administer all', $user) // Has admin permissions?
            || $user->id == $this->user->id // Is the comment owner?
            || $user->id == $page_owner->id); // Is the page owner?
    }
    
    /**
     * Returns if the user can unhide the comment
     *
     * @param $user          The user we're testing against
     * @return bool
     */
    public function canUnhide($user = NULL)
    {
        $di = \Phalcon\Di::getDefault();
        
        return $user instanceof User && ($di['acl']->userAllowed('administer all', $user)); // Has admin permissions?
    }
    
    /**
     * Returns the original parent (The parent comment with NULL)
     *
     * @return entity
     */
    public function getOriginParent()
    {
        $parent = $this->parent;
        
        // If this comment's parent exists, then continue up the tree. If not, we're at the origin!
        return !is_null($parent) ? $parent->getOriginParent() : $this;
    }
    
    /**
     * Traces the parents back, getting the path to the primary parent in descending order
     * If the comment does not have a parent, it'll include itself for consistency sake
     *
     * NOTE: You do not have to set any of the params as this is a recursive function
     *
     * @param $parent_path      Default: array()
     * @return integers         Parents
     */
    public function getParentPath($parent_path = array())
    {
        $parent = $this->parent;
        
        if (!is_null($parent)) {
            array_push($parent_path, $parent->id);
            
            return $parent->getParentPath($parent_path);
        }
        else {
            // Parent is NULL, check if the $parent_path is empty.
            // If so, this comment is it's own parent (At the root)
            if (empty($parent_path))
                array_push($parent_path, $this->id);
        }
        
        return $parent_path;
    }
    
    
    /**
     * Returns how "deep" the comment is (AKA How many parents are above it)
     *
     * NOTE: You do not have to set any of the params as this is a recursive function
     *
     * @param $depth    The starting depth (Default: 0)
     * @return integer
     */
    public function getParentDepth($depth = 0)
    {
        $parent = $this->parent;
        
        // Infinite loop prevention!
        if ($depth > 100)
            return $depth;
        
        // If this comment's parent exists, then continue up the tree. If not, we're at the origin!
        return !is_null($parent) ? $parent->getParentDepth($depth + 1) : $depth;
    }

    /**
     * Static Functions
     */
    
    /**
     * Returns the edit duration for Upload Comments
     * NOTE: Make sure to override this, otherwise will return -1.
     *
     * LEGACY, TEMPORARY
     *
     * @return integer
     */
    public static function getEditDuration()
    {
        return -1;
    }
}
