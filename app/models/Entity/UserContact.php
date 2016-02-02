<?php
namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * User Contacts
 *
 * @Table(name="user_contacts")
 * @Entity
 */
class UserContact extends \FA\Doctrine\Entity
{
    /**
     * @var integer
     * @Column(name="user_id", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    protected $user_id;

    /**
     * @OneToOne(targetEntity="User", inversedBy="contact")
     * @JoinColumn(name="user_id", referencedColumnName="userid", onDelete="CASCADE")
     */
    protected $user;

    /**
     * @var string
     * @Column(name="website", type="string", options={"default"=""}, length=255, nullable=false)
     */
    protected $website = '';

    /**
     * @var string
     * @Column(name="youtube", type="string", options={"default"=""}, length=255, nullable=false)
     */
    protected $youtube = '';

    /**
     * @var string
     * @Column(name="aim", type="string", options={"default"=""}, length=255, nullable=false)
     */
    protected $aim = '';

    /**
     * @var string
     * @Column(name="yim", type="string", options={"default"=""}, length=255, nullable=false)
     */
    protected $yim = '';

    /**
     * @var string
     * @Column(name="icq", type="string", options={"default"=""}, length=32, nullable=false)
     */
    protected $icq = '';

    /**
     * @var string
     * @Column(name="twitter", type="string", options={"default"=""}, length=255, nullable=false)
     */
    protected $twitter = '';

    /**
     * @var string
     * @Column(name="steam", type="string", options={"default"=""}, length=255, nullable=false)
     */
    protected $steam = '';

    /**
     * @var string
     * @Column(name="imvu", type="string", options={"default"=""}, length=255, nullable=false)
     */
    protected $imvu = '';

    /**
     * @var string
     * @Column(name="xbl", type="string", options={"default"=""}, length=255, nullable=false)
     */
    protected $xbl = '';

    /**
     * @var string
     * @Column(name="secondlife", type="string", options={"default"=""}, length=255, nullable=false)
     */
    protected $secondlife = '';

    /**
     * @var string
     * @Column(name="psn", type="string", options={"default"=""}, length=255, nullable=false)
     */
    protected $psn = '';

    /**
     * @var string
     * @Column(name="three_ds", type="string", options={"default"=""}, length=255, nullable=false)
     */
    protected $threeDs = '';

    /**
     * @var string
     * @Column(name="nintendo", type="string", options={"default"=""}, length=255, nullable=false)
     */
    protected $nintendo = '';

    /**
     * @var string
     * @Column(name="skype", type="string", options={"default"=""}, length=255, nullable=false)
     */
    protected $skype = '';

    /**
     * @var string
     * @Column(name="jabber", type="string", options={"default"=""}, length=255, nullable=false)
     */
    protected $jabber = '';

    /**
     * @var string
     * @Column(name="xfire", type="string", options={"default"=""}, length=255, nullable=false)
     */
    protected $xfire = '';

    /**
     * @var string
     * @Column(name="raptr", type="string", options={"default"=""}, length=255, nullable=false)
     */
    protected $raptr = '';

    /**
     * @var string
     * @Column(name="dealersden", type="string", options={"default"=""}, length=255, nullable=false)
     */
    protected $dealersden = '';

    /**
     * @var string
     * @Column(name="etsy", type="string", options={"default"=""}, length=255, nullable=false)
     */
    protected $etsy = '';

    /**
     * @var string
     * @Column(name="furbuy", type="string", options={"default"=""}, length=255, nullable=false)
     */
    protected $furbuy = '';

    /**
     * @var string
     * @Column(name="patreon", type="string", options={"default"=""}, length=255, nullable=false)
     */
    protected $patreon = '';

    /**
     * @var string
     * @Column(name="ustream", type="string", options={"default"=""}, length=255, nullable=false)
     */
    protected $ustream = '';

    /**
     * @var string
     * @Column(name="livestream", type="string", options={"default"=""}, length=255, nullable=false)
     */
    protected $livestream = '';

    /**
     * @var string
     * @Column(name="lj", type="string", options={"default"=""}, length=255, nullable=false)
     */
    protected $lj = '';

    /**
     * @var string
     * @Column(name="facebook", type="string", options={"default"=""}, length=255, nullable=false)
     */
    protected $facebook = '';

    /**
     * @var string
     * @Column(name="sofurry", type="string", options={"default"=""}, length=255, nullable=false)
     */
    protected $sofurry = '';

    /**
     * @var string
     * @Column(name="inkbunny", type="string", options={"default"=""}, length=255, nullable=false)
     */
    protected $inkbunny = '';

    /**
     * @var string
     * @Column(name="deviantart", type="string", options={"default"=""}, length=255, nullable=false)
     */
    protected $deviantart = '';

    /**
     * @var string
     * @Column(name="weasyl", type="string", options={"default"=""}, length=255, nullable=false)
     */
    protected $weasyl = '';

    /**
     * @var string
     * @Column(name="tumblr", type="string", options={"default"=""}, length=255, nullable=false)
     */
    protected $tumblr = '';

    /**
     * @var string
     * @Column(name="transfur", type="string", options={"default"=""}, length=255, nullable=false)
     */
    protected $transfur = '';

    /**
     * @var string
     * @Column(name="nabyn", type="string", options={"default"=""}, length=255, nullable=false)
     */
    protected $nabyn = '';

}
