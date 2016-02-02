<?php
namespace Modules\Account\Controllers;

use Entity\Journal;

class JournalsController extends BaseController
{
    public function indexAction()
    {
        $query = $this->em->createQuery('SELECT j FROM Entity\Journal j WHERE j.user_id = :uid')
            ->setParameter('uid', $this->user->id);

        $perpage = 25;
        $pager = new \FA\Paginator\Doctrine($query, $this->getParam('page', 1), $perpage);

        $this->view->pager = $pager;
        $this->view->featured_journal = $this->user->getVariable('featured_journal_id');
    }

    // Create or update journal
    public function editAction()
    {
        $this->fa->readOnly();

        $form_config = $this->current_module_config->forms->journal->toArray();
        $form = new \FA\Form($form_config);

        $record = null;
        $edit_mode = false;

        $id = (int)$this->getParam('id');
        if ($id != 0)
        {
            $record = Journal::getRepository()->findOneBy(array('id' => $id, 'user_id' => $this->user->id));
            if (!($record instanceof Journal))
                throw new \FA\Exception('Journal entry not found!');

            $record_info = $record->toArray(FALSE, TRUE);

            if ($this->user->getVariable('featured_journal_id') == $id)
                $record_info['is_featured'] = 1;

            $form->populate($record_info);

            $edit_mode = true;
        }

        if ($this->request->isPost() && $form->isValid($_POST))
        {
            if (!($record instanceof Journal))
            {
                $record = new Journal;
                $record->user = $this->user;
            }

            $data = $form->getValues();

            if ($data['is_featured'])
                $this->user->setVariable('featured_journal_id', $id);
            elseif ($this->user->getVariable('featured_journal_id') == $id)
                $this->user->deleteVariable('featured_journal_id');

            $record->fromArray($data);
            $record->save();

            if ($edit_mode)
                $this->alert('<b>Journal edited!</b>', 'green');
            else
                $this->alerT('<b>New journal posted!</b>', 'green');

            return $this->redirectFromHere(array('action' => 'index', 'id' => NULL));
        }

        $this->view->edit_mode = $edit_mode;
        $this->view->form = $form;
    }

    public function deleteAction()
    {
        $this->fa->readOnly();

        $id = (int)$this->getParam('id');
        $record = Journal::getRepository()->findOneBy(array('id' => $id, 'user_id' => $this->user->id));
        if (!($record instanceof Journal))
            throw new \FA\Exception('Journal entry not found!');

        $record->delete();

        $this->alert('<b>Journal deleted!</b>', 'green');
        return $this->redirectFromHere(array('action' => 'index', 'id' => NULL));
    }
}