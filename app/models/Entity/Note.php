<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use FA\Legacy\Notifications;

/**
 * Notes
 *
 * @Table(name="notes", indexes={
 *   @Index(name="for_comment_search_1", columns={"date_posted"}),
 *   @Index(name="fromlower", columns={"fromlower"}),
 *   @Index(name="sender_id__folder_sender", columns={"sender_id", "folder_sender"}),
 *   @Index(name="target_id__folder_target", columns={"target_id", "folder_target"}),
 *   @Index(name="target_id__is_read", columns={"target_id", "is_read"})
 * })
 * @Entity
 * @HasLifecycleCallbacks
 */
class Note extends \FA\Doctrine\Entity
{
    public function __construct()
    {

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
     * @var string
     * @Column(name="fromlower", type="string", options={"default"=""}, length=30, nullable=false)
     */
    protected $fromlower;

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
     * @Column(name="folder_sender", type="boolean", nullable=false)
     */
    protected $folder_sender;

    /**
     * @var boolean
     * @Column(name="folder_target", type="boolean", nullable=false)
     */
    protected $folder_target;

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
    protected $date_posted;

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
