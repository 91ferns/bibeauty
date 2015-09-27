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
    private $availabilityId;


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
     * Set availabilityId
     *
     * @param \AppBundle\Entity\TreatmentAvailabilitySet $availabilityId
     * @return RecurringAppointments
     */
    public function setAvailabilityId(\AppBundle\Entity\TreatmentAvailabilitySet $availabilityId = null)
    {
        $this->availabilityId = $availabilityId;

        return $this;
    }

    /**
     * Get availabilityId
     *
     * @return \AppBundle\Entity\TreatmentAvailabilitySets
     */
    public function getAvailabilityId()
    {
        return $this->availabilityId;
    }
}
