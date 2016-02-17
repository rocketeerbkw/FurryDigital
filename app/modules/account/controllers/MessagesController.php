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

    public function otherAction()
    {
        $notifications = $this->user->getNotifications('other');
        $this->view->notifications = $notifications;
    }

    public function othersAction()
    {
        return $this->dispatcher->forward(array('action' => 'other'));
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
        // Assemble list of folders.
        $folders = array(
            'inbox'     => 'Inbox',
            'outbox'    => 'Outbox',
        );
        $folder = $this->getParam('folder', 'inbox');

        if (!isset($folders[$folder]))
            $folder = 'inbox';

        $this->view->folder = $folder;
        $this->view->folders = $folders;
        $this->view->folder_name = $folders[$folder];

        // Find messages inside specified folder.
        $message_q = $this->em->createQueryBuilder();
        $message_q->select('n, u')
            ->from('Entity\Note', 'n')
            ->orderBy('n.created_at', 'DESC');

        if ($folder == 'outbox')
        {
            $message_q->where('n.sender_id = :user_id')
                ->join('n.recipient', 'u');
        }
        else
        {
            $message_q->where('n.recipient_id = :user_id')
                ->join('n.sender', 'u');
        }

        $message_q->setParameter('user_id', $this->user->id);

        $messages = $message_q->getQuery()->getArrayResult();
        $this->view->messages = $messages;

        // View a specific message in the main panel.
        if ($this->hasParam('id'))
        {
            $id = (int)$this->getParam('id');

            if ($folder == 'outbox')
            {
                $record = Note::getRepository()->findOneBy(array('id' => $id, 'sender_id' => $this->user->id));

                if (!($record instanceof Note))
                    throw new \App\Exception('Note not found!');
            }
            else
            {
                $record = Note::getRepository()->findOneBy(array('id' => $id, 'recipient_id' => $this->user->id));

                if (!($record instanceof Note))
                    throw new \App\Exception('Note not found!');

                $record->setIsRead(true);
                $record->save();

                // Assemble the "reply" form.
                $form_config = $this->current_module_config->forms->message->toArray();
                unset($form_config['elements']['recipient']);
                unset($form_config['elements']['subject']);

                $reply_form = new \App\Form($form_config);

                if ($this->request->isPost() && $reply_form->isValid($_POST))
                {
                    $data = $reply_form->getValues();

                    $reply = new Note;
                    $reply->sender = $this->user;
                    $reply->recipient = $record->sender;

                    $reply->subject = 'Re: '.trim(str_replace('Re:', '', $record->subject));
                    $reply->message = $data['message'];

                    $reply->save();

                    $this->alert('<b>Reply sent!</b>', 'green');
                    return $this->redirectHere();
                }

                $this->view->reply_form = $reply_form;
            }

            $this->view->record = $record;
        }
    }

    public function composeAction()
    {
        $form_config = $this->current_module_config->forms->message->toArray();
        $form = new \App\Form($form_config);

        if ($this->request->isPost() && $form->isValid($_POST))
        {
            $data = $form->getValues();

            $recipient = User::findByLower($data['recipient']);

            if (!($recipient instanceof User))
                $form->addError('recipient', 'Recipient not found! Check their username and try resending your message.');

            if (!$form->hasErrors())
            {
                $record = new Note;
                $record->sender = $this->user;
                $record->recipient = $recipient;

                $record->subject = $data['subject'];
                $record->message = $data['message'];

                $record->save();

                $this->alert('<b>Message sent!</b>', 'green');
                return $this->redirectFromHere(array('action' => 'pms', 'folder' => null, 'recipient' => null));
            }
        }
        else if (!empty($_REQUEST['recipient']))
        {
            $form->populate(array('recipient' => $_REQUEST['recipient']));
        }

        $this->view->form = $form;
    }
}