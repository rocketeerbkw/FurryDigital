<?php
namespace Modules\Account\Controllers;

use Entity\User;

class ShoutsController extends BaseController
{
    public function indexAction()
    {
        $shouts = $this->em->createQuery('SELECT s, us FROM Entity\Shout s JOIN s.sender us WHERE s.recipient_id = :user_id ORDER BY s.id DESC')
            ->setParameter('user_id', $this->user->id)
            ->execute();

        $this->view->shouts = $shouts;
    }

    public function deleteAction()
    {
        $this->fa->readOnly();

        if (empty($_POST['shouts']))
            throw new \FA\Exception('No shouts specified to delete!');

        $shout_ids = array_filter($_POST['shouts']);

        // Must delete using the longer way (not DELETE FROM) to trigger proper notifications.
        $shouts = $this->em->createQuery('SELECT s FROM Entity\Shout s WHERE s.id IN (:shout_ids) AND s.recipient_id = :user_id')
            ->setParameter('shout_ids', $shout_ids)
            ->setParameter('user_id', $this->user->id)
            ->execute();

        foreach($shouts as $record)
        {
            $this->em->remove($record);
        }

        $this->em->flush();

        $this->alert('<b>Shout(s) deleted!</b>', 'green');
        return $this->redirectFromHere(array('action' => 'index', 'shouts' => NULL));
    }
}