<?php
namespace Modules\Account\Controllers;

use Entity\User;

class MessagesController extends BaseController
{
    const SYS_MESSAGES_SUBMISSIONS_PERPAGE = 16;
    const SYS_MESSAGES_PMS_PERPAGE = 16;
    const SYS_MESSAGES_TTS_PERPAGE = 20;
    const SYS_MESSAGES_ADMINNOTICES_PERPAGE = 20;

    const PMS_BOX_INBOX = 0;
    const PMS_BOX_OUTBOX = 1;
    const PMS_BOX_HIGH_PRIO = 2;
    const PMS_BOX_MEDIUM_PRIO = 3;
    const PMS_BOX_LOW_PRIO = 4;
    const PMS_BOX_TRASH = 5;
    const PMS_BOX_ARCHIVE = 6;
    const PMS_BOX_DELETED = 7;

    public function indexAction()
    {
        // Message Center index
    }

    public function othersAction()
    {
        $notifications = $this->user->getNotifications('other');

        if ($this->request->isPost() && $this->hasParam('do'))
        {
            $this->csrf->requireValid($_POST['csrf']);

            $notify_type = $this->getParam('type');
            if (isset($notifications[$notify_type]))
            {
                switch($this->getParam('do'))
                {
                    case 'remove_all':
                        
                    break;

                    case 'remove_selected':

                    break;
                }

                $this->user->updateNotificationCount($notify_type);
            }

            $this->alert('<b>Selected notifications cleared!</b>', 'green');
            return $this->redirectFromHere(array('type' => null, 'do' => null));
        }

        $this->view->notifications = $notifications;
    }

    public function uploadsAction()
    {
    }

    public function pmsAction()
    {
    }

    public function troubleticketsAction()
    {
    }

    public function viewAction()
    {
    }

    public function sendAction()
    {
    }

    public function composeAction()
    {
    }
}