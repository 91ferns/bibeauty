<?php

// src/AppBundle/Entity/TreatmentAvailabilitySet.php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="app_treatment_availability_sets")
 */
class TreatmentAvailabilitySet {

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
    * @ORM\Column(type="time")
    * @Assert\NotBlank()
    */
   private $time;

   /**
    * @ORM\ManyToOne(targetEntity="Treatment", inversedBy="treatmentAvailabilitySets")
    * @ORM\JoinColumn(name="treatment_id", referencedColumnName="id")
    */
    private $treatment;

    /**
     * @ORM\Column(type="boolean")
     * @Assert\NotBlank()
     */
    private $isOpen = false;

    /**
     * @ORM\Column(type="boolean", options={"default" = false})
     * @Assert\NotBlank()
     */
     private $recurring = false;

     /**
      * @ORM\OneToMany(targetEntity="RecurringAppointments", mappedBy="availability")
      */
      private $recurrences;

     public function __construct(){
         $this->recurrences = new ArrayCollection();
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
     * @return TreatmentAvailabilitySet
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
     * Set time
     *
     * @param \DateTime $time
     * @return TreatmentAvailabilitySet
     */
    public function setTime($time)
    {
     $this->time = $time;

        return $this;
    }

    /**
     * Get time
     *
     * @return \DateTime
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * Set isOpen
     *
     * @param integer $isOpen
     * @return TreatmentAvailabilitySet
     */
    public function setIsOpen($isOpen = false)
    {
        $this->isOpen = $isOpen;

        return $this;
    }

    /**
     * Get isOpen
     *
     * @return integer
     */
    public function getIsOpen()
    {
        return $this->isOpen;
    }

    /**
     * Set treatment
     *
     * @param \AppBundle\Entity\Treatment $treatment
     * @return TreatmentAvailabilitySet
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
     * Set recurring
     *
     * @param boolean $recurring
     * @return TreatmentAvailabilitySet
     */
    public function setRecurring($recurring)
    {
        $this->recurring = $recurring;

        return $this;
    }

    /**
     * Get recurring
     *
     * @return boolean
     */
    public function getRecurring()
    {
        return $this->recurring;
    }

    /**
     * Add recurrences
     *
     * @param \AppBundle\Entity\RecurringAppointments $recurrences
     * @return TreatmentAvailabilitySet
     */
    public function addRecurrence(\AppBundle\Entity\RecurringAppointments $recurrences)
    {
        $this->recurrences[] = $recurrences;

        return $this;
    }

    /**
     * Remove recurrences
     *
     * @param \AppBundle\Entity\RecurringAppointments $recurrences
     */
    public function removeRecurrence(\AppBundle\Entity\RecurringAppointments $recurrences)
    {
        $this->recurrences->removeElement($recurrences);
    }

    /**
     * Get recurrences
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRecurrences()
    {
        return $this->recurrences;
    }
}
