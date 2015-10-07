<?php

// src/AppBundle/Entity/RecurringAppointments.php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="app_recurring_appointments")
 */
class RecurringAppointments {

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
    * @ORM\ManyToOne(targetEntity="TreatmentAvailabilitySet", inversedBy="recurrences")
    * @ORM\JoinColumn(name="availabilityId", referencedColumnName="id",nullable=true)
    */
    private $availability;


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
     * @return RecurringAppointments
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
     * @return RecurringAppointments
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
     * Set availability
     *
     * @param \AppBundle\Entity\TreatmentAvailabilitySet $availability
     * @return RecurringAppointments
     */
    public function setAvailability(\AppBundle\Entity\TreatmentAvailabilitySet $availability = null)
    {
        $this->availability = $availability;

        return $this;
    }

    /**
     * Get availability
     *
     * @return \AppBundle\Entity\TreatmentAvailabilitySets
     */
    public function getAvailability()
    {
        return $this->availability;
    }
}
