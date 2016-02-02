<?php
namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * TODO: Unused Table in new submission system.
 * Temporary Submissions
 *
 * @Table(name="submissions_tmp", indexes={@Index(name="userid_date", columns={"userid", "date"})})
 * @Entity
 */
class UploadTemp extends \FA\Doctrine\Entity
{
    /**
     * @var integer
     * @Column(name="rowid", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var integer
     * @Column(name="userid", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $user_id;

    /**
     * @ManyToOne(targetEntity="User", inversedBy="uploads_temp")
     * @JoinColumns({
     *   @JoinColumn(name="userid", referencedColumnName="userid", onDelete="CASCADE")
     * })
     */
    protected $user;

    /**
     * @var integer
     * @Column(name="date", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $date;

    /**
     * @var string
     * @Column(name="title", type="string", options={"default"=""}, length=60, nullable=false)
     */
    protected $title;

    /**
     * @var string
     * @Column(name="submission", type="string", options={"default"=""}, length=200, nullable=false)
     */
    protected $upload;

    /**
     * @var string
     * @Column(name="thumbnail", type="string", options={"default"=""}, length=200, nullable=false)
     */
    protected $thumbnail;

    /**
     * @var string
     * @Column(name="submissiontype", type="string", options={"default"=""}, length=25, nullable=false)
     */
    protected $upload_type;

    /**
     * @var string
     * @Column(name="category", type="string", options={"default"=""}, length=50, nullable=false)
     */
    protected $category;

    /**
     * @var string
     * @Column(name="gender", type="string", options={"default"=""}, length=30, nullable=false)
     */
    protected $gender;

    /**
     * @var string
     * @Column(name="species", type="string", options={"default"=""}, length=40, nullable=false)
     */
    protected $species;

    /**
     * @var string
     * @Column(name="tag", type="text", length=65535, nullable=false)
     */
    protected $tag;

    /**
     * @var string
     * @Column(name="message", type="text", length=65535, nullable=false)
     */
    protected $message;

}