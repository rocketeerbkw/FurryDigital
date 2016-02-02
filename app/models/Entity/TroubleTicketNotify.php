<?php
namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Trouble Ticket Notifications
 *
 * @Table(name="df_usermessages_Tickets", indexes={@Index(name="userid", columns={"userid"})})
 * @Entity
 */
class TroubleTicketNotify extends \FA\Doctrine\Entity
{
    /**
     * @var integer
     * @Column(name="rowid", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var integer
     * @Column(name="ticketid", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $ticket_id;

    /**
     * @ManyToOne(targetEntity="TroubleTicket", inversedBy="notifications")
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
     * @ManyToOne(targetEntity="User", inversedBy="trouble_ticket_notifications")
     * @JoinColumns({
     *   @JoinColumn(name="userid", referencedColumnName="userid", onDelete="CASCADE")
     * })
     */
    protected $user;
}
