<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Trouble Ticket Comments
 *
 * @Table(name="comments_troubleticket", indexes={
 *   @Index(name="ticketid_userid", columns={"ticketid", "userid"}),
 *   @Index(name="ticketid_date", columns={"ticketid", "date"})
 * })
 * @Entity
 * @HasLifecycleCallbacks
 */
class TroubleTicketComment extends \FA\Doctrine\Entity
{
    public function __construct()
    {
        $this->created_at = time();
    }

    /**
     * @var integer
     * @Column(name="rowid", type="integer", length=11, options={"unsigned"=true}, nullable=false)
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
            \FA\Legacy\Notifications::dispatch('ticket', $this->ticket->id, $this->ticket->user_id);
    }

    /**
     * @PreDelete
     */
    public function deleted()
    {
        \FA\Legacy\Notifications::purge('ticket', $this->id, $this->ticket->user_id);
    }

    /**
     * @var integer
     * @Column(name="ticketid", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $ticket_id;

    /**
     * @ManyToOne(targetEntity="TroubleTicket", inversedBy="comments")
     * @JoinColumns({
     *   @JoinColumn(name="ticketid", referencedColumnName="rowid", onDelete="CASCADE")
     * })
     */
    protected $ticket;

    /**
     * @var integer
     * @Column(name="userid", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $user_id;

    /**
     * @ManyToOne(targetEntity="User", inversedBy="trouble_ticket_comments")
     * @JoinColumns({
     *   @JoinColumn(name="userid", referencedColumnName="userid", onDelete="CASCADE")
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
     * @Column(name="date", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $created_at;

    /**
     * @var string
     * @Column(name="message", type="text", length=65535, nullable=false)
     */
    protected $message;

}
