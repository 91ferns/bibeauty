<?php

// src/AppBundle/Entity/OfferAvailabilitySet.php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="app_offer_availability_sets")
 */
class OfferAvailabilitySet {

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
   private $startDate;

   /**
    * @ORM\Column(type="simple_array")
    * @Assert\NotBlank()
    */
   private $daysOfTheWeek = array();

   /**
    * @ORM\Column(type="simple_array")
    * @Assert\NotBlank()
    */
   private $times = array();

   /**
    * @ORM\ManyToOne(targetEntity="Treatment", inversedBy="treatmentAvailabilitySets")
    * @ORM\JoinColumn(name="treatment_id", referencedColumnName="id")
    */
    private $treatment;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $recurrenceType = 'never';

    /**
     * @ORM\OneToMany(targetEntity="Availability", mappedBy="availability")
     */
    private $availabilities;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->availabilities = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set startDate
     *
     * @param \DateTime $startDate
     * @return OfferAvailabilitySet
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get startDate
     *
     * @return \DateTime 
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set daysOfTheWeek
     *
     * @param array $daysOfTheWeek
     * @return OfferAvailabilitySet
     */
    public function setDaysOfTheWeek($daysOfTheWeek)
    {
        $this->daysOfTheWeek = $daysOfTheWeek;

        return $this;
    }

    /**
     * Get daysOfTheWeek
     *
     * @return array 
     */
    public function getDaysOfTheWeek()
    {
        return $this->daysOfTheWeek;
    }

    /**
     * Set times
     *
     * @param array $times
     * @return OfferAvailabilitySet
     */
    public function setTimes($times)
    {
        $this->times = $times;

        return $this;
    }

    /**
     * Get times
     *
     * @return array 
     */
    public function getTimes()
    {
        return $this->times;
    }

    /**
     * Set recurrenceType
     *
     * @param string $recurrenceType
     * @return OfferAvailabilitySet
     */
    public function setRecurrenceType($recurrenceType)
    {
        $this->recurrenceType = $recurrenceType;

        return $this;
    }

    /**
     * Get recurrenceType
     *
     * @return string 
     */
    public function getRecurrenceType()
    {
        return $this->recurrenceType;
    }

    /**
     * Set treatment
     *
     * @param \AppBundle\Entity\Treatment $treatment
     * @return OfferAvailabilitySet
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

    /**
     * Add availabilities
     *
     * @param \AppBundle\Entity\Availability $availabilities
     * @return OfferAvailabilitySet
     */
    public function addAvailability(\AppBundle\Entity\Availability $availabilities)
    {
        $this->availabilities[] = $availabilities;

        return $this;
    }

    /**
     * Remove availabilities
     *
     * @param \AppBundle\Entity\Availability $availabilities
     */
    public function removeAvailability(\AppBundle\Entity\Availability $availabilities)
    {
        $this->availabilities->removeElement($availabilities);
    }

    /**
     * Get availabilities
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAvailabilities()
    {
        return $this->availabilities;
    }
}
