<?php

namespace AppBundle\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\Common\Collections\ArrayCollection;
use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *   itemOperations={
 *     "get"={"method"="GET"},
 *   },
 *   collectionOperations={
 *     "me"={ "route_name"="me", "normalization_context"={ "groups"={"user", "place"} } }
 *   },
 *   attributes={
 *     "normalization_context"={ "groups"={"user", "order"} }
 *   }
 * )
 * @UniqueEntity("email")
 * @UniqueEntity("username")
 * @UniqueEntity("googleId")
 */
class ApiUser extends BaseUser implements JWTUserInterface
{
    protected $id;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(min="3", max="15")
     * @Assert\Regex(pattern="/^[a-zA-Z0-9_]{3,15}$/")
     * @var string
     */
    protected $username;

    /**
     * @Assert\NotBlank()
     * @var string
     */
    protected $email;

    /**
     * @Groups({"order"})
     * @Assert\NotBlank()
     * @ApiProperty(iri="https://schema.org/givenName")
    */
    protected $givenName;

    /**
     * @Groups({"order"})
     * @Assert\NotBlank()
     * @ApiProperty(iri="https://schema.org/familyName")
     */
    protected $familyName;

    /**
     * @Groups({"order"})
     * @Assert\NotBlank()
     * @ApiProperty(iri="https://schema.org/telephone")
     */
    protected $telephone;

    /**
     * @var string
     */
    protected $googleId;

    /**
     * @var string
     */
    protected $googleAccessToken;

    private $restaurants;

    private $stores;

    private $addresses;

    private $stripeAccounts;

    public function __construct()
    {
        $this->addresses = new ArrayCollection();
        $this->restaurants = new ArrayCollection();
        $this->stores = new ArrayCollection();
        $this->stripeAccounts = new ArrayCollection();

        parent::__construct();
    }

    /**
     * @return mixed
     */
    public function getGivenName()
    {
        return $this->givenName;
    }

    /**
     * @param mixed $givenName
     */
    public function setGivenName($givenName)
    {
        $this->givenName = $givenName;
    }

    /**
     * @return mixed
     */
    public function getFamilyName()
    {
        return $this->familyName;
    }

    /**
     * @param mixed $familyName
     */
    public function setFamilyName($familyName)
    {
        $this->familyName = $familyName;
    }

    /**
     * @return mixed
     */
    public function getTelephone()
    {
        return $this->telephone;
    }

    /**
     * @param mixed $telephone
     */
    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;
    }

    /**
     * @return string
     */
    public function getGoogleId()
    {
        return $this->googleId;
    }

    /**
     * @param string $googleId
     */
    public function setGoogleId($googleId) {
        $this->googleId = $googleId;
    }

    public function setGoogleAccessToken($googleAccessToken)
    {
        $this->googleAccessToken = $googleAccessToken;
    }

    public function getGoogleAccessToken()
    {
        return $this->googleAccessToken;
    }

    public function setRestaurants($restaurants)
    {
        $this->restaurants = $restaurants;

        return $this;
    }

    public function addRestaurant(Restaurant $restaurant)
    {
        $this->restaurants->add($restaurant);

        return $this;
    }

    public function ownsRestaurant(Restaurant $restaurant)
    {
        return $this->restaurants->contains($restaurant);
    }

    public function getRestaurants()
    {
        return $this->restaurants;
    }

    public function setStores($stores)
    {
        $this->stores = $stores;

        return $this;
    }

    public function addStore(Store $store)
    {
        $this->stores->add($store);

        return $this;
    }

    public function ownsStore(Store $store)
    {
        return $this->stores->contains($store);
    }

    public function getStores()
    {
        return $this->stores;
    }

    public function addAddress(Address $addresses)
    {
        $this->addresses->add($addresses);

        return $this;
    }

    public function setAddresses($addresses)
    {
        $this->addresses = $addresses;

        return $this;
    }

    public function getAddresses()
    {
        return $this->addresses;
    }

    public function getStripeAccounts()
    {
        return $this->stripeAccounts;
    }

    public function addStripeAccount(StripeAccount $stripeAccount)
    {
        $this->stripeAccounts->add($stripeAccount);

        return $this;
    }

    public function getFullName() {
        return join(' ', [$this->givenName, $this->familyName]);
    }

    public static function createFromPayload($username, array $payload)
    {
        $user = new self();
        $user->setUsername($payload['username']);
        if (isset($payload['roles'])) {
            $user->setRoles($payload['roles']);
        }

        return $user;
    }
}
