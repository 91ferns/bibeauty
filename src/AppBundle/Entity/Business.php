<?php

// src/AppBundle/Entity/Business.php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Cocur\Slugify\Slugify;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass="BusinessRepository")
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
    protected $slug;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Url()
     */
    protected $website = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     * @Assert\Email
     */
    protected $email = null;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $acceptsCash = true;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $acceptsCredit = true;

    /**
     * @ORM\OneToOne(targetEntity="Address", cascade={"persist", "remove"}, orphanRemoval=true, fetch="EAGER")
     * @ORM\JoinColumn(name="address_id", referencedColumnName="id")
     */
    protected $address;

    /**
     * @ORM\OneToMany(targetEntity="Review", cascade={"persist"}, mappedBy="business")
     */
    protected $reviews;

    /**
     * @ORM\OneToOne(targetEntity="OperatingSchedule", cascade={"persist", "remove"}, orphanRemoval=true, fetch="EAGER")
     * @ORM\JoinColumn(name="operation_id", referencedColumnName="id")
     */
    protected $operation;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Url()
     */
    protected $yelpLink;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     * @Assert\Length(min = 10)
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
     * @ORM\OneToOne(targetEntity="Attachment", cascade={"remove"}, orphanRemoval=true, fetch="EAGER")
     * @ORM\JoinColumn(name="logo_attachment_id", referencedColumnName="id")
     */
    protected $logoAttachment;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $updated;

    /**
     * @ORM\OneToMany(targetEntity="Service", cascade={"persist"}, mappedBy="business")
     */
    protected $services;

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
     * Set yelpLink
     *
     * @param string $yelpLink
     * @return Business
     */
    public function setYelpLink($yelpLink)
    {
        if (!empty($yelpLink)) {
            $this->yelpLink = $yelpLink;
        }

        return $this;
    }

    /**
     * Get yelpLink.
     *
     * @return string
     */
    public function getYelpLink() {
        return $this->yelpLink;
    }

    /**
     * Get yelpId. Converts to ID from the link
     *
     * @return string
     */
    public function getYelpId()
    {
        $regex = '/https?:\/\/(www.)?yelp.com\/biz\/(?P<id>[^\/])+/';

        if (preg_match($regex, $this->yelpLink, $matches)) {
            return $matches['id'];
        }
        return false;

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

    public function hasHeaderAttachment() {
        if ($this->headerAttachment) return true;
        return false;
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

    /**
     * Set slug
     *
     * @param string $slug
     * @return Business
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
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
            'description' => $this->getDescription(),
            'slug' => $this->getSlug(),
            'id' => $this->getID()
        );
    }

    /**
     * Set website
     *
     * @param string $website
     * @return Business
     */
    public function setWebsite($website)
    {
        $this->website = $website;

        return $this;
    }

    /**
     * Get website
     *
     * @return string
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Business
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
     * Set acceptsCash
     *
     * @param boolean $acceptsCash
     * @return Business
     */
    public function setAcceptsCash($acceptsCash)
    {
        $this->acceptsCash = $acceptsCash;

        return $this;
    }

    /**
     * Get acceptsCash
     *
     * @return boolean
     */
    public function getAcceptsCash()
    {
        return $this->acceptsCash;
    }

    /**
     * Set acceptsCredit
     *
     * @param boolean $acceptsCredit
     * @return Business
     */
    public function setAcceptsCredit($acceptsCredit)
    {
        $this->acceptsCredit = $acceptsCredit;

        return $this;
    }

    /**
     * Get acceptsCredit
     *
     * @return boolean
     */
    public function getAcceptsCredit()
    {
        return $this->acceptsCredit;
    }

    /**
     * Set operation
     *
     * @param \AppBundle\Entity\OperatingSchedule $operation
     * @return Business
     */
    public function setOperation(\AppBundle\Entity\OperatingSchedule $operation = null)
    {
        $this->operation = $operation;

        return $this;
    }

    /**
     * Get operation
     *
     * @return \AppBundle\Entity\OperatingSchedule
     */
    public function getOperation()
    {
        return $this->operation;
    }

    /**
     * Set logoAttachment
     *
     * @param \AppBundle\Entity\Attachment $logoAttachment
     * @return Business
     */
    public function setLogoAttachment(\AppBundle\Entity\Attachment $logoAttachment = null)
    {
        $this->logoAttachment = $logoAttachment;

        return $this;
    }

    /**
     * Get logoAttachment
     *
     * @return \AppBundle\Entity\Attachment
     */
    public function getLogoAttachment()
    {
        return $this->logoAttachment;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->reviews = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add reviews
     *
     * @param \AppBundle\Entity\Review $reviews
     * @return Business
     */
    public function addReview(\AppBundle\Entity\Review $reviews)
    {
        $this->reviews[] = $reviews;

        return $this;
    }

    /**
     * Remove reviews
     *
     * @param \AppBundle\Entity\Review $reviews
     */
    public function removeReview(\AppBundle\Entity\Review $reviews)
    {
        $this->reviews->removeElement($reviews);
    }

    public function hasReviews() {
        return $this->reviews->count() > 0;
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

    protected $averageRating = false;

    public function getAverageRating() {
        return $this->averageRating;
    }

    public function setAverageRating($averageRating) {
        $this->averageRating = $averageRating;
    }

    /**
     * @ORM\PrePersist
     */
    public function setAutomaticFields() {
        if (!$this->createdAt) {
            $this->createdAt = new \DateTime();
        }
        $this->updated = new \DateTime();
        $this->setSlug($this->generateSlug());
    }

    protected function generateSlug() {
        $slugify = new Slugify();
        return $slugify->slugify($this->name); // hello-world
    }

    /**
     * @ORM\PreUpdate
     */
    public function setUpdated() {
        // will NOT be saved in the database
        $this->updated->modify("now");
    }


    /**
     * Add service
     *
     * @param \AppBundle\Entity\ServiceCategory $services
     * @return Business
     */
    public function addService(\AppBundle\Entity\Service $service)
    {
        $this->service[] = $service;

        return $this;
    }

    /**
     * Remove service
     *
     * @param \AppBundle\Entity\ServiceCategory $serviceCategories
     */
    public function removeService(\AppBundle\Entity\ServiceCategory $service)
    {
        $this->service->removeElement($service);
    }

    /**
     * Get services
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getServices()
    {
        return $this->services;
    }

    public function getServicesAsChoices() {
        $array = array();
        foreach ($this->services as $service) {
            $id = $service->getId();
            $array[$id] = $service->getLabel();
        }

        return $array;
    }
}
