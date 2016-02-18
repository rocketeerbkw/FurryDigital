<?php
namespace Modules\Account\Controllers;

use Entity\User;
use Entity\Upload;
use Entity\UploadFolder;
use Entity\Folder;
use Entity\FolderGroup;

use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Image\Point;
use Imagine\Imagick\Imagine;

class UploadsController extends BaseController
{
    const MAX_THUMB_SIZE = 200;
    const MAX_PREVIEW_SIZE = 500;

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
        $pager = new \App\Paginator\Doctrine($query, $this->getParam('page', 1), $per_page);

        // Determine if any content is adult.
        foreach($pager as $row)
        {
            if ($row->adultsubmission != 0)
                $this->app->setPageHasMatureContent(true);
        }

        $this->view->pager = $pager;

        $this->assets->collection('footer_js')
            ->addJs('//cdnjs.cloudflare.com/ajax/libs/masonry/4.0.0/masonry.pkgd.min.js', false)
            ->addJs('js/gallery.js');
    }

    public function editAction()
    {
        $types = $this->config->fa->upload_types->toArray();
        $id = (int)$this->getParam('id');

        if ($id !== 0)
        {
            $record = Upload::find($id);

            if ($record instanceof Upload)
            {
                if ($this->user->id == $record->user_id)
                    $admin_mode = false;
                elseif ($this->acl->isAllowed('manage uploads'))
                    $admin_mode = true;
                else
                    throw new \App\Exception('Upload not found!');
            }
            else
            {
                throw new \App\Exception('Upload not found!');
            }

            $edit_mode = true;
            $type = $record->upload_type;
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
        $form = new \App\Form($form_config);

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
            }

            unset($data['submission'], $data['thumbnail']);
            $record->fromArray($data);

            // Begin file handling.
            \IMagick::setResourceLimit(\Imagick::RESOURCETYPE_MEMORY, 32);
            \IMagick::setResourceLimit(\Imagick::RESOURCETYPE_MAP, 32);

            $s3 = $this->di->get('s3');

            $files = $form->getFiles($this->request);
            $imagine = new Imagine;

            $submission_file = $files['submission'][0];
            $submission_image = null;

            $thumbnail_file = null;
            $thumbnail_paths = array();
            $thumbnail_image = null;
            
            $preview_file = null;
            $preview_paths = array();
            $preview_image = null;

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
                    $submission_image->strip();

                    $is_animated = count($submission_image->layers()) > 1;
                    
                    // So, it seems Imagine really loves to screw up GIFs, so lets avoid that
                    if ($is_animated)
                    {
                        $dest_path = $submission_paths['full']['path'];
                        
                        // Copying this instead of moving due to the file being reused by preview/thumbnail
                        $s3->copy($submission_file->getTempName(), $dest_path);
                    }
                    else
                    {
                        $submission_image->save($submission_paths['full']['temp']);
                        $s3->upload($submission_paths['full']['temp'], $submission_paths['full']['path']);
                    }

                    // Make this file the thumbnail if no other is specified.
                    if (empty($files['thumbnail']) && (!$edit_mode || $data['rebuild_thumbnail']))
                    {
                        $thumbnail_file = $submission_file;
                        $thumbnail_paths = $submission_paths;
                        $thumbnail_image = $submission_image;
                    }
                    
                    // Set up the preview parameters
                    $preview_file = $submission_file;
                    $preview_paths = $submission_paths;
                    $preview_image = $submission_image;
                }
                else
                {
                    // Handle non-images. Way simpler, right?
                    $dest_path = $submission_paths['full']['path'];
                    $s3->upload($submission_file->getTempName(), $dest_path);

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

                $thumbnail_image = $imagine->open($thumbnail_file->getTempName());
                $thumbnail_image->strip();
            }
            
            // If we haven't set a preview image/path, then use the thumbnail if possible
            if (is_null($preview_file) && !is_null($thumbnail_file))
            {
                $preview_file = $thumbnail_file;
                $preview_paths = $thumbnail_paths;
                $preview_image = $thumbnail_image;
            }
            
            // Process either the uploaded thumbnail, or resize the original file to be our preview.
            if ($preview_image)
            {
                // Generate "small" size thumbnail.
                $this->_resizeImage($preview_image, self::MAX_PREVIEW_SIZE);

                $preview_image->save($preview_paths['small']['temp']);
                $s3->upload($preview_paths['small']['temp'], $preview_paths['small']['path']);

                $record->setSmall($preview_paths['small']['base']);
            }

            // Process either the uploaded thumbnail, or thumbnailize the original file.
            if ($thumbnail_image)
            {
                // Generate "thumb" size thumbnail.
                $this->_resizeImage($thumbnail_image, self::MAX_THUMB_SIZE);

                $thumbnail_image->save($thumbnail_paths['thumbnail']['temp']);
                $s3->upload($thumbnail_paths['thumbnail']['temp'], $thumbnail_paths['thumbnail']['path']);

                $record->setThumbnail($thumbnail_paths['thumbnail']['base']);
            }

            // Delete the temp files (if not already moved).
            if ($submission_file)
                @unlink($submission_file->getTempName());

            if ($thumbnail_file)
                @unlink($thumbnail_file->getTempName());

            $record->save();

            if ($edit_mode)
                $this->alert('<b>Submission Updated!</b>', 'green');
            else
                $this->alert('<b>New Submission Uploaded!</b>', 'green');

            return $this->redirectToName('upload_view', ['id' => $record->id]);
        }

        // Render the main form.
        $this->view->type_info = $type_info;
        $this->view->form = $form;
    }

    /**
     * Assign submission(s) to the specified folder Id.
     *
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
     * @throws \App\Exception
     */
    public function assignfolderAction()
    {
        $submissions = $this->_initBulkAction();

        // Verify that folder ID is owned by user and valid.
        $folder_id = (int)$this->getParam('folder_id');
        $folder = Folder::getRepository()->findOneBy(array('id' => $folder_id, 'user_id' => $this->user->id));

        if (!($folder instanceof Folder))
            throw new \App\Exception('Folder not found!');

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
     * @throws \App\Exception
     */
    public function createfolderAction()
    {
        $submissions = $this->_initBulkAction();

        $folder_name = trim($this->getParam('new_folder_name'));
        if (empty($folder_name))
            throw new \App\Exception('No folder name provided!');

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
     * @throws \App\Exception
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
     * @throws \App\Exception
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
     * @throws \App\Exception
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
     * @throws \App\Exception
     */
    public function deleteAction()
    {
        $this->app->fileReadOnly();

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
     * @throws \App\Exception
     */
    protected function _initBulkAction()
    {
        $this->app->readOnly();

        if (!$this->request->hasPost('ids'))
            throw new \App\Exception('No Upload IDs specified!');

        $csrf = $this->getParam('csrf');
        if (!$this->csrf->verify($csrf, 'uploads'))
            throw new \App\Exception('Form validation code was not valid.');

        $ids_raw = (array)$this->request->getPost('ids');

        $submissions = $this->em->createQuery('SELECT u
            FROM Entity\Upload u
            WHERE u.id IN (:ids) AND u.user_id = :user_id')
            ->setParameter('ids', $ids_raw)
            ->setParameter('user_id', $this->user->id)
            ->execute();

        if (count($submissions) == 0)
            throw new \App\Exception('No valid submissions were found!');

        return $submissions;
    }

    /**
     * Handle some special conditions around resizing images.
     *
     * @param ImageInterface $image
     * @param $width
     */
    protected function _resizeImage(\Imagine\Image\ImageInterface $image, $width)
    {
        $image_size = $image->getSize();

        // Resize images wider than the destination width.
        if ($image_size->getWidth() > $width)
        {
            $image->resize($image_size->widen($width));
            $image_size = $image->getSize();
        }

        // Crop images that are "way too vertical".
        $threshold_height = round($width * 1.5);
        if ($image_size->getHeight() > $threshold_height)
        {
            // Crop from the center of the image.
            $x1 = max(0, round($image_size->getWidth() / 2) - round($width / 2));
            $y1 = max(0, round($image_size->getHeight() / 2) - round($threshold_height / 2));

            $image->crop(new Point($x1, $y1), new Box($width, $threshold_height));
        }

        return $image;
    }
}