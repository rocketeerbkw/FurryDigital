<?php
namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Favorites Notifications
 *
 * @Table(name="favorite_notify", indexes={@Index(name="entity_id", columns={"entity_id"})})
 * @Entity
 */
class FavoriteNotify extends \App\Doctrine\Entity
{
    use Traits\NotifyTrait;
    protected static $identifier = 'favorite_id';

    /**
     * @var integer
     * @Column(name="user_id", type="integer", length=11, options={"unsigned"=true}, nullable=true)
     */
    protected $user_id;

    /**
     * @ManyToOne(targetEntity="User", inversedBy="favorite_notifications")
     * @JoinColumns({
     *   @JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     * @Id
     */
    protected $user;

    /**
     * @var integer
     * @Column(name="entity_id", type="integer", length=11, options={"unsigned"=true}, nullable=true)
     */
    protected $favorite_id;

    /**
     * @ManyToOne(targetEntity="Favorite", inversedBy="notifications")
     * @JoinColumns({
     *   @JoinColumn(name="entity_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     * @Id
     */
    protected $favorite;

    /**
     * @var integer
     * @Column(name="source_id", type="integer", length=11, options={"unsigned"=true}, nullable=true)
     */
    protected $upload_id;
    
    /**
     * @ManyToOne(targetEntity="Upload", inversedBy="favorite_notifications")
     * @JoinColumns({
     *   @JoinColumn(name="source_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    protected $upload;
}