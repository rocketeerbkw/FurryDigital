<?php
namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Notifications for Shouts Recevied
 *
 * @Table(name="shout_notify", indexes={@Index(name="entity_id", columns={"entity_id"})})
 * @Entity
 */
class ShoutNotify extends \App\Doctrine\Entity
{
    use Traits\NotifyTrait;
    protected static $identifier = 'shout_id';

    /**
     * @var integer
     * @Column(name="user_id", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     * @GeneratedValue(strategy="NONE")
     */
    protected $user_id;

    /**
     * @ManyToOne(targetEntity="User", inversedBy="shout_notifications")
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
    protected $shout_id;

    /**
     * @ManyToOne(targetEntity="Shout", inversedBy="notifications")
     * @JoinColumns({
     *   @JoinColumn(name="entity_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     * @Id
     */
    protected $shout;

    /**
     * @var integer
     * @Column(name="source_id", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $source_id;
}
