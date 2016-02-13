<?php
namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Message Center Journal Comment Notifications
 *
 * @Table(name="journal_comment_notify", indexes={
 *   @Index(name="entity_id", columns={"entity_id"})
 * })
 * @Entity
 */
class JournalCommentNotify extends \App\Doctrine\Entity
{
    use Traits\NotifyTrait;
    protected static $identifier = 'comment_id';

    /**
     * @var integer
     * @Column(name="user_id", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     * @Id
     * @GeneratedValue(strategy="NONE")
     */
    protected $user_id;

    /**
     * @ManyToOne(targetEntity="User", inversedBy="journal_comment_notifications")
     * @JoinColumns({
     *   @JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    protected $user;

    /**
     * @var integer
     * @Column(name="entity_id", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     * @Id
     * @GeneratedValue(strategy="NONE")
     */
    protected $comment_id;

    /**
     * @ManyToOne(targetEntity="JournalComment", inversedBy="notifications")
     * @JoinColumns({
     *   @JoinColumn(name="comment_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    protected $comment;

    /**
     * @var integer
     * @Column(name="source_id", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $source_id;


}
