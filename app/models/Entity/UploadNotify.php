<?php
namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * MessagecenterSubmissions
 *
 * @Table(name="messagecenter_submissions", indexes={@Index(name="entity_id", columns={"entity_id"})})
 * @Entity
 */
class UploadNotify extends \FA\Doctrine\Entity
{
    /**
     * @var integer
     * @Column(name="user_id", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     * @GeneratedValue(strategy="NONE")
     */
    protected $user_id;

    /**
     * @ManyToOne(targetEntity="User", inversedBy="upload_notifications")
     * @JoinColumns({
     *   @JoinColumn(name="user_id", referencedColumnName="userid", onDelete="CASCADE")
     * })
     * @Id
     */
    protected $user;

    /**
     * @var integer
     * @Column(name="entity_id", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     * @GeneratedValue(strategy="NONE")
     */
    protected $upload_id;

    /**
     * @ManyToOne(targetEntity="Upload", inversedBy="notifications")
     * @JoinColumns({
     *   @JoinColumn(name="entity_id", referencedColumnName="rowid", onDelete="CASCADE")
     * })
     * @Id
     */
    protected $upload;

    /**
     * @var integer
     * @Column(name="source_id", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $source_id;
}
