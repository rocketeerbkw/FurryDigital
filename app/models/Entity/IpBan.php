<?php
namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * IP Bans
 *
 * @Table(name="ip_bans")
 * @Entity
 */
class IpBan extends \FA\Doctrine\Entity
{
    /**
     * @var integer
     * @Column(name="ip", type="ip_integer", nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    protected $ip;

    /**
     * @var string
     * @Column(name="ban_type", type="string", options={"default"=""}, nullable=false)
     */
    protected $ban_type;

    /**
     * @var integer
     * @Column(name="date_created", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $date_created;

    /**
     * @var integer
     * @Column(name="date_updated", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $date_updated;

    /**
     * @var boolean
     * @Column(name="is_active", type="boolean", nullable=false)
     */
    protected $is_active;

    /**
     * @var string
     * @Column(name="public_reason", type="text", length=65535, nullable=false)
     */
    protected $public_reason;

    /**
     * @var string
     * @Column(name="admin_reason", type="text", length=65535, nullable=false)
     */
    protected $admin_reason;

    /**
     * @var integer UserID of the administrator who issued the ban.
     * @Column(name="user_id", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $user_id;

    /**
     * @ManyToOne(targetEntity="User")
     * @JoinColumns({
     *   @JoinColumn(name="user_id", referencedColumnName="userid", onDelete="CASCADE")
     * })
     */
    protected $user;

}
