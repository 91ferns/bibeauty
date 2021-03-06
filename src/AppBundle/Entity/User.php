<?php

// src/AppBundle/Entity/User.php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="app_users")
 * @UniqueEntity("email", message="Sorry, this email address is already in use.")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\UserRepository")
 */
class User implements AdvancedUserInterface, \Serializable {

    // ...

    public function isAccountNonExpired()
    {
        return true;
    }

    public function isAccountNonLocked()
    {
        return true;
    }

    public function isCredentialsNonExpired()
    {
        return true;
    }

    public function isEnabled()
    {
        return $this->isActive;
    }

    /**
     * @ORM\OneToMany(targetEntity="Business", cascade={"remove"}, orphanRemoval=true, mappedBy="owner")
     */
    protected $businesses;

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=64)
     * @Assert\NotBlank()
     * @Assert\Length(min = 3)
     */
    private $password;

    /**
     * @ORM\Column(type="boolean")
     */
    private $superAdmin = false;

    /**
     * @ORM\Column(type="string", length=120, unique=true)
     * @Assert\NotBlank()
     * @Assert\Email
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=120)
     * @Assert\NotBlank()
     * @Assert\Length(min = 2)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=120)
     * @Assert\NotBlank()
     * @Assert\Length(min = 2)
     */
    private $lastName;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    /**
     * @ORM\Column(name="reset_token", type="string", length=120, nullable=true)
     */
    private $resetToken;

    public function __construct()
    {
        $this->isActive = true;
        // may not be needed, see section on salt below
        // $this->salt = md5(uniqid(null, true));
        $this->businesses = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add business
     *
     * @param \AppBundle\Entity\Review $business
     * @return Business
     */
    public function addBusiness(\AppBundle\Entity\Business $business)
    {
        $this->businesses[] = $business;

        return $this;
    }

    public function clearBusinesses() {
        $this->businesses = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Remove business
     *
     * @param \AppBundle\Entity\Business $businesses
     */
    public function removeBusiness(\AppBundle\Entity\Business $business)
    {
        $this->businesses->removeElement($business);
    }

    /**
     * Get businesses
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBusinesses()
    {
        return $this->businesses;
    }

    public function hasBusinesses() {
        return $this->businesses->count() > 0;
    }

    /**
     * Get reviews
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getReviews()
    {
        return $this->reviews;
    }

    public function getUsername()
    {
        return $this->email;
    }

    public function getDisplayName() {
        $firstName = $this->getFirstName();
        $lastName = $this->getLastName();

        $displayName = $firstName;

        if ($lastName) {
            $displayName .= ' ' . $lastName;
        }

        return $displayName;
    }

    public function getSalt()
    {
        // you *may* need a real salt depending on your encoder
        // see section on salt below
        return null;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getRoles()
    {
        return array('ROLE_USER');
    }

    public function eraseCredentials()
    {
    }

    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->email,
            $this->password,
            $this->isActive
            // see section on salt below
            // $this->salt,
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->email,
            $this->password,
            $this->isActive
            // see section on salt below
            // $this->salt
        ) = unserialize($serialized);
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return User
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    public function getGravatar($s = 60, $d = 'mm', $r = 'g') {
        $format = 'https://www.gravatar.com/avatar/%s?s=%s&d=%s&r=%s';
        $emailHash = md5( strtolower( trim( $this->getEmail() ) ) );
        return sprintf($format, $emailHash, $s, $d, $r);;
    }

    public function getFullname() {
        return sprintf('%s %s', $this->getFirstName(), $this->getLastName());
    }

    /**
     * Set superAdmin
     *
     * @param boolean $superAdmin
     * @return User
     */
    public function setSuperAdmin($superAdmin = false)
    {
        $this->superAdmin = $superAdmin;

        return $this;
    }

    /**
     * Get superAdmin
     *
     * @return boolean
     */
    public function getSuperAdmin()
    {
        return $this->superAdmin;
    }

    /**
     * Set resetToken
     *
     * @param string $resetToken
     * @return User
     */
    public function setResetToken($resetToken)
    {
        $this->resetToken = $resetToken;

        return $this;
    }

    /**
     * Get resetToken
     *
     * @return string
     */
    public function getResetToken()
    {
        return $this->resetToken;
    }
}
