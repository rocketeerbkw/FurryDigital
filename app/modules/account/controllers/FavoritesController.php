<?php
namespace Modules\Account\Controllers;

use \Entity\Favorite;

class FavoritesController extends BaseController
{
    public function indexAction()
    {
        $perpage = 48;
        $page = $this->getParam('page', 1);

        $query = $this->em->createQuery('SELECT f, up FROM Entity\Favorite f JOIN f.upload up WHERE f.user_id = :user_id ORDER BY f.id DESC')
            ->setParameter('user_id', $this->user->id);

        $pager = new \FA\Paginator\Doctrine($query, $page, $perpage);

        // Determine if any content is adult.
        foreach($pager as $row)
        {
            if ($row->adultsubmission != 0)
                $this->fa->setPageHasMatureContent(true);
        }

        $this->view->pager = $pager;
    }

    public function deleteAction()
    {
        $this->fa->readOnly();

        $id = (int)$this->getParam('id');
        $record = Favorite::getRepository()->findOneBy(array('id' => $id, 'user_id' => $this->user->id));

        if ($record instanceof Favorite)
            $record->delete();

        $this->alert('<b>Favorite removed!</b>', 'green');
        return $this->redirectFromHere(array('action' => 'index', 'id' => NULL));
    }

    /* (TODO: Potentially unused "clear all" function)
    public function cleanupAction()
    {
        $q= 'DELETE f '.
            'FROM   favorites AS f LEFT JOIN submissions AS s ON f.submission_id=s.rowid '.
            'WHERE  f.user_id='.$sql->qstr($_USER['userid']).' AND s.rowid IS NULL';
        $sql->query($q);

        header('Location: /controls/favorites/');
        exit();
    }
    */
}