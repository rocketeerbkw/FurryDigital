<?php
namespace Modules\Account\Controllers;

use Entity\User;
use Entity\Note;

class MessagesController extends BaseController
{
    public function indexAction()
    {
        // Message Center index
    }

    public function othersAction()
    {
        $notifications = $this->user->getNotifications('other');
        $this->view->notifications = $notifications;
    }

    public function uploadsAction()
    {
        $notifications = $this->user->getNotifications('uploads');

        $this->view->notify_key = 'upload';
        $this->view->notify_info = $notifications['upload'];

        $this->assets->collection('footer_js')
            ->addJs('//cdnjs.cloudflare.com/ajax/libs/masonry/4.0.0/masonry.pkgd.min.js', false)
            ->addJs('js/gallery.js');
    }

    public function processAction()
    {
        $notifications = $this->user->getNotifications();

        $this->csrf->requireValid($_POST['csrf'], 'messages');

        $notify_type = $this->getParam('type');

        if (!isset($notifications[$notify_type]))
            throw new \App\Exception('Notification type not found!');

        $notify_info = $notifications[$notify_type];
        $notify_class = '\Entity\\'.$notify_info['class'];

        switch($this->getParam('do'))
        {
            case 'remove_all':
                $notify_class::purgeAllByUser($this->user->id);
                break;

            case 'remove_selected':
                foreach($_POST['ids'] as $id)
                    $notify_class::purgeByIdentifier($this->user->id, $id);
                break;
        }

        $this->user->updateNotificationCount($notify_type);
        $this->user->save();

        $this->alert('<b>Selected notifications cleared!</b>', 'green');
        return $this->response->redirect($notify_info['url']);
    }

    public function pmsAction()
    {
        $folder = $this->getParam('folder', 'inbox');
        $this->view->folder = $folder;

        $message_q = $this->em->createQueryBuilder();
        $message_q->select('n')
            ->from('Entity\Note', 'n')
            ->orderBy('n.created_at', 'DESC');

        if ($folder == 'outbox')
            $message_q->where('n.sender_id = :user_id');
        else
            $message_q->where('n.recipient_id = :user_id');

        $message_q->setParameter('user_id', $this->user->id);

        $messages = $message_q->getQuery()->getArrayResult();
        $this->view->messages = $messages;

        if ($this->hasParam('id'))
        {
            $id = (int)$this->getParam('id');

            if ($folder == 'outbox')
                $record = Note::getRepository()->findOneBy(array('id' => $id, 'sender_id' => $this->user->id));
            else
                $record = Note::getRepository()->findOneBy(array('id' => $id, 'recipient_id' => $this->user->id));

            if (!($record instanceof Note))
                throw new \App\Exception('Note not found!');

            $this->view->record = $record;
        }
    }

    public function composeAction()
    {

    }
}