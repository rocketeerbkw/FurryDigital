<?php
namespace Modules\Account\Controllers;

use Entity\User;

use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Imagick\Imagine;

class AvatarController extends BaseController
{
    protected $_avatars;
    protected $_gravatar;

    public function preDispatch()
    {
        parent::preDispatch();

        // Loop through art directory for filesystem avatars.
        $art_base = $this->user->lower.'/avatars';
        $art_dir = $this->config->application->art_path.'/'.$art_base;
        $art_url = $this->config->application->art_url.'/'.$art_base;

        $this->_avatars = array();

        foreach(scandir($art_dir) as $avatar_result)
        {
            if (in_array($avatar_result, array('.', '..')))
                continue;

            $avatar_id = md5($avatar_result);
            $this->_avatars[$avatar_id] = array(
                'path'      => $art_dir.'/'.$avatar_result,
                'url'       => $art_url.'/'.$avatar_result,
            );
        }

        // Build current gravatar URL.
        $user_email = $this->user->email;

        $gravatar_params = array(
            's'     => '100',       // Size in px (1-2048)
            'd'     => 'mm',        // Default avatar (404 | mm | identicon | monsterid | wavatar)
            'r'     => 'g',         // Rating to use (g | pg | r | x)
        );
        $this->_gravatar = 'https://secure.gravatar.com/avatar/'.md5(strtolower(trim($user_email))).'?'.http_build_query($gravatar_params);
    }

    public function indexAction()
    {
        $this->fa->readOnly();
        $this->fa->fileReadOnly();

        $form = new \FA\Form($this->current_module_config->forms->avatar);

        // Handle new avatar upload.
        if ($_POST && $this->request->hasFiles() && $form->isValid(array_merge($_POST, $_FILES)))
        {
            $files = $form->getFiles($this->request);
            $file = $files['avatar'][0];

            if ($file->isUploadedFile())
            {
                $source_path = $file->getTempName();
                $art_dir = $this->config->application->art_path;

                // Clean up original filename for destination filename.
                $source_info = pathinfo($file->getName());
                $dest_filename = preg_replace("/[^A-Za-z0-9_]/", '', substr($source_info['filename'], 0, 30)).'.gif';

                $dest_path = $art_dir.'/'.$this->user->lower.'/avatars/'.$dest_filename;

                // Create resized image for avatar.
                $imagine = new Imagine;
                $size    = new Box(100, 100);
                $mode    = ImageInterface::THUMBNAIL_INSET;

                $imagine->open($source_path)
                    ->thumbnail($size, $mode)
                    ->save($dest_path);

                // Set as default avatar also.
                $this->user->setAvatar($dest_path);

                $this->alert('<b>New avatar uploaded!</b><br>The avatar has been set as your default.', 'green');
                return $this->redirectHere();
            }
        }

        $this->view->form = $form;

        $this->view->gravatar = $this->_gravatar;
        $this->view->avatars = $this->_avatars;
    }

    public function gravatarAction()
    {
        $source_path = FA_INCLUDE_TEMP.'/'.$this->user->lower.'_gravatar.png';

        if(!@copy($this->_gravatar, $source_path))
            throw new \FA\Exception('Copy error: '.error_get_last());

        $art_dir = $this->config->application->art_path;

        // Clean up original filename for destination filename.
        $dest_path = $art_dir.'/'.$this->user->lower.'/avatars/gravatar.gif';

        // Create resized image for avatar.
        $imagine = new Imagine;
        $size    = new Box(100, 100);
        $mode    = ImageInterface::THUMBNAIL_INSET;

        $imagine->open($source_path)
            ->thumbnail($size, $mode)
            ->save($dest_path);

        $this->user->setAvatar($dest_path);

        $this->alert('<b>Gravatar loaded!</b><br>Your Gravatar has been added to your avatar collection and set as your default.', 'green');
        return $this->redirectFromHere(array('action' => 'index'));
    }

    public function chooseAction()
    {
        $id = $this->getParam('id');

        if (empty($id))
            throw new \FA\Exception('Avatar ID not specified.');

        if (isset($this->_avatars[$id]))
        {
            $avatar_path = $this->_avatars[$id]['path'];
            $this->user->setAvatar($avatar_path);

            $this->alert('<b>Avatar changed!</b><br>Your new avatar may take a few minutes to update across the site.', 'green');
        }

        return $this->redirectFromHere(array('action' => 'index', 'id' => NULL));
    }

    public function deleteAction()
    {
        $id = $this->getParam('id');

        if (empty($id))
            throw new \FA\Exception('Avatar ID not specified.');

        if (isset($this->_avatars[$id]))
        {
            $avatar_path = $this->_avatars[$id]['path'];
            @unlink($avatar_path);

            $this->alert('<b>Avatar deleted.</b>', 'green');
        }

        return $this->redirectFromHere(array('action' => 'index', 'id' => NULL));
    }
}