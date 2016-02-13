<?php
namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * MessagecenterJournals
 *
 * @Table(name="journal_notify", indexes={@Index(name="entity_id", columns={"entity_id"})})
 * @Entity
 */
class JournalNotify extends \App\Doctrine\Entity
{
    use Traits\NotifyTrait;
    protected static $identifier = 'journal_id';

    /**
     * @var integer
     * @Column(name="user_id", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     * @Id
     * @GeneratedValue(strategy="NONE")
     */
    protected $user_id;

    /**
     * @ManyToOne(targetEntity="User", inversedBy="journal_notifications")
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
    protected $journal_id;

    /**
     * @ManyToOne(targetEntity="Journal", inversedBy="notifications")
     * @JoinColumns({
     *   @JoinColumn(name="entity_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    protected $journal;

    /**
     * @var integer
     * @Column(name="source_id", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $source_id;

}