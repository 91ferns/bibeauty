<?php

// src/AppBundle/Entity/Offer.php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass="OfferRepository")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="app_offers")
 */
class Offer {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, options={"default" = null})
     * @Assert\Length(min = 3)
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, options={"default" = null})
     * @Assert\Email
     */
    protected $email;

    /**
     * @ORM\Column(type="string", length=255,  nullable=true, options={"default" = null})
     * @Assert\Length(min = 9)
     */
    protected $phone;

    /**
     * @ORM\OneToOne(targetEntity="OfferAvailabilitySet", mappedBy="offer",cascade={"remove"})
     */
    protected $availabilitySet;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $updated;

    /**
     * @ORM\Column(type="float", length=8)
     * @Assert\NotBlank()
     * @Assert\GreaterThan(
     *     value = 0.00
     * )
     */
    protected $currentPrice;

    /**
     * @ORM\Column(type="boolean")
     * @Assert\NotBlank()
     */
    private $isOpen = true;

    /**
     * @ORM\ManyToOne(targetEntity="Business")
     * @ORM\JoinColumn(name="business_id", referencedColumnName="id")
     */
    protected $business;

    /**
     * @ORM\ManyToOne(targetEntity="Treatment", inversedBy="offers")
     * @ORM\JoinColumn(name="treatment_id", referencedColumnName="id")
     */
    protected $treatment;

    /**
     * @ORM\PrePersist
     */
    public function setAutomaticFields() {
        if (!$this->createdAt) {
            $this->createdAt = new \DateTime();
        }
        $this->updated = new \DateTime();
    }

    /**
     * @ORM\PreUpdate
     */
    public function setUpdated() {
        // will NOT be saved in the database
        $this->updated->modify("now");
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
     * Set name
     *
     * @param string $name
     * @return Booking
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Booking
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
     * Set phone
     *
     * @param string $phone
     * @return Booking
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Booking
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Get updated
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set approval
     *
     * @param integer $approval
     * @return Booking
     */
    public function setApproval($approval)
    {
        $this->approval = $approval;

        return $this;
    }

    /**
     * Get approval
     *
     * @return integer
     */
    public function getApproval()
    {
        return $this->approval;
    }

    /**
     * Set availability
     *
     * @param \AppBundle\Entity\OfferAvailabilitySet $availability
     * @return Booking
     */
    public function setAvailabilitySet(\AppBundle\Entity\OfferAvailabilitySet $availabilitySet = null)
    {
        $this->availabilitySet = $availabilitySet;

        return $this;
    }

    /**
     * Get availability
     *
     * @return \AppBundle\Entity\OfferAvailabilitySet
     */
    public function getAvailabilitySet()
    {
        return $this->availabilitySet;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     * @return Booking
     */
    public function setUser(\AppBundle\Entity\User $user = null)
    {

        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {

        if ($this->user) {
            return $this->user;
        } else {
            return 'None';
        }
    ;
    }

    /**
     * Set business
     *
     * @param \AppBundle\Entity\Business $business
     * @return Booking
     */
    public function setBusiness(\AppBundle\Entity\Business $business = null)
    {
        $this->business = $business;

        return $this;
    }

    /**
     * Get businessId
     *
     * @return \AppBundle\Entity\Business
     */
    public function getBusiness()
    {
        return $this->business;
    }

    /**
     * Set treatment
     *
     * @param \AppBundle\Entity\Treatment $treatment
     * @return Booking
     */
    public function setTreatment(\AppBundle\Entity\Treatment $treatment = null)
    {
        $this->treatment = $treatment;

        return $this;
    }

    /**
     * Get treatment
     *
     * @return \AppBundle\Entity\Treatment
     */
    public function getTreatment()
    {
        return $this->treatment;
    }

    /**
     * Set currentPrice
     *
     * @param float $currentPrice
     * @return Service
     */
    public function setCurrentPrice($currentPrice)
    {
        $this->currentPrice = $currentPrice;

        return $this;
    }

    /**
     * Get currentPrice
     *
     * @return float
     */
    public function getCurrentPrice()
    {
        return $this->currentPrice;
    }

    public function getPercentageSaved() {
        $divided = ($this->getOriginalPrice() - $this->getCurrentPrice()) / $this->getOriginalPrice();
        $divided = $divided * 100;

        return number_format($divided, 0);
    }

    /**
     * Set isOpen
     *
     * @param boolean $isOpen
     * @return Offer
     */
    public function setIsOpen($isOpen)
    {
        $this->isOpen = $isOpen;

        return $this;
    }

    /**
     * Get isOpen
     *
     * @return boolean
     */
    public function getIsOpen()
    {
        return $this->isOpen;
    }

    public function getDiscountPercentage() {
        $originalPrice = $this->getTreatment()->getOriginalPrice();
        $newPrice = $this->getCurrentPrice();

        return sprintf('%d', (($originalPrice - $newPrice) / $originalPrice) * 100);

    }
}
