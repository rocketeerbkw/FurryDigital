<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use App\Legacy\Notifications;

/**
 * Notes
 *
 * @Table(name="notes", indexes={
 *   @Index(name="for_comment_search_1", columns={"date_posted"}),
 *   @Index(name="target_id__is_read", columns={"target_id", "is_read"})
 * })
 * @Entity
 * @HasLifecycleCallbacks
 */
class Note extends \App\Doctrine\Entity
{
    const FOLDER_INBOX = 0;
    const FOLDER_OUTBOX = 1;

    public function __construct()
    {
        $this->created_at = time();
    }

    /**
     * @PostPersist
     */
    public function created()
    {
        Notifications::dispatch('shout', $this->id, $this->recipient->id, $this->sender->id);
    }

    /**
     * @PreDelete
     */
    public function deleting()
    {
        Notifications::purge('note', $this->id, $this->recipient->id);
    }

    /**
     * @var integer
     * @Column(name="row_id", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var integer The sending user ID.
     * @Column(name="sender_id", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $sender_id;

    /**
     * @ManyToOne(targetEntity="User", inversedBy="notes_sent")
     * @JoinColumns({
     *   @JoinColumn(name="sender_id", referencedColumnName="userid", onDelete="CASCADE")
     * })
     */
    protected $sender;

    /**
     * @var integer The recipient user ID.
     * @Column(name="target_id", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $recipient_id;

    /**
     * @ManyToOne(targetEntity="User", inversedBy="notes_received")
     * @JoinColumns({
     *   @JoinColumn(name="target_id", referencedColumnName="userid", onDelete="CASCADE")
     * })
     */
    protected $recipient;

    /**
     * @var boolean
     * @Column(name="is_read", type="boolean", nullable=false)
     */
    protected $is_read;

    public function setIsRead($new_value)
    {
        if ((bool)$new_value != (bool)$this->is_read)
        {
            if ($new_value)
                Notifications::purge('note', $this->id, $this->recipient_id);
            else
                Notifications::dispatch('note', $this->id, $this->recipient_id);
        }

        $this->is_read = (bool)$new_value;
    }

    /**
     * @var integer
     * @Column(name="date_posted", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $created_at;

    /**
     * @var string
     * @Column(name="title", type="string", options={"default"=""}, length=120, nullable=false)
     */
    protected $title;

    /**
     * @var string
     * @Column(name="message", type="text", length=65535, nullable=false)
     */
    protected $message;

}
