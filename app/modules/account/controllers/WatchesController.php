<?php
namespace Modules\Account\Controllers;

use Entity\Watch;
use Entity\User;

class WatchesController extends BaseController
{
    public function indexAction()
    {
        $this->app->readOnly();

        $per_page = 64;
        $page = $this->getParam('page', 1);

        $query = $this->em->createQuery('SELECT w FROM Entity\Watch w WHERE w.user_id = :user_id')
            ->setParameter('user_id', $this->user->id);

        $pager = new \App\Paginator\Doctrine($query, $page, $per_page);
        $this->view->pager = $pager;
    }
}