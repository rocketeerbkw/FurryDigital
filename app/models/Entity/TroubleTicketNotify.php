<?php
namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Trouble Ticket Notifications
 *
 * @Table(name="trouble_ticket_notify", indexes={
 *   @Index(name="userid", columns={"user_id"})
 * })
 * @Entity
 */
class TroubleTicketNotify extends \App\Doctrine\Entity
{
    use Traits\NotifyTrait;
    protected static $identifier = 'ticket_id';

    /**
     * @var integer
     * @Column(name="id", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var integer
     * @Column(name="ticket_id", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $ticket_id;

    /**
     * @ManyToOne(targetEntity="TroubleTicket", inversedBy="notifications")
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
     * @ManyToOne(targetEntity="User", inversedBy="trouble_ticket_notifications")
     * @JoinColumns({
     *   @JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    protected $user;
}
