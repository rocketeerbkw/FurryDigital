<?php
namespace Modules\Account\Controllers;

use Entity\User;
use Entity\Upload;
use Entity\UploadFolder;
use Entity\Folder;
use Entity\FolderGroup;

use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Imagick\Imagine;

class UploadsController extends BaseController
{
    const MAX_THUMB_SIZE = 120;
    const MAX_PREVIEW_SIZE = 300;

    public function indexAction()
    {
        // Handle an action if submitted.
        if ($this->request->hasPost('action'))
        {
            $this->dispatcher->forward(array('action' => $this->request->getPost('action')));
            return false;
        }

        // Fetch a list of folders to assign submissions to.
        $this->view->folders = \Entity\Folder::fetchSelectWithGroups($this->user->id, TRUE);

        // Generate query and paginate.
        $query = $this->em->createQuery('SELECT s FROM Entity\Upload s WHERE s.user_id = :user_id ORDER BY s.id DESC')
            ->setParameter('user_id', $this->user->id);

        $per_page = 64;
        $pager = new \FA\Paginator\Doctrine($query, $this->getParam('page', 1), $per_page);

        // Determine if any content is adult.
        foreach($pager as $row)
        {
            if ($row->adultsubmission != 0)
                $this->fa->setPageHasMatureContent(true);
        }

        $this->view->pager = $pager;
    }

    public function editAction()
    {
        $types = $this->config->fa->upload_types->toArray();
        $id = (int)$this->getParam('id');

        if ($id !== 0)
        {
            // Edit existing record.
            // TODO: Reimplement administrator access.
            $record = Upload::getRepository()->findOneBy(array('id' => $id, 'user_id' => $this->user->id));

            if (!($record instanceof Upload))
                throw new \FA\Exception('Submission ID not found!');

            $edit_mode = true;
            $type = $record->submission_type;
        }
        else
        {
            // Create new submission.
            $type = $this->getParam('type');

            // Show type selector if no type is specified.
            if (empty($type) || !isset($types[$type]))
            {
                $this->view->types = $types;
                return $this->view->pick('uploads/select');
            }

            $edit_mode = false;
            $record = NULL;
        }

        $type_info = $types[$type];
        $form_config = $this->current_module_config->forms->uploads_edit->toArray();

        // Create mode changes
        if (!$edit_mode)
        {
            $form_config['groups']['files']['elements']['submission'][1]['required'] = true;

            unset($form_config['groups']['files']['elements']['submission'][1]['description']);
            unset($form_config['groups']['files']['elements']['rebuild_thumbnail']);
        }

        // Changes to the form based on submission type.
        if (isset($type_info['category']))
            $form_config['groups']['metadata']['elements']['category'][1]['default'] = $type_info['category'];

        if ($type !== Upload::TYPE_IMAGE)
            unset($form_config['groups']['files']['elements']['rebuild_thumbnail']);

        $form_config['groups']['files']['elements']['submission'][1]['allowedTypes'] = $type_info['types'];

        // Create the form class.
        $form = new \FA\Form($form_config);

        // Populate the form (if available).
        if ($record instanceof Upload)
        {
            $form->setDefaults($record->toArray(TRUE, TRUE));
        }
        
        // Handle form submission.
        if ($_POST && $this->request->hasFiles() && $form->isValid(array_merge($_POST, $_FILES)))
        {
            $data = $form->getValues();

            if (!($record instanceof Upload))
            {
                $record = new Upload();
                $record->upload_type = $type;
                $record->user = $this->user;
                $record->is_hidden = true; // Hide until properly populated.
                $record->save(); // Immediately save to generate IDs used in next steps.
            }

            $record->fromArray($data);

            // Begin file handling.
            \IMagick::setResourceLimit(\Imagick::RESOURCETYPE_MEMORY, 32);
            \IMagick::setResourceLimit(\Imagick::RESOURCETYPE_MAP, 32);

            $files = $form->getFiles($this->request);
            $imagine = new Imagine;

            $submission_file = $files['submission'][0];

            $thumbnail_file = null;
            $thumbnail_paths = array();
            
            $preview_file = null;
            $preview_paths = array();

            if ($submission_file)
            {
                $submission_paths = $record->generatePaths($submission_file->getName());

                // Create the proper artwork directory if it doesn't exist.
                $submission_dir = dirname($submission_paths['full']['path']);
                @mkdir($submission_dir);

                if ($type == Upload::TYPE_IMAGE)
                {
                    // Handle image uploads.
                    $submission_image = $imagine->open($submission_file->getTempName());
                    $is_animated = count($submission_image->layers()) > 1;
                    
                    // So, it seems Imagine really loves to screw up GIFs, so lets avoid that
                    if ($is_animated) {
                        $dest_path = $submission_paths['full']['path'];
                        
                        // Copying this instead of moving due to the file being reused by preview/thumbnail
                        copy($submission_file->getTempName(), $dest_path);
                    }
                    else
                        $submission_image->save($submission_paths['full']['path'], array('animated' => $is_animated));

                    // Make this file the thumbnail if no other is specified.
                    if (empty($files['thumbnail']) && (!$edit_mode || $data['rebuild_thumbnail']))
                    {
                        $thumbnail_file = $submission_file;
                        $thumbnail_paths = $submission_paths;
                    }
                    
                    // Set up the preview parameters
                    $preview_file = $submission_file;
                    $preview_paths = $submission_paths;
                }
                else
                {
                    // Handle non-images. Way simpler, right?
                    $dest_path = $submission_paths['full']['path'];
                    $submission_file->moveTo($dest_path);

                    // Prevent the file from being deleted below.
                    $submission_file = null;
                }

                $record->setFull($submission_paths['full']['base']);
            }

            // Use the thumbnail field if supplied.
            if (!empty($files['thumbnail']))
            {
                $thumbnail_file = $files['thumbnail'][0];
                $thumbnail_paths = $record->generatePaths($thumbnail_file->getName());
            }
            
            // If we haven't set a preview image/path, then use the thumbnail if possible
            if (is_null($preview_file))
            {
                $preview_file = $thumbnail_file;
                $preview_paths = $thumbnail_paths;
            }
            
            // Process either the uploaded thumbnail, or resize the original file to be our preview.
            if ($preview_file)
            {
                // Generate "small" size thumbnail.
                $preview_size = new Box(self::MAX_PREVIEW_SIZE, self::MAX_PREVIEW_SIZE);
                
                self::_saveAsJPG($imagine, $preview_file, $preview_paths['small']['path'], 90, $preview_size);

                $record->setSmall(self::_changeExtension($preview_paths['small']['base'], 'jpg'));
            }

            // Process either the uploaded thumbnail, or thumbnailize the original file.
            if ($thumbnail_file)
            {
                // Generate "thumb" size thumbnail.
                $thumbnail_size = new Box(self::MAX_THUMB_SIZE, self::MAX_THUMB_SIZE);
                
                self::_saveAsJPG($imagine, $thumbnail_file, $thumbnail_paths['thumbnail']['path'], 90, $thumbnail_size);
                
                $record->setThumbnail(self::_changeExtension($thumbnail_paths['thumbnail']['base'], 'jpg'));
            }

            // Delete the temp files (if not already moved).
            if ($submission_file)
                @unlink($submission_file->getTempName());

            if ($thumbnail_file)
                @unlink($thumbnail_file->getTempName());

            // Unhide the record that was hidden earlier.
            if (!$edit_mode)
                $record->is_hidden = false;

            $record->save();

            $view_url = $this->url->get('view/'.$record->id);
            $view_link = 'You can <a href="'.$view_url.'" target="_blank_">view your submission\'s public page here</a>.';

            if ($edit_mode)
                $this->alert('<b>Submission Updated!</b><br>'.$view_link, 'green');
            else
                $this->alert('<b>New Submission Uploaded!</b><br>'.$view_link, 'green');

            return $this->redirectFromHere(array('action' => 'index', 'id' => NULL, 'type' => NULL));
        }

        // Render the main form.
        $this->view->type_info = $type_info;
        $this->view->form = $form;
    }

    /**
     * Assign submission(s) to the specified folder Id.
     *
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
     * @throws \FA\Exception
     */
    public function assignfolderAction()
    {
        $submissions = $this->_initBulkAction();

        // Verify that folder ID is owned by user and valid.
        $folder_id = (int)$this->getParam('folder_id');
        $folder = Folder::getRepository()->findOneBy(array('id' => $folder_id, 'user_id' => $this->user->id));

        if (!($folder instanceof Folder))
            throw new \FA\Exception('Folder not found!');

        foreach($submissions as $sub)
        {
            $record = UploadFolder::getRepository()->findOneBy(array('folder_id' => $folder_id, 'upload_id' => $sub->id));

            if (!($record instanceof UploadFolder))
            {
                $record = new UploadFolder;
                $record->upload = $sub;
                $record->folder = $folder;

                $this->em->persist($record);
            }
        }

        $this->em->flush();

        $this->alert('<b>Submissions assigned to folder!</b>', 'green');
        return $this->redirectFromHere(array('action' => 'index', 'ids' => NULL));
    }

    /**
     * Assign submission(s) to a newly created folder.
     *
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
     * @throws \FA\Exception
     */
    public function createfolderAction()
    {
        $submissions = $this->_initBulkAction();

        $folder_name = trim($this->getParam('new_folder_name'));
        if (empty($folder_name))
            throw new \FA\Exception('No folder name provided!');

        $folder = new Folder;
        $folder->user = $this->user;
        $folder->name = $folder_name;
        $folder->save();

        foreach($submissions as $submission)
        {
            $subfolder = new UploadFolder;
            $subfolder->upload = $submission;
            $subfolder->folder = $folder;

            $this->em->persist($subfolder);
        }

        $this->em->flush();

        $this->alert('<b>Submissions added to a newly created folder!</b>', 'green');
        return $this->redirectFromHere(array('action' => 'index', 'ids' => NULL));
    }

    /**
     * Remove all folders from submission(s).
     *
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
     * @throws \FA\Exception
     */
    public function removefolderAction()
    {
        $submissions = $this->_initBulkAction();

        foreach($submissions as $sub)
        {
            if ($sub->folders->count())
            {
                foreach($sub->folders as $folder)
                    $this->em->remove($folder);
            }
        }

        $this->em->flush();

        $this->alert('<b>Submissions removed from all folders!</b>', 'green');
        return $this->redirectFromHere(array('action' => 'index', 'ids' => NULL));
    }

    /**
     * Move submission(s) to Scraps folder.
     *
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
     * @throws \FA\Exception
     */
    public function movetoscrapsAction()
    {
        $submissions = $this->_initBulkAction();

        foreach($submissions as $record)
        {
            $record->is_scrap = true;
            $this->em->persist($record);
        }

        $this->em->flush();

        $this->alert('<b>Submissions moved to the scraps section!</b>', 'green');
        return $this->redirectFromHere(array('action' => 'index', 'ids' => NULL));
    }

    /**
     * Move submission(s) from Scraps folder.
     *
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
     * @throws \FA\Exception
     */
    public function movefromscrapsAction()
    {
        $submissions = $this->_initBulkAction();

        foreach($submissions as $record)
        {
            $record->is_scrap = false;
            $this->em->persist($record);
        }

        $this->em->flush();

        $this->alert('<b>Submissions moved from the scraps section!</b>', 'green');
        return $this->redirectFromHere(array('action' => 'index', 'ids' => NULL));
    }

    /**
     * Delete submission(s).
     *
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
     * @throws \FA\Exception
     */
    public function deleteAction()
    {
        $this->fa->fileReadOnly();

        $submissions = $this->_initBulkAction();

        foreach($submissions as $sub)
        {
            $sub->delete();
        }

        $this->alert('<b>Submissions deleted!</b>', 'green');
        return $this->redirectFromHere(array('action' => 'index', 'ids' => NULL));
    }

    /**
     * Initialize a bulk action by validating submission IDs.
     *
     * @return \Entity\Upload[] An array of submissions.
     * @throws \FA\Exception
     */
    protected function _initBulkAction()
    {
        $this->fa->readOnly();

        if (!$this->request->hasPost('ids'))
            throw new \FA\Exception('No Upload IDs specified!');

        $csrf = $this->getParam('csrf');
        if (!$this->csrf->verify($csrf, 'uploads'))
            throw new \FA\Exception('Form validation code was not valid.');

        $ids_raw = (array)$this->request->getPost('ids');

        $submissions = $this->em->createQuery('SELECT u
            FROM Entity\Upload u
            WHERE u.id IN (:ids) AND u.user_id = :user_id')
            ->setParameter('ids', $ids_raw)
            ->setParameter('user_id', $this->user->id)
            ->execute();

        if (count($submissions) == 0)
            throw new \FA\Exception('No valid submissions were found!');

        return $submissions;
    }
    
    /**
     * Helper function for changing file extensions
     *
     * @param $file_path    File path
     * @param $new_ext      Desired extension
     */
    protected static function _changeExtension($file_path, $new_ext)
    {
        // Change the image extension to .jpg
        return substr_replace($file_path, $new_ext, strrpos($file_path, '.') + 1);
    }
    
    /**
     * Saves the image as a jpg.
     *
     * @param $imagine      Imagine object
     * @param $file         Temporary file
     * @param $path         Destination path
     * @param $quality      Jpeg Quality (Default: 90)
     * @param $box_size     Constraint box size
     */
    protected static function _saveAsJPG($imagine, $file, $path, $quality = 90, $box_size = null)
    {
        // Change the image extension to .jpg
        $file_path = self::_changeExtension($path, 'jpg');
        
        // Start processing
        $image = $imagine->open($file->getTempName());
        
        // Grab the first frame in case this is an animation (If not, it just returns the image itself)
        $image = $image->layers()->get(0);
        
        // If a box size is specify, resize the image!
        if ($box_size instanceof Box)
            $image = $image->thumbnail($box_size, ImageInterface::THUMBNAIL_INSET);
        
        $image->save($file_path, array('jpeg_quality' => $quality));
    }
}