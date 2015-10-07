<?php

// src/AppBundle/Entity/Business.php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Cocur\Slugify\Slugify;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="app_service_type")
 */
class ServiceType {
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="ServiceCategory", inversedBy="serviceTypes")
     * @ORM\JoinColumn(name="service_category_id", referencedColumnName="id")
     */
    protected $serviceCategory;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(min = 3)
     * @ORM\Column(type="string", length=255)
     */
    protected $label;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $updated;


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
     * @return ServiceType
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return ServiceType
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
     * Set updated
     *
     * @param \DateTime $updated
     * @return ServiceType
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
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
     * Set serviceCategory
     *
     * @param \AppBundle\Entity\ServiceCategory $serviceCategory
     * @return ServiceType
     */
    public function setServiceCategory(\AppBundle\Entity\ServiceCategory $serviceCategory = null)
    {
        $this->serviceCategory = $serviceCategory;

        return $this;
    }

    /**
     * Get serviceCategory
     *
     * @return \AppBundle\Entity\ServiceCategory
     */
    public function getServiceCategory()
    {
        return $this->serviceCategory;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        //$this->serviceCategories = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add serviceCategories
     *
     * @param \AppBundle\Entity\Service $serviceCategories
     * @return ServiceType
     */
    public function addServiceCategory(\AppBundle\Entity\ServiceCategory $serviceCategory)
    {
        $this->serviceCategories[] = $serviceCategory;

        return $this;
    }

    /**
     * Remove serviceCategories
     *
     * @param \AppBundle\Entity\Service $serviceCategories
     */
    public function removeServiceCategory(\AppBundle\Entity\ServiceCategory $serviceCategory)
    {
        $this->serviceCategories->removeElement($serviceCategory);
    }

    /**
     * Get serviceCategories
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getServiceCategories()
    {
        return $this->serviceCategories;
    }

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
    public function updated() {
        // will NOT be saved in the database
        $this->updated->modify("now");
    }

    public function getCategoryName() {
        return $this->getServiceCategory() ? $this->getServiceCategory()->getLabel() : null;
    }

    private $services = array(); // Temporary

    public function hasServices() {
        return count($this->services) > 0;
    }

    public function addService(\AppBundle\Entity\Service $service) {
        $this->services[] = $service;
    }

    public function getServices() {
        return $this->services;
    }

    public function getLowestPrice() {
        $services = $this->getServices();
        $lowest = false;

        foreach($services as $service) {
            $price = $service->getCurrentPrice();
            if ($lowest === false || $price < $lowest) {
                $lowst = $price;
            }
        }

        return $lowest;
    }

}
