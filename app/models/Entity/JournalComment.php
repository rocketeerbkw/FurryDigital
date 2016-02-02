<?php
namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Journal Comments
 *
 * @Table(name="comments_journal", indexes={
 *   @Index(name="date", columns={"date_posted"}),
 *   @Index(name="journal_level", columns={"entity_id", "level"}),
 *   @Index(name="journal_nestid", columns={"entity_id", "nest_level"}),
 *   @Index(name="user_id", columns={"user_id"}),
 *   @Index(name="parent_id", columns={"parent_id"})
 * })
 * @Entity
 * @HasLifecycleCallbacks
 */
class JournalComment extends \FA\Doctrine\Entity
{
    public function __construct()
    {
        $this->notifications = new ArrayCollection;
    }

    /**
     * @PostPersist
     */
    public function created()
    {
        \FA\Legacy\Notifications::dispatch('journal_comment', $this->id, $this->journal->user_id);
    }

    /**
     * @PreDelete
     */
    public function deleted()
    {
        \FA\Legacy\Notifications::purge('journal_comment', $this->id, $this->journal->user_id);
    }

    /**
     * @var integer
     *
     * @Column(name="row_id", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var integer The commenting user.
     * @Column(name="user_id", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $user_id;

    /**
     * @ManyToOne(targetEntity="User", inversedBy="journal_comments")
     * @JoinColumns({
     *   @JoinColumn(name="user_id", referencedColumnName="userid", onDelete="CASCADE")
     * })
     */
    protected $user;

    /**
     * @var integer The submission that this relates to.
     * @Column(name="entity_id", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $journal_id;

    /**
     * @ManyToOne(targetEntity="Journal", inversedBy="comments")
     * @JoinColumns({
     *   @JoinColumn(name="entity_id", referencedColumnName="row_id", onDelete="CASCADE")
     * })
     */
    protected $journal;

    /**
     * @var integer The ID of a parent comment (if available).
     * @Column(name="parent_id", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $parent_id;

    /**
     * @var integer
     * @Column(name="lft", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $lft;

    /**
     * @var integer
     * @Column(name="rgt", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $rgt;

    /**
     * @var float
     * @Column(name="nest_level", type="float", precision=10, scale=0, nullable=false)
     */
    protected $nest_level;

    /**
     * @var integer
     * @Column(name="level", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $level;

    /**
     * @var integer
     * @Column(name="date_posted", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $date_posted;

    /**
     * @var integer
     * @Column(name="date_updated", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $date_updated;

    /**
     * @var integer
     * @Column(name="is_collapsed", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $is_collapsed;

    /**
     * @var integer
     * @Column(name="is_deleted", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $is_deleted;

    /**
     * @var string
     * @Column(name="subject", type="string", options={"default"=""}, length=60, nullable=false)
     */
    protected $subject;

    /**
     * @var string
     * @Column(name="message", type="text", length=16777215, nullable=false)
     */
    protected $message;

    /**
     * @OneToMany(targetEntity="JournalCommentNotify", mappedBy="comment")
     */
    protected $notifications;
}
