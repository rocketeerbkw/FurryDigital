<?php
namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Admin Actions Audit Log
 *
 * @Table(name="df_adminactions")
 * @Entity
 */
class AuditAdminAction extends \FA\Doctrine\Entity
{
    public function __construct()
    {
        $this->date = time();

    }

    /**
     * @var integer
     * @Column(name="rowid", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var integer
     * @Column(name="date", type="integer", length=11, options={"unsigned"=true, "default"=0}, nullable=false)
     */
    protected $date;

    /**
     * @var integer
     * @Column(name="user", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $user_id;

    /**
     * @ManyToOne(targetEntity="User", inversedBy="admin_actions")
     * @JoinColumns({
     *   @JoinColumn(name="user", referencedColumnName="userid", onDelete="CASCADE")
     * })
     */
    protected $user;

    /**
     * @var string
     * @Column(name="username", type="string", options={"default"=""}, length=30, nullable=false)
     */
    protected $username;

    /**
     * @var string
     * @Column(name="lower", type="string", options={"default"=""}, length=30, nullable=false)
     */
    protected $lower;

    /**
     * @var string
     * @Column(name="title", type="string", options={"default"=""}, length=60, nullable=false)
     */
    protected $title;

    /**
     * @var string
     * @Column(name="message", type="text", length=65535, nullable=false)
     */
    protected $message;

    /**
     * @var string
     * @Column(name="cc_user_pm", type="text", length=65535, nullable=false)
     */
    protected $cc_user_pm;

    /**
     * Log an administrator action for later auditing.
     *
     * @param $title
     * @param $message
     * @param string $reason
     * @return AuditAdminAction
     */
    public static function log($title, $message, $reason='')
    {
        $di = \Phalcon\Di::getDefault();

        $user = $di->get('auth')->getLoggedInUser();

        $record = new self;
        $record->user = $user;
        $record->username = $user->username;
        $record->lower = $user->lower;
        $record->title = $title;
        $record->message = $message;

        if ($reason)
            $record->cc_user_pm = $reason;

        $record->save();

        return $record;
    }

}
