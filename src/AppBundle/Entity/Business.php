<?php

// src/AppBundle/Entity/Business.php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="app_businesses")
 */
class Business {
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $name;

    /**
     * @ORM\OneToOne(targetEntity="Address", cascade={"persist", "remove"}, orphanRemoval=true, fetch="EAGER")
     * @ORM\JoinColumn(name="address_id", referencedColumnName="id")
     */
    protected $address;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $yelpId;

    /**
     * @ORM\Column(type="text")
     */
    protected $description;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $owner;

    /**
     * @ORM\OneToOne(targetEntity="Attachment", cascade={"remove"}, orphanRemoval=true, fetch="EAGER")
     * @ORM\JoinColumn(name="header_attachment_id", referencedColumnName="id")
     */
    protected $headerAttachment;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $createdAt;

    /**
     * @ORM\PrePersist
     */
    public function setCreatedAtValue() {
        $this->createdAt = new \DateTime();
        $this->updated = new \DateTime();
    }

    /**
     * @ORM\Column(type="datetime")
     */
    protected $updated;

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
     * @return Business
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
     * Set yelpId
     *
     * @param string $yelpId
     * @return Business
     */
    public function setYelpId($yelpId)
    {
        $this->yelpId = $yelpId;

        return $this;
    }

    /**
     * Get yelpId
     *
     * @return string
     */
    public function getYelpId()
    {
        return $this->yelpId;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Business
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Business
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
     * Set address
     *
     * @param \AppBundle\Entity\Address $address
     * @return Business
     */
    public function setAddress(\AppBundle\Entity\Address $address = null)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return \AppBundle\Entity\Address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set owner
     *
     * @param \AppBundle\Entity\User $owner
     * @return Business
     */
    public function setOwner(\AppBundle\Entity\User $owner = null)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner
     *
     * @return \AppBundle\Entity\User
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Set headerAttachment
     *
     * @param \AppBundle\Entity\Attachment $headerAttachment
     * @return Business
     */
    public function setHeaderAttachment(\AppBundle\Entity\Attachment $headerAttachment = null)
    {
        $this->headerAttachment = $headerAttachment;

        return $this;
    }

    /**
     * Get headerAttachment
     *
     * @return \AppBundle\Entity\Attachment
     */
    public function getHeaderAttachment()
    {
        return $this->headerAttachment;
    }

    public function toJSON() {
        return array(
            'address' => array(
                'street' => $this->address->getStreet(),
                'line2' => $this->address->getLine2(),
                'city' => $this->address->getCity(),
                'state' => $this->address->getState(),
                'zip' => $this->address->getZip(),
                'country' => $this->address->getCountry()
            ),
            'coordinates' => array(
                'longitude' => $this->address->getLongitude(),
                'latitude' => $this->address->getLatitude()
            ),
            'name' => $this->getName(),
            'description' => $this->getDescription()
        );
    }
}
