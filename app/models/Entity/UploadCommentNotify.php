<?php
namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Upload Notifications
 *
 * @Table(name="messagecenter_comments_submission", indexes={@Index(name="entity_id", columns={"entity_id"})})
 * @Entity
 */
class UploadCommentNotify extends \FA\Doctrine\Entity
{
    /**
     * @var integer
     * @Column(name="user_id", type="integer", length=11, options={"unsigned"=true}, nullable=true)
     */
    protected $user_id;

    /**
     * @ManyToOne(targetEntity="User", inversedBy="upload_comment_notifications")
     * @JoinColumns({
     *   @JoinColumn(name="user_id", referencedColumnName="userid", onDelete="CASCADE")
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
     *   @JoinColumn(name="entity_id", referencedColumnName="row_id", onDelete="CASCADE")
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
     *   @JoinColumn(name="source_id", referencedColumnName="rowid", onDelete="CASCADE")
     * })
     */
    protected $upload;
}
