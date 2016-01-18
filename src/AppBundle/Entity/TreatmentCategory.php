<?php

// src/AppBundle/Entity/TreatmentCategory.php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Cocur\Slugify\Slugify;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="TreatmentCategoryRepository")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="app_treatment_categories")
 */
class TreatmentCategory {
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="TreatmentCategory", mappedBy="parent")
     */
    protected $children;

    /**
     * @ORM\ManyToOne(targetEntity="TreatmentCategory", inversedBy="children", cascade={"persist"})
     * @ORM\JoinColumn(name="parent_category_id", referencedColumnName="id")
     */
    protected $parent;

    /**
     * @ORM\OneToMany(targetEntity="TreatmentCategory", mappedBy="alias")
     */
    protected $aliases;

    /**
     * @ORM\ManyToOne(targetEntity="TreatmentCategory", inversedBy="aliases")
     * @ORM\JoinColumn(name="alias_category_id", referencedColumnName="id", nullable=true)
     */
    protected $alias;

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
     * Constructor
     */
    public function __construct()
    {
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set parent
     *
     * @param \AppBundle\Entity\TreatmentCategory $treatmentCategory
     * @return ServiceType
     */
    public function setParent(\AppBundle\Entity\TreatmentCategory $parent = null)
    {
        $this->parent = $parent;
        return $this;
    }

    /**
     * Get parent
     *
     * @return \AppBundle\Entity\TreatmentCategory
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add serviceCategories
     *
     * @param \AppBundle\Entity\TreatmentCategory $children
     * @return ServiceType
     */
    public function addChild(\AppBundle\Entity\TreatmentCategory $child)
    {
        $this->children[] = $child;

        return $this;
    }

    /**
     * Remove child
     *
     * @param \AppBundle\Entity\TreatmentCategory $treatmentCategory
     */
    public function removeChild(\AppBundle\Entity\TreatmentCategory $child)
    {
        $this->children->removeElement($child);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Set parent
     *
     * @param \AppBundle\Entity\TreatmentCategory $treatmentCategory
     * @return ServiceType
     */
    public function setAlias(\AppBundle\Entity\TreatmentCategory $alias = null)
    {
        $this->alias = $alias;
        return $this;
    }

    /**
     * Get parent
     *
     * @return \AppBundle\Entity\TreatmentCategory
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * Add serviceCategories
     *
     * @param \AppBundle\Entity\TreatmentCategory $children
     * @return ServiceType
     */
    public function addAlias(\AppBundle\Entity\TreatmentCategory $alias)
    {
        $this->aliases[] = $alias;

        return $this;
    }

    /**
     * Remove child
     *
     * @param \AppBundle\Entity\TreatmentCategory $treatmentCategory
     */
    public function removeAlias(\AppBundle\Entity\TreatmentCategory $alias)
    {
        $this->aliases->removeElement($alias);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAliases()
    {
        return $this->aliases;
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
        return $this->getParent() ? $this->getParent()->getLabel() : null;
    }

    private $treatments = array(); // Temporary

    public function hasTreatments() {
        return count($this->treatments) > 0;
    }

    public function addTreatment(\AppBundle\Entity\Treatment $treatment) {
        $this->treatments[] = $treatment;
    }

    public function getTreatments() {
        return $this->treatments;
    }

    public function getLowestPrice() {
        $treatments = $this->getTreatments();
        $lowest = false;

        foreach($treatments as $treatment) {
            $price = $treatment->getCurrentPrice();
            if ($lowest === false || $price < $lowest) {
                $lowst = $price;
            }
        }

        return $lowest;
    }

}
