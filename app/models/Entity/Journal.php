<?php
namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Journals
 *
 * @Table(name="journals", indexes={
 *   @Index(name="user", columns={"user_id"}),
 *   @Index(name="date", columns={"date_posted"})
 * })
 * @Entity
 * @HasLifecycleCallbacks
 */
class Journal extends \FA\Doctrine\Entity
{
    public function __construct()
    {
        $this->created_at = time();

        $this->comments = new ArrayCollection;
        $this->notifications = new ArrayCollection;
    }

    /**
     * @PostPersist
     */
    public function created()
    {
        User::incrementCounter('journals', $this->user_id);
        \FA\Legacy\Notifications::dispatch('upload', $this->id);
    }

    /**
     * @PreDelete
     */
    public function deleted()
    {
        if ($this->comments->count() > 0)
        {
            foreach($this->comments as $comment)
            {
                \FA\Legacy\Notifications::purge('journal_comment', $comment->id, $this->user_id);
            }
        }

        User::decrementCounter('journals', $this->user_id);
        \FA\Legacy\Notifications::purge('journal', $this->id);
    }


    /**
     * @var integer
     * @Column(name="row_id", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var integer The submitting user.
     * @Column(name="user_id", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $user_id;

    /**
     * @ManyToOne(targetEntity="User", inversedBy="journals")
     * @JoinColumns({
     *   @JoinColumn(name="user_id", referencedColumnName="userid", onDelete="CASCADE")
     * })
     */
    protected $user;

    /**
     * @var integer
     * @Column(name="date_posted", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $created_at;

    /**
     * @var integer
     * @Column(name="comments_locked", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $disable_comments;

    /**
     * @var integer
     * @Column(name="num_comments", type="smallint", nullable=false)
     */
    protected $num_comments = 0;

    /**
     * @var string
     * @Column(name="subject", type="string", options={"default"=""}, length=60, nullable=false)
     */
    protected $subject;

    /**
     * @var string
     * @Column(name="message", type="text", length=65535, nullable=false)
     */
    protected $message;

    /**
     * @OneToMany(targetEntity="JournalComment", mappedBy="journal")
     */
    protected $comments;

    /**
     * @OneToMany(targetEntity="JournalNotify", mappedBy="journal")
     */
    protected $notifications;
}
