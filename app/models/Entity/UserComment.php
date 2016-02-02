<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * User Comments
 *
 * @Table(name="comments_user", indexes={
 *   @Index(name="subid_level", columns={"entity_id", "level"}),
 *   @Index(name="subid_nestid", columns={"entity_id", "nest_level"}),
 *   @Index(name="for_comment_search", columns={"date_posted"}),
 *   @Index(name="user_id", columns={"user_id"}),
 *   @Index(name="parent_id", columns={"parent_id"})
 * })
 * @Entity
 */
class UserComment extends \FA\Doctrine\Entity
{
    /**
     * @var integer
     * @Column(name="row_id", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var integer Comment parent ID (optional).
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
     * @var integer
     * @Column(name="user_id", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $sender_id;

    /**
     * @ManyToOne(targetEntity="User", inversedBy="user_comments_sent")
     * @JoinColumns({
     *   @JoinColumn(name="user_id", referencedColumnName="userid", onDelete="CASCADE")
     * })
     */
    protected $sender;

    /**
     * @var integer
     * @Column(name="entity_id", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $recipient_id;

    /**
     * @ManyToOne(targetEntity="User", inversedBy="user_comments_received")
     * @JoinColumns({
     *   @JoinColumn(name="entity_id", referencedColumnName="userid", onDelete="CASCADE")
     * })
     */
    protected $recipient;

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
     * @var integer
     * @Column(name="date_posted", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $date_posted;

    /**
     * @var boolean
     * @Column(name="level", type="boolean", nullable=false)
     */
    protected $level;

    /**
     * @var float
     * @Column(name="nest_level", type="float", precision=10, scale=0, nullable=false)
     */
    protected $nest_level;


}
