<?php
namespace Modules\Profile\Controllers;

use \Entity\Upload;
//use \DateTime as DateTime;

class UploadController extends BaseController
{
    // TODO: Figure out what this is. I suspect it's for versioning files, but not entirely sure
    const STATIC_ASSET_MODIFICATION_DATE = 2015101700;

    public function indexAction()
    {
        $upload_id = (int)$this->getParam('id');
        
        // Grab the submission information (Which includes the uploader's info and comments)
        $upload = Upload::find($upload_id);

        if (!($upload instanceof Upload))
            throw new \FA\Exception('Upload not found!');

        $view = $this->view;
        
        $view->upload = $upload;
        
        $view->is_favorited = (false ? '+Favorite' : '-Favorite');
        $view->comment_csrf_str = $this->csrf->generate('_upload_comments');
        $view->upload_csrf_str = $this->csrf->generate('_upload_content');
        $view->file_mime = $upload->getMIME();
        $view->keyword_arr = $upload->getKeywords();
        $view->created_at = \FA\Utilities::fa_date_format($upload->created_at, $upload->user->getTimezoneDiff());
        
        if ($this->user != NULL) {
            // Determine if the user is the owner of the upload
            $view->is_owner = $upload->user->id == $this->user->id;
            
            // Get if the user prefer fullview first
            $view->fullview = $this->user->fullview ? 'true' : 'false'; // Apparently, Volt doesn't seem to want convert straight to string
        }
        
        // Comments!
        // Create the comment forms
        $form_config = $this->current_module_config->forms->upload_comment->toArray();
        
        $form_config['action'] = $this->url->named('upload_view', array('id' => $upload->id)) . '/comment/new'; // Add the action so they can actually comment!
        
        $view->comment_form = new \FA\Form($form_config);
        
        // Reply form. Uses the same config, but different id.
        $form_config['action'] = ''; // No need for this.
        
        $form_config['id'] = 'reply_form';
        
        $view->reply_form = new \FA\Form($form_config);
        
        // Edit form. Same story.
        $form_config['id'] = 'edit_form';
        
        $view->edit_form = new \FA\Form($form_config);

        // Construct the comments        
        $comment_ents = \Entity\UploadComment::getRepository()->findBy(
            array('upload_id' => $upload->id),
            array('id' => 'DESC')
        );
        
        // TODO: Move to CommentTrait for a more global use
        // Initialize our upload comment array
        $up_comments = array();
        
        foreach ($comment_ents as $comment) {
            // Get the comment's parents
            $parent_path = array_reverse($comment->getParentPath());
            
            // Map the array, creating new arrays along the way
            $results = self::_mapArray($parent_path, array($comment), 'a');
            
            // Merge our new array with our overall one!
            $up_comments = array_merge_recursive($results, $up_comments);
        }
        
        // Flatten the array to allow Volt to run through it without issue
        $view->upload_comments = \Nette\Utils\Arrays::flatten($up_comments);
        
        // Only need to do these when users with access need to see these stats.
        if ($this->acl->isAllowed('administer all')) {
            $view->total_deleted_comments = 0;
            $view->total_deleted_comments_by_admin = 0;
            $view->total_deleted_comments_by_uploader = 0;  
            $view->total_deleted_comments_by_poster = 0;
        
            // Get the total comments deleted
            foreach ($comment_ents as $comment) {
                $deleting_user = $comment->deleting_user;
            
                if ($deleting_user != NULL) { // Post has been deleted
                    $view->total_deleted_comments++;
                    
                    // Determine who deleted it!
                    if($deleting_user->id == $comment->user_id) // Poster deleted it!
                        $view->total_deleted_comments_by_poster++;
                    elseif($deleting_user->id == $upload->user.id) // Uploader deleted it!
                        $view->total_deleted_comments_by_uploader++;
                    elseif($this->acl->userAllowed('administer all', $deleting_user)) // Admin deleted it!
                        $view->total_deleted_comments_by_admin++;
                }
            }
        }
        
        // Grab the EXIF info (If any) and pass it to the view
        // TODO: Will need to determine if we need to include more or less information
        //$exif = exif_read_data($upload->getFullPath(), 'EXIF');
        //$view->exif_info = ($exif ? $exif : '');
        
        // Legacy stuff
        // TODO: Move this off to either ACL or some other config. Most if not all is controller specific
        $view->edit_duration_sec = \Entity\UploadComment::getEditDuration();
        $view->STATIC_ASSET_MODIFICATION_DATE = self::STATIC_ASSET_MODIFICATION_DATE; // Assuming this is for versioning.
    }
    
    protected static function _getValueByKeys($array, $keys) {
        return array_reduce($keys, function($x, $key) { return $x[$key]; }, $array);
    }
    
    /**
     * Maps an array using keys provided.
     *
     * The optional prefix parameter is when you want to make the key a string to allow for proper merging.
     *
     * @param $keys         Array of keys
     * @param $value        Value to insert
     * @param $prefix       Default: ''
     * @return array
     */
    protected static function _mapArray($keys, $value, $prefix = '') {
        $array = array();
        $key = $prefix . array_shift($keys);
        
        $array[$key] = (count($keys) > 0 ? self::_mapArray($keys, $value) : $value);
        
        return $array;
    }
}