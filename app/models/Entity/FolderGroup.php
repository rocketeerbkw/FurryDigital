<?php
namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Gallery Folder Groups
 *
 * @Table(name="gallery_folder_groups", indexes={
 *   @Index(name="user_id__section_order", columns={"user_id", "sort_order"})
 * })
 * @Entity
 */
class FolderGroup extends \FA\Doctrine\Entity
{
    public function __construct()
    {
        $this->created_at = time();
        $this->updated_at = time();

        $this->folders = new ArrayCollection;
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
     * @ManyToOne(targetEntity="User", inversedBy="folder_groups")
     * @JoinColumns({
     *   @JoinColumn(name="user_id", referencedColumnName="userid", onDelete="CASCADE")
     * })
     */
    protected $user;

    /**
     * @var string
     * @Column(name="entity_name", type="string", options={"default"=""}, length=64, nullable=false)
     */
    protected $name;

    /**
     * @var integer
     * @Column(name="sort_order", type="smallint", nullable=false)
     */
    protected $sort_order;

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
     * @OneToMany(targetEntity="Folder", mappedBy="group")
     */
    protected $folders;

    /*
     * Static Functions
     */

    /**
     * @param $user_id
     * @param bool|false $add_blank
     * @return array
     */
    public static function fetchSelect($user_id, $add_blank = false)
    {
        $select = array();

        if ($add_blank !== false)
            $select[''] = ($add_blank === true) ? 'Select...' : $add_blank;

        $em = self::getEntityManager();

        $groups_raw = $em->createQuery('SELECT fg FROM '.__CLASS__.' fg WHERE fg.user_id = :user_id ORDER BY fg.sort_order ASC, fg.name ASC')
            ->setParameter('user_id', $user_id)
            ->getArrayResult();

        foreach((array)$groups_raw as $group)
            $select[$group['id']] = $group['name'];

        return $select;
    }
}