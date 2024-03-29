<?php
namespace Modules\Profile\Controllers;

use Entity\Folder;
use Entity\Upload;

class GalleryController extends BaseController
{
    protected $scraps_mode = 0;

    public function preDispatch()
    {
        $this->_getUser();
        $this->_logPageView();
        $this->_enforceNoGuests();

        parent::preDispatch();
    }

    public function indexAction()
    {
        $this->view->scraps_mode = $this->scraps_mode;

        $rating_query = '(true = true)';
        $rating_cache = '';

        if ($this->app->canSeeArt('adult'))
        {
            $rating_cache = 'gma';
        }
        elseif ($this->app->canSeeArt('mature'))
        {
            $rating_query = '(up.rating = '.Upload::RATING_GENERAL.' OR up.rating = '.Upload::RATING_MATURE.')';
            $rating_cache = 'gm-';
        }
        else
        {
            $rating_query = '(up.rating = '.Upload::RATING_GENERAL.')';
            $rating_cache = 'g--';
        }

        $folder_id = (int)$this->getParam('folder');

        if ($folder_id)
        {
            $folder = Folder::getRepository()->findOneBy(array('user_id' => $this->owner->id, 'id' => $folder_id));

            if (!($folder instanceof Folder))
                throw new \App\Exception('Folder not found!');

            $this->view->folder = $folder;

            $query = $this->em->createQuery('SELECT up FROM Entity\Upload up WHERE up.user_id = :user_id AND '.$rating_query.' AND up.id IN (SELECT uf.upload_id FROM Entity\UploadFolder uf WHERE uf.folder_id = :folder_id) ORDER BY up.id DESC')
                ->setParameter('user_id', $this->owner->id)
                ->setParameter('folder_id', $folder_id);
        }
        else
        {
            $query = $this->em->createQuery('SELECT up FROM Entity\Upload up WHERE up.user_id = :user_id AND '.$rating_query.' AND up.is_scrap = :is_scrap ORDER BY up.id DESC')
                ->setParameter('user_id', $this->owner->id)
                ->setParameter('is_scrap', $this->scraps_mode);
        }

        $perpage = $this->user->getVariable('perpage');
        $pager = new \App\Paginator\Doctrine($query, $this->getParam('page', 1), $perpage);

        if (count($pager) > 0)
        {
            foreach($pager as $row)
            {
                if ($row->rating == Upload::RATING_ADULT)
                    $this->app->setPageHasMatureContent();
            }
        }

        $this->view->pager = $pager;

        // Folder List
        $this->view->folder_list = Folder::fetchSelectWithGroups($this->owner->id, FALSE);

        // Extra assets to display galleries
        $this->assets->collection('footer_js')
            ->addJs('//cdnjs.cloudflare.com/ajax/libs/masonry/4.0.0/masonry.pkgd.min.js', false)
            ->addJs('js/gallery.js');
    }

    public function scrapsAction()
    {
        $this->scraps_mode = 1;
        return $this->dispatcher->forward(array('action' => 'index'));
    }
}
