<?php
namespace Modules\Account\Controllers;

use Entity\User;

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