<?php

// src/AppBundle/Entity/TreatmentAvailabilitySet.php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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
   protected $id;

   /**
    * @ORM\Column(type="date")
    * @Assert\NotBlank()
    */
   protected $date;

   /**
    * @ORM\Column(type="time", length=255)
    * @Assert\NotBlank()
    */
   protected $time;

   /**
    * @ORM\ManyToOne(targetEntity="Service", inversedBy="treatmentAvailabilitySets")
    * @ORM\JoinColumn(name="serviceId", referencedColumnName="id")
    */
    protected $serviceId;

   /**
    * @ORM\Column(type="integer", options={"default": 0})
    */
   protected $isOpen;


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
    public function setIsOpen($isOpen)
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
     * Set service_id
     *
     * @param \AppBundle\Entity\Service $serviceId
     * @return TreatmentAvailabilitySet
     */
    public function setServiceId(\AppBundle\Entity\Service $serviceId = null)
    {
        $this->serviceId = $serviceId;

        return $this;
    }

    /**
     * Get service_id
     *
     * @return \AppBundle\Entity\Service
     */
    public function getServiceId()
    {
        return $this->serviceId;
    }
}
