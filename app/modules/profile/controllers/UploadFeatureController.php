<?php
namespace Modules\Profile\Controllers;

use \Entity\Upload;
use \Entity\UploadComment;
use \Entity\Favorite;

class UploadFeatureController extends BaseController
{
    public function replyAction()
    {    
        $upload_id = (int)$this->getParam('id');
    
        // Create the comment forms
        $form_config = $this->current_module_config->forms->upload_comment;
        
        $form = new \FA\Form($form_config);
        
        $form->populate($_POST);
        
        if ($_POST && $form->isValid($_POST) && $this->csrf->verify($form->getValue('csrf'), '_upload_comments')) {            
            // TODO: Check comment rate limit here!
            
            // Grab the submission information
            $upload = Upload::find($upload_id);

            if (!($upload instanceof Upload))
                throw new \FA\Exception('Upload not found!');
                
            // Prevent posting if comments are locked
            if ($upload->comments_locked)
                throw new \FA\Exception('Comments are locked on this Upload!');
            
            // Verify the user can even progress further
            self::_userCheck($upload);
            
            // Create the comment
            $record = new UploadComment();
            
            $record->message = $form->getValue('JSMessage');
            $record->user = $this->user;
            $record->upload = $upload;
            
            // If there is a parent comment, verify if it's legit by checking if it exists within this upload.
            $parent_id = (int)$form->getValue('parent_id');

            if ($parent_id > 0) {
                $parent_com = UploadComment::find($parent_id);
                
                if (($parent_com instanceof UploadComment) && $upload->id == $parent_com->upload_id)
                    $record->parent = $parent_com;
                else
                    throw new \FA\Exception('Invalid parent comment!');
            }
            
            $record->save();
            
            // Redirect to the Upload page to our comment!
            // TODO: Add a way to auto-lock on to the comment. Maybe a perma-link style approach?
            return $this->redirectToName('upload_view', array('id' => $upload->id));
        }
        
        // Redirect back to the Upload page regardless if we're successful
        return $this->redirectToName('upload_view', array('id' => $upload->id));
    }

    public function editAction()
    {
        $comment_id = (int)$this->getParam('id');
    
        $form_config = $this->current_module_config->forms->upload_comment;
        
        $form = new \FA\Form($form_config);
        
        $form->populate($_POST);
        
        if ($_POST && $form->isValid($_POST) && $this->csrf->verify($form->getValue('csrf'), '_upload_comments')) {            
            // TODO: Check comment edit rate limit here!
            
            $comment = UploadComment::find($comment_id);

            if (!($comment instanceof UploadComment))
                throw new \FA\Exception('Comment not found!');
            
            if (!$comment->canEdit($this->user))
                throw new \FA\Exception('Unable to edit comment!');
            
            $upload = $comment->upload;
                
            // Prevent posting if comments are locked
            if ($upload->comments_locked)
                throw new \FA\Exception('Comments are locked on this Upload!');
            
            // Verify the user can even progress further
            self::_userCheck($upload);
            
            // Update the comment's text
            $comment->message = $form->getValue('JSMessage');
            
            $comment->save();
            
            // Redirect to the Upload page to our comment!
            // TODO: Add a way to auto-lock on to the comment. Maybe a perma-link style approach?
            return $this->redirectToName('upload_view', array('id' => $upload->id));
        }
    }

    public function hideAction()
    {
        $csrf_key = $this->getParam('key');
        
        // Verify this isn't a cross-domain attack before proceeding
        if ($this->csrf->verify($csrf_key, '_upload_comments')) {
            // TODO: Check comment hiding rate limit here!
            $comment_id = (int)$this->getParam('id');
            
            $comment = UploadComment::find($comment_id);

            if (!($comment instanceof UploadComment))
                throw new \FA\Exception('Comment not found!');
            
            if (($comment->hidden && !$comment->canUnhide($this->user)) || !$comment->canHide($this->user))
                throw new \FA\Exception('Unable to change hidden state of comment!');
            
            $upload = $comment->upload;
                
            // Prevent posting if comments are locked
            if ($upload->comments_locked)
                throw new \FA\Exception('Comments are locked on this Upload!');
            
            // Verify the user can even progress further
            self::_userCheck($upload);
            
            // Toggle if the comment is hidden
            $comment->deleting_user = ($comment->isHidden() ? null : $this->user);
            
            $comment->save();
            
            // Redirect to the Upload page to our comment!
            // TODO: Add a way to auto-lock on to the comment. Maybe a perma-link style approach?
            return $this->redirectToName('upload_view', array('id' => $upload->id));            
        }
    }

    public function favoriteAction()
    {
        $csrf_key = $this->getParam('key');
        
        // Verify this isn't a cross-domain attack before proceeding
        if ($this->csrf->verify($csrf_key, '_upload_content')) {
            // TODO: Check comment hiding rate limit here!
            $upload_id = (int)$this->getParam('id');
            
            $upload = Upload::find($upload_id);
            
            // Verifying if the Upload exists
            if (!($upload instanceof Upload))
                throw new \FA\Exception('Upload not found!');
            
            $favorite = $this->em->createQuery('SELECT f FROM \Entity\Favorite f WHERE f.upload_id = :upload_id')
                                ->setParameter('upload_id', $upload->id)
                                ->getArrayResult();
            
            // Verify the user can even progress further
            //self::_userCheck($upload);
            
            // If the favorite exists, delete it. If not, create it!
            // TODO: Look into soft deleting
            if (count($favorite) > 0)
                \FA\Utilities::print_r($favorite);
                //$favorite->delete();
            else {
                $favorite = new Favorite();
                
                $favorite->upload = $upload;
                $favorite->user = $this->user;
                
                $favorite->save();
            }
            
            
            
            // Redirect to the Upload page to our comment!
            // TODO: Add a way to auto-lock on to the comment. Maybe a perma-link style approach?
            return $this->redirectToName('upload_view', array('id' => $upload->id));            
        }
    }
    
    protected function _userCheck($upload = NULL, $skipBlocklist = false) {
        $logged_user = $this->user;
    
        // Prevent banned users from posting
        // LEGACY        
        // TODO: Report error to user on how they're banned and redirect back to the submission
        if ($logged_user->suspend && $logged_user->access_level == User::LEGACY_ACL_BANNED)            
            throw new \FA\Exception('Banned users cannot comment!');

        // LEGACY
        // TODO: Add check for disabled accounts (Need to find where that's stored)
        /*
        // disabled account check
        if($_USER['vars']->getint('account_disabled')) {
            $output = 'Your user profile is currently disabled and disabled accounts are not allowed to post.<br />Your comment on a submission was not added.<br />';
            redirect('Error posting a comment', $output);
        }
        */
    
        // Blocklist check
        // applies only if the commenter is a regular user
        /*
        if($_USER['accesslevel'] == 0) {
            // check in the currently logged in user who is making the comment is in the page owner's blocklist
            if(is_blocklisted($_USER['lower'], $PAGE_OWNER_INFO['blocklist'])) {
                $output = 'You appear to be on <u>'.htmlspecialchars($PAGE_OWNER_INFO['username']).'\'s</u> block list.<br />Your submission comment was not added.<br />';
                redirect('Error posting a comment', $output);
            }
        }
        */
    }
    
    protected function permissions()
    {
        return $this->acl->isAllowed('is logged in');
    }
}