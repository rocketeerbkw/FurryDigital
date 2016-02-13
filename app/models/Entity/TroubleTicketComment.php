<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Trouble Ticket Comments
 *
 * @Table(name="trouble_ticket_comment", indexes={
 *   @Index(name="ticketid_userid", columns={"ticket_id", "user_id"}),
 *   @Index(name="ticketid_date", columns={"ticket_id", "created_at"})
 * })
 * @Entity
 * @HasLifecycleCallbacks
 */
class TroubleTicketComment extends \App\Doctrine\Entity
{
    public function __construct()
    {
        $this->created_at = time();
    }

    /**
     * @var integer
     * @Column(name="id", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @PostPersist
     */
    public function created()
    {
        if ($this->user_id !== $this->ticket->user_id)
            \App\Legacy\Notifications::dispatch('ticket', $this->ticket->id, $this->ticket->user_id);
    }

    /**
     * @PreDelete
     */
    public function deleted()
    {
        \App\Legacy\Notifications::purge('ticket', $this->id, $this->ticket->user_id);
    }

    /**
     * @var integer
     * @Column(name="ticket_id", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $ticket_id;

    /**
     * @ManyToOne(targetEntity="TroubleTicket", inversedBy="comments")
     * @JoinColumns({
     *   @JoinColumn(name="ticket_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    protected $ticket;

    /**
     * @var integer
     * @Column(name="user_id", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $user_id;

    /**
     * @ManyToOne(targetEntity="User", inversedBy="trouble_ticket_comments")
     * @JoinColumns({
     *   @JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    protected $user;

    /**
     * @var boolean
     * @Column(name="isstaff", type="boolean", nullable=false)
     */
    protected $is_staff = false;

    /**
     * TODO: Unused field
     * @var string
     * @Column(name="username", type="string", options={"default"=""}, length=30, nullable=false)
     */
    protected $username = '';

    /**
     * @var integer
     * @Column(name="created_at", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $created_at;

    /**
     * @var string
     * @Column(name="message", type="text", length=65535, nullable=false)
     */
    protected $message;

}
