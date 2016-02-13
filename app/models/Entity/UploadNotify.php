<?php
namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * MessagecenterSubmissions
 *
 * @Table(name="upload_notify", indexes={@Index(name="entity_id", columns={"entity_id"})})
 * @Entity
 */
class UploadNotify extends \App\Doctrine\Entity
{
    use Traits\NotifyTrait;
    protected static $identifier = 'upload_id';

    /**
     * @var integer
     * @Column(name="user_id", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     * @GeneratedValue(strategy="NONE")
     */
    protected $user_id;

    /**
     * @ManyToOne(targetEntity="User", inversedBy="upload_notifications")
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
    protected $upload_id;

    /**
     * @ManyToOne(targetEntity="Upload", inversedBy="notifications")
     * @JoinColumns({
     *   @JoinColumn(name="entity_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     * @Id
     */
    protected $upload;
}
