<?php
namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Trouble Tickets
 *
 * @Table(name="troubletickets", indexes={
 *   @Index(name="resolved_lastlookedat", columns={"resolved", "lastlookedat"}),
 *   @Index(name="userid_resolved", columns={"userid", "resolved"})
 * })
 * @Entity
 */
class TroubleTicket extends \FA\Doctrine\Entity
{
    public function __construct()
    {
        $this->created_at = time();

        $this->comments = new ArrayCollection;
        $this->notifications = new ArrayCollection;
    }

    /**
     * @var integer
     * @Column(name="rowid", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var integer The submitting user.
     * @Column(name="userid", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $user_id;

    /**
     * @ManyToOne(targetEntity="User", inversedBy="trouble_tickets")
     * @JoinColumns({
     *   @JoinColumn(name="userid", referencedColumnName="userid", onDelete="CASCADE")
     * })
     */
    protected $user;

    /**
     * @var integer The user assigned to review the ticket.
     * @Column(name="assigned_to_user_id", type="integer", length=11, options={"unsigned"=true}, nullable=true)
     */
    protected $assigned_to_user_id;

    /**
     * @ManyToOne(targetEntity="User", inversedBy="trouble_tickets_assigned")
     * @JoinColumns({
     *   @JoinColumn(name="assigned_to_user_id", referencedColumnName="userid", onDelete="SET NULL")
     * })
     */
    protected $assigned_to_user;

    /**
     * @var string
     * @Column(name="username", type="string", options={"default"=""}, length=30, nullable=false)
     */
    protected $username;

    /**
     * @var int
     * @Column(name="issuetype", type="smallint", nullable=false)
     */
    protected $issue_type = 0;

    public function getIssueTypeName()
    {
        return self::getNameForType($this->issue_type);
    }

    /**
     * @var string The "other" for the issue type, a.k.a. the issue subject.
     * @Column(name="other", type="string", options={"default"=""}, length=80, nullable=false)
     */
    protected $other = '';

    /**
     * @var string
     * @Column(name="message", type="text", length=65535, nullable=false)
     */
    protected $message = '';

    /**
     * @var boolean
     * @Column(name="resolved", type="boolean", nullable=false)
     */
    protected $is_resolved = 0;

    /**
     * @var integer
     * @Column(name="lastlookedat", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $last_reply_date = 0;

    /**
     * @var integer
     * @Column(name="replies", type="smallint", nullable=false)
     */
    protected $replies = 0;

    /**
     * @var string An administrator reply.
     * @Column(name="admin", type="string", options={"default"=""}, length=30, nullable=false)
     */
    protected $admin = '';

    /**
     * @var integer
     * @Column(name="ticketdate", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $created_at;

    /**
     * @OneToMany(targetEntity="TroubleTicketComment", mappedBy="ticket")
     */
    protected $comments;

    /**
     * @OneToMany(targetEntity="TroubleTicketNotify", mappedBy="ticket")
     */
    protected $notifications;

    public static function getNameForType($type_id)
    {
        static $type_lookup;

        if (!$type_lookup)
        {
            $di = \Phalcon\Di::getDefault();
            $config = $di['config'];

            $types_raw = $config->fa->trouble_ticket_types->toArray();

            $type_lookup = array();
            foreach($types_raw as $group_name => $group_items)
            {
                foreach($group_items as $item_name => $item_info)
                    $type_lookup[$item_info['id']] = $item_name;
            }
        }

        return $type_lookup[$type_id];
    }
}
