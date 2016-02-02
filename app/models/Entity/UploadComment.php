<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Upload Comments
 *
 * @Table(name="comments_submission", indexes={
 *   @Index(name="subid_level", columns={"entity_id", "level"}),
 *   @Index(name="subid_nestid", columns={"entity_id", "nest_level"}),
 *   @Index(name="for_comment_search", columns={"date_posted"}),
 *   @Index(name="user_id", columns={"user_id"}),
 *   @Index(name="parent_id", columns={"parent_id"}),
 *   @Index(name="upload_id", columns={"entity_id"})
 * })
 * @Entity
 * @HasLifecycleCallbacks
 */
class UploadComment extends \FA\Doctrine\Entity
{
    use CommentTrait;

    // TODO: Move to ACL
    // How much time before a user is unable to edit their comments
    const COMMENT_EDIT_DURATION = 300; // 5 minutes
    const COMMENT_EDIT_DURATION_ADMIN = 900; // 15 minutes

    public function __construct()
    {
        $this->created_at = time();
        $this->updated_at = time();

        $this->notifications = new ArrayCollection;
    }

    /**
     * @PreUpdate
     */
    public function updated()
    {
        $this->updated_at = time();
    }
    
    /**
     * @PostPersist
     */
    public function created()
    {
        User::incrementCounter('comments_sent', $this->user_id);
        User::incrementCounter('comments_received', $this->upload->user_id);

        \FA\Legacy\Notifications::dispatch('upload_comment', $this->id, $this->upload->user_id, $this->upload->id);
    }

    /**
     * @PreDelete
     */
    public function deleted()
    {
        User::decrementCounter('comments_sent', $this->user_id);
        User::decrementCounter('comments_received', $this->upload->user_id);

        \FA\Legacy\Notifications::purge('upload_comment', $this->id, $this->upload->user_id);
    }

    /**
     * @var integer
     * @Column(name="row_id", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var integer Commenting User ID.
     * @Column(name="user_id", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $user_id;

    /**
     * @ManyToOne(targetEntity="User", inversedBy="upload_comments")
     * @JoinColumns({
     *   @JoinColumn(name="user_id", referencedColumnName="userid", onDelete="CASCADE")
     * })
     */
    protected $user;

    /**
     * @var integer The submission ID.
     * @Column(name="entity_id", type="integer", length=11, options={"unsigned"=true}, nullable=true)
     */
    protected $upload_id;

    /**
     * @ManyToOne(targetEntity="Upload", inversedBy="comments")
     * @JoinColumns({
     *   @JoinColumn(name="entity_id", referencedColumnName="rowid", onDelete="CASCADE")
     * })
     */
    protected $upload;

    /**
     * @var integer Parent comment ID (optional).
     * @Column(name="parent_id", type="integer", length=11, options={"unsigned"=true}, nullable=true)
     */
    protected $parent_id;
    
    /**
     * @ManyToOne(targetEntity="UploadComment", inversedBy="comments")
     * @JoinColumn(name="parent_id", referencedColumnName="row_id", onDelete="CASCADE")
     */
    protected $parent;

    /**
     * TODO: Unused Field
     * @var integer
     * @Column(name="lft", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $_unused_lft = 0;

    /**
     * TODO: Unused Field
     * @var integer
     * @Column(name="rgt", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $_unused_rgt = 0;

    /**
     * TODO: Unused Field
     * @var float
     * @Column(name="nest_level", type="float", precision=10, scale=0, nullable=false)
     */
    protected $_unused_nest_level = 0;

    /**
     * TODO: Unused Field
     * @var integer
     * @Column(name="level", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $_unused_level = 0;

    /**
     * @var integer
     * @Column(name="date_posted", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $created_at;

    /**
     * @var integer
     * @Column(name="date_updated", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $updated_at;

    /**
     * @var integer UserID of the user who deleted the comment.
     * @Column(name="is_deleted", type="integer", length=11, options={"unsigned"=true}, nullable=true)
     */
    protected $deleting_user_id;
    
    /**
     * @ManyToOne(targetEntity="User")
     * @JoinColumn(name="is_deleted", referencedColumnName="userid", onDelete="CASCADE")
     */
    protected $deleting_user;

    /**
     * TODO: Unused Field
     * @var string
     * @Column(name="subject", type="string", options={"default"=""}, length=60, nullable=false)
     */
    protected $_unused_subject = '';

    /**
     * @var string
     * @Column(name="message", type="text", length=65535, nullable=false)
     */
    protected $message ='';

    /**
     * @OneToMany(targetEntity="UploadCommentNotify", mappedBy="comment")
     */
    protected $notifications;
    
    public static function getEditDuration()
    {
        $di = \Phalcon\Di::getDefault();
        $acl = $di['acl'];
        
        return ($acl->isAllowed('administer all') ? self::COMMENT_EDIT_DURATION_ADMIN : self::COMMENT_EDIT_DURATION);
    }
}
