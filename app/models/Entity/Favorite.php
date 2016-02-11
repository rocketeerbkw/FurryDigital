<?php
namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Favorites
 *
 * @Table(name="favorites", indexes={
 *   @Index(name="subid_user", columns={"submission_id", "user_id"}),
 *   @Index(name="user", columns={"user_id"})
 * })
 * @Entity
 * @HasLifecycleCallbacks
 */
class Favorite extends \App\Doctrine\Entity
{
    public function __construct()
    {
        $this->created_at = time();

        $this->notifications = new ArrayCollection;
    }

    /**
     * @PostPersist
     */
    public function created()
    {
        User::incrementCounter('favorites', $this->upload->user_id);
        \App\Legacy\Notifications::dispatch('favorite', $this->id, $this->upload->user_id, $this->upload->id);
    }

    /**
     * @PreDelete
     */
    public function deleted()
    {
        User::decrementCounter('favorites', $this->upload->user_id);
        \App\Legacy\Notifications::purge('favorite', $this->id, $this->upload->user_id);
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
     * @ManyToOne(targetEntity="User", inversedBy="favorites")
     * @JoinColumns({
     *   @JoinColumn(name="user_id", referencedColumnName="userid", onDelete="CASCADE")
     * })
     */
    protected $user;

    /**
     * @var integer
     * @Column(name="submission_id", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $upload_id;

    /**
     * @ManyToOne(targetEntity="Upload", inversedBy="favorites")
     * @JoinColumns({
     *   @JoinColumn(name="submission_id", referencedColumnName="rowid", onDelete="CASCADE")
     * })
     */
    protected $upload;

    /**
     * @var integer
     * @Column(name="date_created", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $created_at;

    /**
     * @OneToMany(targetEntity="FavoriteNotify", mappedBy="favorite")
     */
    protected $notifications;
}
