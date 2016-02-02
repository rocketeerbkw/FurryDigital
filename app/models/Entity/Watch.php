<?php
namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Watches
 *
 * @Table(name="watches", indexes={
 *   @Index(name="target_user", columns={"target_id", "user_id"}),
 *   @Index(name="user", columns={"user_id"})
 * })
 * @Entity
 * @HasLifecycleCallbacks
 */
class Watch extends \FA\Doctrine\Entity
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
        \FA\Legacy\Notifications::dispatch('watch', $this->id, $this->target->id, $this->user->id);
    }

    /**
     * @PreDelete
     */
    public function deleted()
    {
        \FA\Legacy\Notifications::purge('watch', $this->id, $this->target_id);
    }

    /**
     * @var integer
     * @Column(name="row_id", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var integer
     * @Column(name="user_id", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $user_id;

    /**
     * @ManyToOne(targetEntity="User", inversedBy="watches")
     * @JoinColumns({
     *   @JoinColumn(name="user_id", referencedColumnName="userid", onDelete="CASCADE")
     * })
     */
    protected $user;

    /**
     * @var integer
     * @Column(name="target_id", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $target_id;

    /**
     * @ManyToOne(targetEntity="User", inversedBy="watching_me")
     * @JoinColumns({
     *   @JoinColumn(name="target_id", referencedColumnName="userid", onDelete="CASCADE")
     * })
     */
    protected $target;

    /**
     * @var integer
     * @Column(name="date_watched", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $created_at;

    /**
     * @var boolean
     * @Column(name="watch_type", type="smallint", nullable=false)
     */
    protected $watch_type = 0;

    /**
     * @OneToMany(targetEntity="WatchNotify", mappedBy="watch")
     */
    protected $notifications;

}