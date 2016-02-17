<?php
namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * MessagecenterWatches
 *
 * @Table(name="watch_notify", indexes={@Index(name="entity_id", columns={"entity_id"})})
 * @Entity
 */
class WatchNotify extends \App\Doctrine\Entity
{
    use Traits\NotifyTrait;
    protected static $identifier = 'watch_id';

    /**
     * @var integer
     * @Column(name="user_id", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     * @GeneratedValue(strategy="NONE")
     */
    protected $user_id;

    /**
     * @ManyToOne(targetEntity="User", inversedBy="watch_notifications")
     * @JoinColumns({
     *   @JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     * @Id
     */
    protected $user;

    /**
     * @var integer
     * @Column(name="entity_id", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     * @GeneratedValue(strategy="NONE")
     */
    protected $watch_id;

    /**
     * @ManyToOne(targetEntity="Watch", inversedBy="notifications")
     * @JoinColumns({
     *   @JoinColumn(name="entity_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     * @Id
     */
    protected $watch;

    /**
     * @var integer
     * @Column(name="source_id", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $source_id;

}
