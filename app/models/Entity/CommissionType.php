<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Commission Types
 *
 * @Table(name="commission_types", indexes={
 *   @Index(name="user_id_display_order", columns={"user_id", "display_order"})
 * })
 * @Entity
 */
class CommissionType extends \FA\Doctrine\Entity
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
     * @Column(name="user_id", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $user_id;

    /**
     * @ManyToOne(targetEntity="User", inversedBy="commission_types")
     * @JoinColumns({
     *   @JoinColumn(name="user_id", referencedColumnName="userid", onDelete="CASCADE")
     * })
     */
    protected $user;

    /**
     * @var integer
     * @Column(name="display_order", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $display_order;

    /**
     * @var string
     * @Column(name="currency", type="string", options={"default"=""}, length=3, nullable=false)
     */
    protected $currency;

    /**
     * @var string
     * @Column(name="price_lower", type="decimal", precision=7, scale=2, nullable=false)
     */
    protected $price_lower;

    /**
     * @var string
     * @Column(name="price_upper", type="decimal", precision=7, scale=2, nullable=false)
     */
    protected $price_upper;

    /**
     * @var boolean
     * @Column(name="is_addon", type="boolean", nullable=false)
     */
    protected $is_addon;

    /**
     * @var string
     * @Column(name="type_name", type="string", options={"default"=""}, length=250, nullable=false)
     */
    protected $type_name;

    /**
     * @var string
     * @Column(name="description", type="text", length=16777215, nullable=false)
     */
    protected $description;

    /**
     * @var integer
     * @Column(name="example_id", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $example_id;

    /**
     * @var integer
     * @Column(name="slots", type="integer", length=11, options={"unsigned"=true}, nullable=true)
     */
    protected $slots;

}
