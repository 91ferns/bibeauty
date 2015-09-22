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
     * @ORM\ManyToOne(targetEntity="ServiceCategory")
     * @ORM\JoinColumn(name="serviceCategoryId", referencedColumnName="id")
     */
    protected $serviceCategoryId;

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
     * @ORM\OneToMany(targetEntity="Service", cascade={"persist"}, mappedBy="service")
     */
    protected $serviceCategories;


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
     * Set serviceCategoryId
     *
     * @param \AppBundle\Entity\ServiceCategory $serviceCategoryId
     * @return ServiceType
     */
    public function setServiceCategoryId(\AppBundle\Entity\ServiceCategory $serviceCategoryId = null)
    {
        $this->serviceCategoryId = $serviceCategoryId;

        return $this;
    }

    /**
     * Get serviceCategoryId
     *
     * @return \AppBundle\Entity\ServiceCategory
     */
    public function getServiceCategoryId()
    {
        return $this->serviceCategoryId;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->serviceCategories = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add serviceCategories
     *
     * @param \AppBundle\Entity\Service $serviceCategories
     * @return ServiceType
     */
    public function addServiceCategory(\AppBundle\Entity\Service $serviceCategories)
    {
        $this->serviceCategories[] = $serviceCategories;

        return $this;
    }

    /**
     * Remove serviceCategories
     *
     * @param \AppBundle\Entity\Service $serviceCategories
     */
    public function removeServiceCategory(\AppBundle\Entity\Service $serviceCategories)
    {
        $this->serviceCategories->removeElement($serviceCategories);
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
}
