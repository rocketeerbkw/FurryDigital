<?php
namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use OAuth\Common\Service\ServiceInterface;
use OAuth\UserData\ExtractorFactory;

/**
 * @Table(name="users_external")
 * @Entity
 */
class UserExternal extends \App\Doctrine\Entity
{
    public function __construct()
    {}

    /**
     * @Column(name="provider", type="string", length=50)
     * @Id
     */
    protected $provider;

    /**
     * @Column(name="external_id", type="string", length=100)
     * @Id
     */
    protected $external_id;

    /** @Column(name="user_id", type="integer", length=11, options={"unsigned"=true}, nullable=false) */
    protected $user_id;

    /** @Column(name="name", type="string", length=255, nullable=true) */
    protected $name;

    public function getName()
    {
        if ($this->name)
            return $this->name;
        else
            return $this->user->name;
    }

    /** @Column(name="avatar_url", type="string", length=255, nullable=true) */
    protected $avatar_url;

    /**
     * @ManyToOne(targetEntity="User", inversedBy="external_accounts")
     * @JoinColumns({
     *   @JoinColumn(name="user_id", referencedColumnName="userid", onDelete="CASCADE")
     * })
     */
    protected $user;

    /**
     * Static Functions
     */

    /**
     * Handle external OAuth1/2 authentication.
     *
     * @param $provider
     * @param \OAuth\Common\Service\ServiceInterface $service
     * @param \Entity\User|null $user
     * @return \Entity\User|null|object
     * @throws \App\Exception
     * @throws \App\Exception\AccountNotLinked
     * @throws \OAuth\UserData\Exception\UndefinedExtractorException
     */
    public static function processExternal($provider, ServiceInterface $service, User $user = null)
    {
        $extractorFactory = new ExtractorFactory();
        $extractor = $extractorFactory->get($service);

        if (!$extractor->supportsUniqueId())
            throw new \App\Exception('External provider has no unique ID.');

        $external = self::getRepository()->findOneBy(array('provider' => $provider, 'external_id' => $extractor->getUniqueId()));

        // Locate a user account to associate.
        if ($user instanceof User)
        {
            // No additional processing.
        }
        elseif ($external instanceof self && $external->user instanceof User)
        {
            $user = $external->user;
        }
        elseif ($extractor->supportsEmail())
        {
            $user = User::getRepository()->findOneBy(array('email' => $extractor->getEmail()));

            if (!($user instanceof User))
            {
                $user = new User;
                $user->email = $extractor->getEmail();
                $user->name = $extractor->getUsername();
                $user->avatar_url = $extractor->getImageUrl();
                $user->generateRandomPassword();
                $user->save();
            }
        }
        else
        {
            // Not enough information to auto-create account; throw exception.
            throw new \App\Exception\AccountNotLinked;
        }

        // Create new external record (if none exists)
        if (!($external instanceof self))
        {
            // Create new external account and associate with the specified user.
            $external = new self;
            $external->provider = $provider;
            $external->external_id = $extractor->getUniqueId();
        }

        $external->user = $user;
        $external->name = $extractor->getUsername();
        $external->avatar_url = $extractor->getImageUrl();
        $external->save();

        return $user;
    }
}
