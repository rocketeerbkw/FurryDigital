<?php
namespace Modules\Account\Controllers;

use Entity\User;
use Entity\Folder;
use Entity\FolderGroup;

class FoldersController extends BaseController
{
    protected $max_folders = 50;
    protected $max_groups = 20;

    public function indexAction()
    {
        // Fetch list of groups and their folders.
        $groups = $this->em->createQuery('SELECT fg, f FROM Entity\FolderGroup fg LEFT JOIN fg.folders f WHERE fg.user_id = :user_id ORDER BY fg.sort_order ASC, f.sort_order ASC')
            ->setParameter('user_id', $this->user->id)
            ->execute();

        $this->view->groups = $groups;

        $group_select = array('' => 'Select...');
        foreach($groups as $group)
            $group_select[$group->id] = $group->name;

        $this->view->group_select = $group_select;

        // Fetch list of unattended folders.
        $folders = $this->em->createQuery('SELECT f FROM Entity\Folder f WHERE f.user_id = :user_id AND f.group_id IS NULL ORDER BY f.sort_order ASC')
            ->setParameter('user_id', $this->user->id)
            ->execute();

        $this->view->folders = $folders;

        $this->view->max_folders = $this->max_folders;
        $this->view->max_groups = $this->max_groups;
    }

    /*
     * Folders
     */

    public function editfolderAction()
    {
        $this->fa->readOnly();

        $form_config = $this->current_module_config->forms->folder->toArray();
        $form_config['elements']['group_id'][1]['options'] = FolderGroup::fetchSelect($this->user->id, 'No Group');

        $form = new \FA\Form($form_config);

        $id = (int)$this->getParam('id');
        if ($id)
        {
            $record = Folder::getRepository()->findOneBy(array('id' => $id, 'user_id' => $this->user->id));

            if (!($record instanceof Folder))
                throw new \FA\Exception('Folder ID not found!');

            $form->populate($record->toArray());
        }

        if ($this->request->isPost() && $form->isValid($_POST))
        {
            $data = $form->getValues();

            if (!($record instanceof Folder))
            {
                // TODO: Enforce folder count limit.

                $record = new Folder;
                $record->user = $this->user;
                $record->sort_order = 100;
            }

            $record->fromArray($data);
            $record->save();

            $this->_reSort();

            $this->alert('<b>Folder updated!</b>', 'green');
            return $this->redirectFromHere(array('action' => 'index', 'id' => NULL));
        }

        $this->view->form = $form;
    }

    public function deletefolderAction()
    {
        $this->fa->readOnly();

        $id = (int)$this->getParam('id');
        $record = Folder::getRepository()->findOneBy(array('id' => $id, 'user_id' => $this->user->id));

        if (!($record instanceof Folder))
            throw new \FA\Exception('Folder ID not found!');

        $record->delete();

        $this->alert('<b>Folder deleted!</b>', 'green');
        return $this->redirectFromHere(array('action' => 'index', 'id' => NULL));
    }

    public function movefolderAction()
    {
        $this->fa->readOnly();

        // Trigger a manual folder re-sort before selecting the record.
        $this->_reSort();

        $id = (int)$this->getParam('id');
        $record = Folder::getRepository()->findOneBy(array('id' => $id, 'user_id' => $this->user->id));

        if (!($record instanceof Folder))
            throw new \FA\Exception('Folder ID not found!');

        $this->_moveEntity($record, $this->getParam('direction'));

        $this->alert('<b>Folder moved!</b>', 'green');
        return $this->redirectFromHere(array('action' => 'index', 'id' => NULL, 'direction' => NULL));
    }

    /*
     * Groups
     */

    public function editgroupAction()
    {
        $this->fa->readOnly();

        $form_config = $this->current_module_config->forms->foldergroup->toArray();
        $form = new \FA\Form($form_config);

        $id = (int)$this->getParam('id');
        if ($id)
        {
            $record = FolderGroup::getRepository()->findOneBy(array('id' => $id, 'user_id' => $this->user->id));

            if (!($record instanceof FolderGroup))
                throw new \FA\Exception('Folder ID not found!');

            $form->populate($record->toArray());
        }

        if ($this->request->isPost() && $form->isValid($_POST))
        {
            $data = $form->getValues();

            if (!($record instanceof FolderGroup))
            {
                // TODO: Enforce folder count limit.

                $record = new FolderGroup;
                $record->user = $this->user;
                $record->sort_order = 100;
            }

            $record->fromArray($data);
            $record->save();

            $this->_reSort();

            $this->alert('<b>Group updated!</b>', 'green');
            return $this->redirectFromHere(array('action' => 'index', 'id' => NULL));
        }

        $this->view->form = $form;
    }

    public function deletegroupAction()
    {
        $this->fa->readOnly();

        $id = (int)$this->getParam('id');
        $record = FolderGroup::getRepository()->findOneBy(array('id' => $id, 'user_id' => $this->user->id));

        if (!($record instanceof FolderGroup))
            throw new \FA\Exception('Folder ID not found!');

        $record->delete();

        $this->alert('<b>Folder group deleted!</b>', 'green');
        return $this->redirectFromHere(array('action' => 'index', 'id' => NULL));
    }

    public function movegroupAction()
    {
        $this->fa->readOnly();

        // Trigger a manual folder re-sort before selecting the record.
        $this->_reSort();

        $id = (int)$this->getParam('id');
        $record = FolderGroup::getRepository()->findOneBy(array('id' => $id, 'user_id' => $this->user->id));

        if (!($record instanceof FolderGroup))
            throw new \FA\Exception('Folder ID not found!');

        $this->_moveEntity($record, $this->getParam('direction'));

        $this->alert('<b>Folder group moved!</b>', 'green');
        return $this->redirectFromHere(array('action' => 'index', 'id' => NULL, 'direction' => NULL));
    }

    /*
     * Internal Functions
     */

    /**
     * Move a specified entity's sort order up or down one position.
     *
     * @param $entity
     * @param $direction
     * @throws \FA\Exception
     */
    protected function _moveEntity($entity, $direction)
    {
        $old_position = $entity->sort_order;

        if ($direction == 'up')
            $new_position = $entity->sort_order-1;
        elseif ($direction == 'down')
            $new_position = $entity->sort_order+1;
        else
            throw new \FA\Exception('Invalid direction specified!');

        // Update any record currently in the new position.
        $entity_class = get_class($entity);
        $update_other_entities = $this->em->createQuery('UPDATE '.$entity_class.' e SET e.sort_order = :old_position WHERE e.sort_order = :new_position AND e.user_id = :user_id')
            ->setParameter('old_position', $old_position)
            ->setParameter('new_position', $new_position)
            ->setParameter('user_id', $this->user->id)
            ->execute();

        // Update the entity being moved.
        $entity->sort_order = $new_position;
        $entity->save();
    }

    /**
     * Rearranges all folders and groups to have sort orders starting at zero.
     */
    protected function _reSort()
    {
        $entities_to_resort = array('Entity\Folder', 'Entity\FolderGroup');

        foreach($entities_to_resort as $entity_class)
        {
            if ($entity_class == 'Entity\Folder')
                $query_string = 'SELECT e.id FROM '.$entity_class.' e WHERE e.user_id = :user_id ORDER BY e.group_id ASC, e.sort_order ASC, e.name ASC';
            else
                $query_string = 'SELECT e.id FROM '.$entity_class.' e WHERE e.user_id = :user_id ORDER BY e.sort_order ASC, e.name ASC';

            $existing_records = $this->em->createQuery($query_string)
                ->setParameter('user_id', $this->user->id)
                ->getArrayResult();

            $update_position_q = $this->em->createQuery('UPDATE '.$entity_class.' e SET e.sort_order = :position WHERE e.id = :id');

            $i = 0;
            foreach($existing_records as $record)
            {
                $update_position_q->setParameter('position', $i)
                    ->setParameter('id', $record['id'])
                    ->execute();

                $i++;
            }
        }
    }

}