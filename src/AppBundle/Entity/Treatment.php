<?php

// src/AppBundle/Entity/Treatment.php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Cocur\Slugify\Slugify;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="app_treatments")
 */
class Treatment {
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer", length=3)
     * @Assert\NotBlank()
     * @Assert\GreaterThanOrEqual(
     *     value = 15
     * )
     * @Assert\LessThan(
     *     value = 520
     * )
     */
    protected $duration;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="TreatmentCategory")
     * @ORM\JoinColumn(name="treatment_category_id", referencedColumnName="id")
     */
    protected $treatmentCategory;

    /**
     * @ORM\ManyToOne(targetEntity="Business", inversedBy="treatments")
     * @ORM\JoinColumn(name="business_id", referencedColumnName="id")
     */
    protected $business;

    /**
     * @ORM\OneToMany(targetEntity="TreatmentAvailabilitySet", mappedBy="treatment")
     */
    protected $treatmentAvailabilitySets;

    /**
     * @ORM\ManyToOne(targetEntity="Therapist")
     * @ORM\JoinColumn(name="therapist_id", referencedColumnName="id")
     */
    protected $therapist;

    /**
     * @ORM\Column(type="float", length=8)
     * @Assert\NotBlank()
     * @Assert\GreaterThan(
     *     value = 0.00
     * )
     */
    protected $originalPrice;

    /**
     * Set originalPrice
     *
     * @param float $originalPrice
     * @return Service
     */
    public function setOriginalPrice($originalPrice)
    {
        $this->originalPrice = $originalPrice;

        return $this;
    }

    /**
     * Get originalPrice
     *
     * @return float
     */
    public function getOriginalPrice()
    {
        return $this->originalPrice;
    }

    public function __construct()
    {
      $this->treatmentAvailabilitySets = new ArrayCollection();
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
     * Set hours
     *
     * @param integer $hours
     * @return Service
     */
    public function setHours($hours)
    {
        $this->hours = $hours;

        return $this;
    }

    /**
     * Get hours
     *
     * @return integer
     */
    public function getHours()
    {
        return $this->hours;
    }

    /**
     * Set duration
     *
     * @param integer $duration
     * @return integer
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * Get duration
     *
     * @return integer
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Service
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
     * Get time for printing
     *
     * @return String
     */
    public function getTimeForPrint()
    {
        $duration = $this->getDuration();

        $minutes = $duration % 60;
        $hours = floor($duration / 60);

        $string = '';

        if ($hours > 0) {
            $string .= sprintf('%d hr%s ', $hours, $hours == 1 ? '' : 's');
        }

        $string .= sprintf('%d min%s', $minutes, $minutes == 1 ? '' : 's');

        return $string;
    }

    /**
     * Set business
     *
     * @param \AppBundle\Entity\Business $business
     * @return Service
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
     * Add treatmentAvailabilitySets
     *
     * @param \AppBundle\Entity\TreatmentAvailabilitySet $treatmentAvailabilitySets
     * @return Service
     */
    public function addTreatmentAvailabilitySet(\AppBundle\Entity\TreatmentAvailabilitySet $treatmentAvailabilitySets)
    {
        $this->treatmentAvailabilitySets[] = $treatmentAvailabilitySets;

        return $this;
    }

    /**
     * Remove treatmentAvailabilitySets
     *
     * @param \AppBundle\Entity\TreatmentAvailabilitySet $treatmentAvailabilitySets
     */
    public function removeTreatmentAvailabilitySet(\AppBundle\Entity\TreatmentAvailabilitySet $treatmentAvailabilitySets)
    {
        $this->treatmentAvailabilitySets->removeElement($treatmentAvailabilitySets);
    }

    /**
     * Get treatmentAvailabilitySets
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTreatmentAvailabilitySets()
    {
        return $this->treatmentAvailabilitySets;
    }

    /**
     * Set treatmentCategory
     *
     * @param \AppBundle\Entity\ServiceType $treatmentCategory
     * @return Service
     */
    public function setTreatmentCategory(\AppBundle\Entity\TreatmentCategory $treatmentCategory = null)
    {
        $this->treatmentCategory = $treatmentCategory;

        return $this;
    }

    /**
     * Get treatmentCategory
     *
     * @return \AppBundle\Entity\TreatmentCategory
     */
    public function getTreatmentCategory()
    {
        return $this->treatmentCategory;
    }

    /**
     * Set therapist
     *
     * @param \AppBundle\Entity\Therapist $therapist
     * @return Service
     */
    public function setTherapist(\AppBundle\Entity\Therapist $therapist = null)
    {
        $this->therapist = $therapist;

        return $this;
    }

    /**
     * Get therapist
     *
     * @return \AppBundle\Entity\Therapist
     */
    public function getTherapist()
    {
        if (!$this->therapist) {
            return 'None';
        }
        return $this->therapist;
    }

    public function getLabel() {
        return $this->getTreatmentCategory()->getLabel();
    }

    public static function getByCategory($treatments)
    {
      $list = [];
      foreach ($treatments as $treatment) {
        $children = $treatment->getChildren();

        if(!array_key_exists($cat,$list)){
          $list[$cat] = [];
        }
        $list[$cat][]= $treatment;
      }
      return $list;
    }
}
