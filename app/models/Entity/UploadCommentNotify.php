<?php
namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Upload Notifications
 *
 * @Table(name="upload_comment_notify", indexes={@Index(name="entity_id", columns={"entity_id"})})
 * @Entity
 */
class UploadCommentNotify extends \App\Doctrine\Entity
{
    use Traits\NotifyTrait;
    protected static $identifier = 'comment_id';

    /**
     * @var integer
     * @Column(name="user_id", type="integer", length=11, options={"unsigned"=true}, nullable=true)
     */
    protected $user_id;

    /**
     * @ManyToOne(targetEntity="User", inversedBy="upload_comment_notifications")
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
    protected $comment_id;

    /**
     * @ManyToOne(targetEntity="UploadComment", inversedBy="notifications")
     * @JoinColumns({
     *   @JoinColumn(name="entity_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     * @Id
     */
    protected $comment;

    /**
     * @var integer
     * @Column(name="source_id", type="integer", length=11, options={"unsigned"=true}, nullable=true)
     */
    protected $upload_id;

    /**
     * @ManyToOne(targetEntity="Upload", inversedBy="comment_notifications")
     * @JoinColumns({
     *   @JoinColumn(name="source_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    protected $upload;
}
