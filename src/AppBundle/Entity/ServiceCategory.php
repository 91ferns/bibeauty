<?php

// src/AppBundle/Entity/Business.php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Cocur\Slugify\Slugify;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="app_service_categories")
 */
class ServiceCategory {
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(min = 3)
     * @ORM\Column(type="string", length=255)
     */
    protected $label;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $slug;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $createdAt;


    /**
     * @ORM\OneToMany(targetEntity="ServiceType", cascade={"persist", "remove"}, mappedBy="serviceCategory")
     */
    protected $serviceTypes;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $updated;

    /**
     * @ORM\ManyToOne(targetEntity="Business")
     * @ORM\JoinColumn(name="business_id", referencedColumnName="id")
     */
    protected $business;


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
     * Set label
     *
     * @param string $label
     * @return ServiceCategory
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return ServiceCategory
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

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return ServiceCategory
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
     * Set business
     *
     * @param \AppBundle\Entity\Business $business
     * @return ServiceCategory
     */
    public function setBusiness(\AppBundle\Entity\Business $business = null)
    {
        $this->business = $business;

        return $this;
    }

    /**
     * Get business
     *
     * @return \AppBundle\Entity\Business
     */
    public function getBusiness()
    {
        return $this->business;
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
        return $slugify->slugify($this->label);
    }

    /**
     * @ORM\PreUpdate
     */
    public function setUpdated() {
        // will NOT be saved in the database
        $this->updated->modify("now");
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->serviceTypes = new \Doctrine\Common\Collections\ArrayCollection();
    }



    /**
     * Iterates through all services and returns lowest list price
     *
     * @return float
     */
    public function getLowestPrice()
    {
        $services = $this->getServices()->toArray();
        if (count($services) < 1) return 0.0;

        usort($services, function($a, $b) {
            $a = $a->getCurrentPrice();
            $b = $b->getCurrentPrice();

            if ($a == $b) {
                return 0;
            }
            return ($a < $b) ? -1 : 1;
        });

        $lowest = min($services);
        return $lowest->getCurrentPrice();
    }

    /**
     * Add serviceTypes
     *
     * @param \AppBundle\Entity\ServiceType $serviceTypes
     * @return ServiceCategory
     */
    public function addServiceType(\AppBundle\Entity\ServiceType $serviceTypes)
    {
        $this->serviceTypes[] = $serviceTypes;

        return $this;
    }
    /**
     * Remove serviceTypes
     *
     * @param \AppBundle\Entity\ServiceType $serviceTypes
     */
    public function removeServiceType(\AppBundle\Entity\ServiceType $serviceTypes)
    {
        $this->serviceTypes->removeElement($serviceTypes);
    }

    /**
     * Get serviceTypes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getServiceTypes()
    {
        return $this->serviceTypes;
    }
}
