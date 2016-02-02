<?php
namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * UserVariables
 *
 * @Table(name="user_variables", indexes={
 *   @Index(name="var_id", columns={"var_id"})
 * })
 * @Entity
 */
class UserVariable extends \FA\Doctrine\Entity
{
    /**
     * @var integer
     * @Column(name="user_id", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $user_id;

    /**
     * @ManyToOne(targetEntity="User", inversedBy="vars")
     * @Id
     * @JoinColumns({
     *   @JoinColumn(name="user_id", referencedColumnName="userid", onDelete="CASCADE")
     * })
     */
    protected $user;

    /**
     * @var integer
     * @Column(name="var_id", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     * @Id
     */
    protected $var_id;

    /**
     * @var string
     * @Column(name="content", type="blob", length=65535, nullable=false)
     */
    protected $content;

    public function getContent()
    {
        return (is_resource($this->content)) ? stream_get_contents($this->content, -1) : $this->content;
    }

    public function getValue()
    {
        return $this->getContent();
    }

    /**
     * Return list of all variable definitions.
     *
     * @return mixed
     */
    public static function getDefinitions()
    {
        static $var_definitions;

        if (!$var_definitions)
        {
            $di = \Phalcon\Di::getDefault();
            $var_definitions = $di['config']->fa->user_variables->toArray();
        }

        return $var_definitions;
    }
}