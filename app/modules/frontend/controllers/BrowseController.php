<?php
namespace Modules\Frontend\Controllers;

use Entity\Upload;

class BrowseController extends BaseController
{
    public function indexAction()
    {
        // TODO: Use variable for this setting.
        $per_page = $this->user->getVariable('perpage');

        $form_config = $this->current_module_config->forms->browse->toArray();

        if (!$this->fa->canSeeArt('mature'))
        {
            unset($form_config['elements']['rating'][1]['options'][Upload::RATING_MATURE]);
            unset($form_config['elements']['rating'][1]['options'][Upload::RATING_ADULT]);
        }
        elseif (!$this->fa->canSeeArt('adult'))
        {
            unset($form_config['elements']['rating'][1]['options'][Upload::RATING_ADULT]);
        }

        $form = new \App\Form($form_config);

        if ($this->request->isPost() && $form->isValid($_POST))
            $filters = $form->getValues();
        else
            $filters = $form->getDefaults();

        // Force form to default to page 1, so that if filters change, the page will reset.
        $form->populate(array('page' => 1));
        $this->view->form = $form;

        // Create query builder.
        $qb = $this->em->createQueryBuilder();
        $qb->select('up')->from('Entity\Upload up');

        if (!empty($filters['category']) && $filters['category'] != 1)
        {
            $qb->andWhere('up.category = :category')
                ->setParameter('category', $filters['category']);
        }

        if (!empty($filters['theme']) && $filters['theme'] != 1)
        {
            $qb->andWhere('up.theme = :theme')
                ->setParameter('theme', $filters['theme']);
        }

        if (!empty($filters['species']) && $filters['species'] != 1)
        {
            $qb->andWhere('up.species = :species')
                ->setParameter('species', $filters['species']);
        }

        if (!empty($filters['gender']) && $filters['gender'] != 0)
        {
            $qb->andWhere('up.gender = :gender')
                ->setParameter('gender', $filters['gender']);
        }

        if (isset($filters['rating']))
        {
            $qb->andWhere('up.rating IN (:ratings)')
                ->setParameter('ratings', (array)$filters['rating']);

            if (in_array(Upload::RATING_MATURE, $filters['rating']) || in_array(Upload::RATING_ADULT, $filters['rating']))
                $this->fa->setPageHasMatureContent(true);
        }

        $query = $qb->orderBy('up.created_at', 'DESC')->getQuery();
        $pager = new \App\Paginator\Doctrine($query, $filters['page'], $per_page);

        $this->view->page_current = $filters['page'];
        $this->view->page_count = $pager->getPageCount();

        $this->view->pager = $pager;
        $this->view->thumbnail_size = $this->user->getVariable('thumbnail_size');

        $this->assets->collection('footer_js')
            ->addJs('//cdnjs.cloudflare.com/ajax/libs/masonry/4.0.0/masonry.pkgd.min.js', false)
            ->addJs('js/gallery.js');
    }
}