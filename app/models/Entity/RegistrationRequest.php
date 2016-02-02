<?php
namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * RegistrationRequests
 *
 * @Table(name="registration_requests", uniqueConstraints={
 *   @UniqueConstraint(name="username", columns={"username"})
 * },indexes={
 *   @Index(name="confirmation_code__date_created", columns={"confirmation_code", "date_created"}),
 *   @Index(name="temp_sid", columns={"temp_sid"})
 * })
 * @Entity
 */
class RegistrationRequest extends \FA\Doctrine\Entity
{
    public function __construct()
    {
        $this->confirmation_code = \FA\Legacy\Utilities::uuid();
        $this->ip = $_SERVER['REMOTE_ADDR'];
        $this->created_at = time();
        $this->is_used = false;
    }

    /**
     * @var integer
     *
     * @Column(name="row_id", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string UUID
     * @Column(name="confirmation_code", type="binary_uuid", nullable=false)
     */
    protected $confirmation_code;

    /**
     * @var string UUID
     * @Column(name="temp_sid", type="binary_uuid", nullable=false)
     */
    protected $temp_sid;

    /**
     * @var integer
     * @Column(name="ip", type="ip_integer", nullable=false)
     */
    protected $ip;

    /**
     * @var string
     * @Column(name="username", type="string", options={"default"=""}, length=30, nullable=false)
     */
    protected $username;

    /**
     * Set the username and lower-case version together.
     * @param $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
        $this->lower = User::getLowerCase($username);
    }

    /**
     * @var string
     * @Column(name="lower", type="string", options={"default"=""}, length=30, nullable=false)
     */
    protected $lower;

    /**
     * @var string
     * @Column(name="email", type="string", options={"default"=""}, length=255, nullable=false)
     */
    protected $email;

    /**
     * @var integer
     * @Column(name="date_created", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $created_at;

    /**
     * @var boolean
     * @Column(name="is_used", type="boolean", nullable=false)
     */
    protected $is_used;

    /**
     * Validate a confirmation code and return the registration object if available.
     *
     * @param $code
     * @return bool|null|object
     */
    public static function validate($code)
    {
        $code_bin = \FA\Doctrine\Type\BinaryUuid::uuidToBin($code);
        $record = self::getRepository()->findOneBy(array('confirmation_code' => $code, 'is_used' => FALSE));

        if ($record instanceof self)
        {
            $threshold = time()-86400;
            if ($record->created_at >= $threshold)
                return $record;
        }

        return false;
    }
}
