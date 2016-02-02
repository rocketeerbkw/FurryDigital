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

    public function uploadsAction()
    {
    }

    public function othersAction()
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