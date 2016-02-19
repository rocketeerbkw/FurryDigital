<?php
namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Shouts
 *
 * @Table(name="shout", indexes={
 *   @Index(name="user", columns={"target_id"}),
 *   @Index(name="message", columns={"message"}),
 *   @Index(name="shouterid_user", columns={"sender_id", "target_id"}),
 *   @Index(name="for_comment_search", columns={"created_at"})
 * })
 * @Entity
 * @HasLifecycleCallbacks
 */
class Shout extends \App\Doctrine\Entity
{
    public function __construct()
    {
        $this->created_at = time();

        $this->notifications = new ArrayCollection;
    }

    /**
     * @PostPersist
     */
    public function created()
    {
        \App\Notifications::dispatch('shout', $this->id, $this->recipient->id, $this->sender->id);
    }

    /**
     * @PreDelete
     */
    public function deleted()
    {
        \App\Notifications::purge('shout', $this->id, $this->recipient->id);
    }

    /**
     * @var integer
     * @Column(name="id", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var integer
     * @Column(name="sender_id", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $sender_id;

    /**
     * @ManyToOne(targetEntity="User", inversedBy="shouts_sent")
     * @JoinColumns({
     *   @JoinColumn(name="sender_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    protected $sender;

    /**
     * @var integer
     * @Column(name="target_id", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $recipient_id;

    /**
     * @ManyToOne(targetEntity="User", inversedBy="shouts_received")
     * @JoinColumns({
     *   @JoinColumn(name="target_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    protected $recipient;

    /**
     * @var integer
     * @Column(name="parent_id", type="integer", length=11, options={"unsigned"=true}, nullable=true)
     */
    protected $parent_id;

    /**
     * @var integer
     * @Column(name="lft", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $lft = 0;

    /**
     * @var integer
     * @Column(name="rgt", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $rgt = 0;

    /**
     * @var integer
     * @Column(name="created_at", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $created_at;

    /**
     * @var boolean
     * @Column(name="is_deleted", type="boolean", nullable=false)
     */
    protected $is_deleted = 0;

    /**
     * @var string
     * @Column(name="message", type="string", options={"default"=""}, length=255, nullable=false)
     */
    protected $message;

    /**
     * @OneToMany(targetEntity="ShoutNotify", mappedBy="shout")
     */
    protected $notifications;

}