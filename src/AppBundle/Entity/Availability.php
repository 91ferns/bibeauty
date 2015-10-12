<?php

// src/AppBundle/Entity/RecurringAppointments.php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass="AvailabilityRepository")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="app_availabilities")
 */
class Availability {

   /**
    * @ORM\Column(type="integer")
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="AUTO")
    */
   private $id;

   /**
    * @ORM\Column(type="date")
    * @Assert\NotBlank()
    */
   private $date;

   /**
    * @ORM\ManyToOne(targetEntity="Business")
    * @ORM\JoinColumn(name="business_id", referencedColumnName="id")
    */
   private $business;

   /**
    * @ORM\ManyToOne(targetEntity="Treatment")
    * @ORM\JoinColumn(name="treatment_id", referencedColumnName="id")
    */
   private $treatment;

   /**
    * @ORM\ManyToOne(targetEntity="OfferAvailabilitySet", inversedBy="availabilities")
    * @ORM\JoinColumn(name="availability_set_id", referencedColumnName="id")
    */
    private $availabilitySet;


    public function __toString() {
        return (string) $this->getId();
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
     * Set date
     *
     * @param \DateTime $date
     * @return Availability
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set business
     *
     * @param \AppBundle\Entity\Business $business
     * @return Availability
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
     * Set treatment
     *
     * @param \AppBundle\Entity\Treatment $treatment
     * @return Availability
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

    public function getLabel() {
        $treatment = $this->getTreatment();
        if ($treatment) {
            return $treatment->getLabel();
        }
        return 'N/A';
    }

    /**
     * Set availabilitySet
     *
     * @param \AppBundle\Entity\OfferAvailabilitySet $availabilitySet
     * @return Availability
     */
    public function setAvailabilitySet(\AppBundle\Entity\OfferAvailabilitySet $availabilitySet = null)
    {
        $this->availabilitySet = $availabilitySet;

        return $this;
    }

    /**
     * Get availabilitySet
     *
     * @return \AppBundle\Entity\OfferAvailabilitySet
     */
    public function getAvailabilitySet()
    {
        return $this->availabilitySet;
    }
}
