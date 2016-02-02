<?php
namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * User Suspensions
 *
 * @Table(name="user_suspensions2", indexes={@Index(name="user_suspended", columns={"user_suspended"})})
 * @Entity
 */
class UserSuspension extends \FA\Doctrine\Entity
{
    /**
     * @var integer
     * @Column(name="row_id", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var integer
     * @Column(name="user_suspended", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $user_id;

    /**
     * @ManyToOne(targetEntity="User", inversedBy="suspensions")
     * @JoinColumns({
     *   @JoinColumn(name="user_suspended", referencedColumnName="userid", onDelete="CASCADE")
     * })
     */
    protected $user;

    /**
     * @var integer
     * @Column(name="enacted_by", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $enforcer_id;

    /**
     * @ManyToOne(targetEntity="User", inversedBy="suspensions_enforced")
     * @JoinColumns({
     *   @JoinColumn(name="enacted_by", referencedColumnName="userid", onDelete="CASCADE")
     * })
     */
    protected $enforcer;

    /**
     * @var string
     * @Column(name="reason_admin", type="text", length=65535, nullable=false)
     */
    protected $reason_admin;

    /**
     * @var string
     * @Column(name="reason_private", type="text", length=65535, nullable=false)
     */
    protected $reason_private;

    /**
     * @var string
     * @Column(name="reason_public", type="text", length=65535, nullable=false)
     */
    protected $reason_public;

    /**
     * @var integer
     * @Column(name="time_lifted", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $time_lifted;

    /**
     * @var integer
     * @Column(name="time_lifted_orig", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $time_lifted_orig;

    /**
     * @var integer
     * @Column(name="created", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $created;

}
