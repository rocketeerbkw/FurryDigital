<?php
namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Gallery Folders
 *
 * @Table(name="gallery_folders", indexes={
 *   @Index(name="user_id__folder_order", columns={"user_id", "sort_order"}),
 *   @Index(name="group_id", columns={"group_id"})
 * })
 * @Entity
 * @HasLifecycleCallbacks
 */
class Folder extends \FA\Doctrine\Entity
{
    public function __construct()
    {
        $this->created_at = time();
        $this->updated_at = time();

        $this->uploads = new ArrayCollection;
    }

    /**
     * @PreUpdate
     */
    public function updating()
    {
        $this->updated_at = time();
    }

    /**
     * @var integer
     * @Column(name="row_id", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var integer
     * @Column(name="user_id", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $user_id;

    /**
     * @ManyToOne(targetEntity="User", inversedBy="folders")
     * @JoinColumns({
     *   @JoinColumn(name="user_id", referencedColumnName="userid", onDelete="CASCADE")
     * })
     */
    protected $user;

    /**
     * @var integer
     * @Column(name="group_id", type="integer", length=11, options={"unsigned"=true}, nullable=true)
     */
    protected $group_id;

    /**
     * @ManyToOne(targetEntity="FolderGroup", inversedBy="folders")
     * @JoinColumns({
     *   @JoinColumn(name="group_id", referencedColumnName="row_id", onDelete="SET NULL")
     * })
     */
    protected $group;

    /**
     * @var boolean
     * @Column(name="entity_type", type="smallint", nullable=false)
     */
    protected $type = 0;

    /**
     * @var integer
     * @Column(name="sort_order", type="smallint", nullable=false)
     */
    protected $sort_order = 0;

    /**
     * @var integer
     * @Column(name="num_files", type="smallint", nullable=false)
     */
    protected $num_files = 0;

    /**
     * @var boolean
     * @Column(name="icon_type", type="smallint", nullable=false)
     */
    protected $icon_type = 0;

    /**
     * @var string
     * @Column(name="entity_name", type="string", options={"default"=""}, length=64, nullable=false)
     */
    protected $name;

    /**
     * @var string
     * @Column(name="entity_description", type="text", length=65535, nullable=true)
     */
    protected $description = '';

    /**
     * @var integer
     * @Column(name="date_created", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $created_at;

    /**
     * @var integer
     * @Column(name="date_updated", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $updated_at;

    /**
     * @OneToMany(targetEntity="UploadFolder", mappedBy="folder")
     */
    protected $uploads;

    /**
     * Static Functions
     */

    /**
     * Get a multidimensional array with all folders (in groups) for a user.
     *
     * @param $user_id
     * @param bool|false $add_blank
     * @return array|bool
     */

    public static function fetchSelectWithGroups($user_id, $add_blank = false)
    {
        $select = array();

        if ($add_blank !== false)
            $select[''] = ($add_blank === true) ? 'Select...' : $add_blank;

        $em = self::getEntityManager();

        $folders_raw = $em->createQuery('SELECT f, fg FROM '.__CLASS__.' f LEFT JOIN f.group fg WHERE f.user_id = :user_id ORDER BY fg.sort_order ASC, fg.name ASC, f.sort_order ASC, f.name ASC')
            ->setParameter('user_id', $user_id)
            ->getArrayResult();

        foreach((array)$folders_raw as $folder)
        {
            if ($folder['group'])
            {
                $group_name = $folder['group']['name'];
                $select[$group_name][$folder['id']] = $folder['name'];
            }
            else
            {
                $select['Other Folders'][$folder['id']] = $folder['name'];
            }
        }

        return $select;
    }
}
