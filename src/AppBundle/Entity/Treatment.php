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
     * @ORM\Column(type="string", length=60)
     * @Assert\NotBlank()
     */
    protected $name;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     */
    protected $description;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="TreatmentCategory", fetch="EAGER")
     * @ORM\JoinColumn(name="treatment_category_id", referencedColumnName="id")
     */
    protected $treatmentCategory;

    /**
     * @ORM\ManyToOne(targetEntity="Business", inversedBy="treatments")
     * @ORM\JoinColumn(name="business_id", referencedColumnName="id")
     */
    protected $business;

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
     * @ORM\OneToMany(targetEntity="Offer", cascade={"persist","remove"}, mappedBy="treatment")
     */
    protected $offers;

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

    /**
     * Add offers
     *
     * @param \AppBundle\Entity\Offer $offers
     * @return Treatment
     */
    public function addOffer(\AppBundle\Entity\Offer $offers)
    {
        $this->offers[] = $offers;

        return $this;
    }

    /**
     * Remove offers
     *
     * @param \AppBundle\Entity\Offer $offers
     */
    public function removeOffer(\AppBundle\Entity\Offer $offers)
    {
        $this->offers->removeElement($offers);
    }

    /**
     * Get offers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOffers()
    {
        return $this->offers;
    }

    public function getCheapestOffer() {
        $offers = $this->getOffers();

        $cheapestOffer = false;

        foreach($offers as $offer) {
            if ($cheapestOffer === false) {
                $cheapestOffer = $offer;
            } elseif ($cheapestOffer->getCurrentPrice() > $offer->getCurrentPrice()) {
                $cheapestOffer = $offer;
            }
        }


        return $cheapestOffer;
    }

    public function getCheapestDiscountPrice() {
        $cheapestOffer = $this->getCheapestOffer();

        if (!$cheapestOffer) {
            return $this->getOriginalPrice();
        } else {
            return $cheapestOffer->getCurrentPrice();
        }

    }

    public function getCheapestDiscountPercentage() {
        $cheapestOffer = $this->getCheapestOffer();

        if (!$cheapestOffer) {
            return 0;
        } else {
            $current = $cheapestOffer->getCurrentPrice();
            $original = $this->getOriginalPrice();

            return sprintf('%d', ( ($original - $current) / $original ) * 100 );
        }

    }

    /**
     * Set name
     *
     * @param string $name
     * @return Treatment
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
     * Set description
     *
     * @param string $description
     * @return Treatment
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
}
