<?php
namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Submission Folders
 *
 * @Table(name="gallery_folders_submissions", indexes={
 *   @Index(name="submission_id", columns={"submission_id"})
 * })
 * @Entity
 * @HasLifecycleCallbacks
 */
class UploadFolder extends \FA\Doctrine\Entity
{
    public function __construct()
    {
        $this->created_at = time();
    }

    /**
     * @PrePersist
     */
    public function creating()
    {
        $em = self::getEntityManager();

        $em->createQuery('UPDATE Entity\Folder f SET f.num_files=f.num_files+1 WHERE f.id = :folder_id')
            ->setParameter('folder_id', $this->folder->id)
            ->execute();
    }

    /**
     * @PreRemove
     */
    public function deleting()
    {
        $em = self::getEntityManager();

        $em->createQuery('UPDATE Entity\Folder f SET f.num_files=IF(f.num_files>0,f.num_files-1,0) WHERE f.id = :folder_id')
            ->setParameter('folder_id', $this->folder_id)
            ->execute();
    }

    /**
     * @var integer
     * @Column(name="folder_id", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     * @GeneratedValue(strategy="NONE")
     */
    protected $folder_id;

    /**
     * @ManyToOne(targetEntity="Folder", inversedBy="uploads")
     * @JoinColumns({
     *   @JoinColumn(name="folder_id", referencedColumnName="row_id", onDelete="CASCADE")
     * })
     * @Id
     */
    protected $folder;

    /**
     * @var integer
     * @Column(name="submission_id", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     * @GeneratedValue(strategy="NONE")
     */
    protected $upload_id;

    /**
     * @ManyToOne(targetEntity="Upload", inversedBy="folders")
     * @JoinColumns({
     *   @JoinColumn(name="submission_id", referencedColumnName="rowid", onDelete="CASCADE")
     * })
     * @Id
     */
    protected $upload;

    /**
     * @var integer
     * @Column(name="submission_order", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $upload_order = 0;

    /**
     * @var integer
     * @Column(name="date_created", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $created_at;

}
