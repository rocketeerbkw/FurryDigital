<?php
namespace Modules\Profile\Controllers;

use Entity\Journal;

class JournalsController extends BaseController
{
    /**
     * Display all journals.
     */
    public function indexAction()
    {
        $this->_getUser();
        $this->_logPageView();
        $this->_enforceNoGuests();

        $perpage = $this->user->getVariable('perpage');

        $query = $this->em->createQuery('SELECT j FROM Entity\Journal j WHERE j.user_id = :user_id ORDER BY j.id DESC')
            ->setParameter('user_id', $this->owner->id);

        $pager = new \FA\Paginator\Doctrine($query, $this->getParam('page', 1), $perpage);
        $this->view->pager = $pager;
    }

    public function viewAction()
    {
        $id = (int)$this->getParam('id');
        $record = Journal::find($id);

        if (!($record instanceof Journal))
            throw new \FA\Exception('Journal not found!');

        $this->view->journal = $record;

        $this->_getUser($record->user);
        $this->_logPageView();
        $this->_enforceNoGuests();

        // TODO: Rewrite
    }

    public function replyAction()
    {
    }

    public function replytoAction()
    {
    }

    /**
     * Hide comment feature for users
     */
    public function hidecommentAction()
    {
    }

    /**
     * Restore hidden comment feature for admins
     */
    public function unhidecommentAction()
    {
    }

    public function editcommentAction()
    {
    }
}