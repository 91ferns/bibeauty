<?php

// src/AppBundle/Entity/Booking.php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass="BookingRepository")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="app_bookings")
 */
class Booking {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min = 3)
     * @Assert\NotBlank()
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Email
     * @Assert\NotBlank()
     */
    protected $email;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min = 9)
     * @Assert\NotBlank()
     */
    protected $phone;

    /**
     * @ORM\ManyToOne(targetEntity="TreatmentAvailabilitySet")
     * @ORM\JoinColumn(name="availability_id", referencedColumnName="id")
     */
    protected $availability_id;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user_id;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $updated;

    /**
     * @ORM\Column(type="integer")
     */
    protected $approval = 1;
    // 1 = unapproved
    // 4 = approved
    // 3 = cancelled
    // 2 = Declined

    /**
     * @ORM\ManyToOne(targetEntity="Business")
     * @ORM\JoinColumn(name="business_id", referencedColumnName="id")
     */
    protected $businessId;

    /**
     * @ORM\ManyToOne(targetEntity="Service")
     * @ORM\JoinColumn(name="service_id", referencedColumnName="id")
     */
    protected $serviceId;

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
     * Set availability_id
     *
     * @param \AppBundle\Entity\TreatmentAvailabilitySet $availabilityId
     * @return Booking
     */
    public function setAvailabilityId(\AppBundle\Entity\TreatmentAvailabilitySet $availabilityId = null)
    {
        $this->availability_id = $availabilityId;

        return $this;
    }

    /**
     * Get availability_id
     *
     * @return \AppBundle\Entity\TreatmentAvailabilitySet
     */
    public function getAvailabilityId()
    {
        return $this->availability_id;
    }

    /**
     * Set user_id
     *
     * @param \AppBundle\Entity\User $userId
     * @return Booking
     */
    public function setUserId(\AppBundle\Entity\User $userId = null)
    {
        $this->user_id = $userId;

        return $this;
    }

    /**
     * Get user_id
     *
     * @return \AppBundle\Entity\User
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * Set businessId
     *
     * @param \AppBundle\Entity\Business $businessId
     * @return Booking
     */
    public function setBusinessId(\AppBundle\Entity\Business $businessId = null)
    {
        $this->businessId = $businessId;

        return $this;
    }

    /**
     * Get businessId
     *
     * @return \AppBundle\Entity\Business
     */
    public function getBusinessId()
    {
        return $this->businessId;
    }

    /**
     * Set serviceId
     *
     * @param \AppBundle\Entity\Service $serviceId
     * @return Booking
     */
    public function setServiceId(\AppBundle\Entity\Service $serviceId = null)
    {
        $this->serviceId = $serviceId;

        return $this;
    }

    /**
     * Get serviceId
     *
     * @return \AppBundle\Entity\Service 
     */
    public function getServiceId()
    {
        return $this->serviceId;
    }
}
