<?php
namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Journals
 *
 * @Table(name="journal", indexes={
 *   @Index(name="user", columns={"user_id"}),
 *   @Index(name="date", columns={"created_at"})
 * })
 * @Entity
 * @HasLifecycleCallbacks
 */
class Journal extends \App\Doctrine\Entity
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
        \App\Notifications::dispatch('upload', $this->id);
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
                \App\Notifications::purge('journal_comment', $comment->id, $this->user_id);
            }
        }

        User::decrementCounter('journals', $this->user_id);
        \App\Notifications::purge('journal', $this->id);
    }


    /**
     * @var integer
     * @Column(name="id", type="integer", length=11, options={"unsigned"=true}, nullable=false)
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
     *   @JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    protected $user;

    /**
     * @var integer
     * @Column(name="created_at", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $created_at;

    /**
     * @var integer
     * @Column(name="disable_comments", type="integer", length=11, options={"unsigned"=true}, nullable=false)
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
