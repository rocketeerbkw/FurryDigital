<?php
namespace Modules\Account\Controllers;

use Entity\User;
use Entity\TroubleTicket;
use Entity\TroubleTicketComment;

class TicketsController extends BaseController
{
    public function indexAction()
    {
        $form_config = $this->current_module_config->forms->ticket_new->toArray();
        $form = new \FA\Form($form_config);

        if ($this->request->isPost() && $form->isValid($_POST))
        {
            // Handle new ticket creation.
            $data = $form->getValues();

            // Add note to message for note sharing.
            if ($data['share_notes'])
                $data['message'] .= "\n\n[system]: Granting admins the permission to read user notes for the duration of the ticket.";

            $record = new TroubleTicket();
            $record->user = $this->user;
            $record->username = $this->user->username;
            $record->fromArray($data);

            $record->save();

            // Store note sharing information in user variable.
            if ($data['share_notes'])
            {
                $ticket_id = $record->id;
                $this->user->setVariable('RO.adminsCanSeeNotes', $ticket_id);
            }

            $this->alert('<b>New ticket created!</b><br>A staff member will respond to your ticket as soon as possible.', 'green');
            return $this->redirectHere();
        }

        $this->view->form = $form;

        // Show all existing tickets.
        $tickets = $this->em->createQuery('SELECT tt FROM Entity\TroubleTicket tt WHERE tt.user_id = :user_id ORDER BY tt.is_resolved ASC, tt.id DESC')
            ->setParameter('user_id', $this->user->id)
            ->execute();

        $this->view->tickets = $tickets;
    }

    public function viewAction()
    {
        $id = (int)$this->getParam('id');
        $record = TroubleTicket::getRepository()->findOneBy(array('id' => (int)$id, 'user_id' => $this->user->id));

        if (!($record instanceof TroubleTicket))
            throw new \FA\Exception('Trouble ticket not found!');

        $this->view->ticket = $record;

        // Create reply form.
        $form_config = $this->current_module_config->forms->ticket_reply->toArray();

        if (!$record->is_resolved)
            unset($form_config['elements']['reopen']);

        $form = new \FA\Form($form_config);

        if ($this->request->isPost() && $form->isValid($_POST))
        {
            $this->fa->readOnly();

            // Handle reply to the ticket.
            $data = $form->getValues();

            // Reopen ticket if indicated.
            if ($data['reopen'])
            {
                $data['message'] .= "\n\n[system]: User reopened the ticket.";
                $record->is_resolved = false;
            }

            $record->replies += 1;
            $record->last_reply_date = time();
            $record->save();

            // Add note to message for note sharing.
            if ($data['share_notes'])
                $data['message'] .= "\n\n[system]: Granting admins the permission to read user notes for the duration of the ticket.";

            $comment = new TroubleTicketComment();
            $comment->ticket = $record;
            $comment->is_staff = false;
            $comment->user = $this->user;

            $comment->fromArray($data);
            $comment->save();

            // Store note sharing information in user variable.
            if ($data['share_notes'])
            {
                $ticket_id = $record->id;
                $this->user->setVariable('RO.adminsCanSeeNotes', $ticket_id);
            }

            // Notify and redirect user.
            $this->alert('<b>Reply posted!</b>', 'green');
            return $this->redirectHere();
        }

        $this->view->form = $form;
    }

    public function closeAction()
    {
        $this->fa->readOnly();

        $id = (int)$this->getParam('id');
        $record = TroubleTicket::getRepository()->findOneBy(array('id' => (int)$id, 'user_id' => $this->user->id));

        if (!($record instanceof TroubleTicket))
            throw new \FA\Exception('Trouble ticket not found!');

        $record->is_resolved = true;
        $record->replies += 1;
        $record->last_reply_date = time();
        $record->save();

        $comment = new TroubleTicketComment();
        $comment->ticket = $record;
        $comment->is_staff = false;
        $comment->user = $this->user;
        $comment->message = '[system]: Revoking admin permission to read user notes granted for the duration of the ticket.';
        $comment->save();

        $this->user->deleteVariable('RO.adminsCanSeeNotes');

        $this->alert('<b>Ticket closed!</b>', 'green');
        return $this->redirectFromHere(array('action' => 'view'));
    }
}