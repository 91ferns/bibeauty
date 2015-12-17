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
    * @ORM\Column(type="datetime")
    * @Assert\NotBlank()
    */
   private $date;

   /**
    * @ORM\ManyToOne(targetEntity="Business")
    * @ORM\JoinColumn(name="business_id", referencedColumnName="id")
    */
   private $business;

   /**
    * @ORM\ManyToOne(targetEntity="Treatment",cascade={"persist","remove"})
    * @ORM\JoinColumn(name="treatment_id", referencedColumnName="id")
    */
   private $treatment;

   /**
    * @ORM\ManyToOne(targetEntity="OfferAvailabilitySet", inversedBy="availabilities",cascade={"persist","remove"})
    * @ORM\JoinColumn(name="availability_set_id", referencedColumnName="id")
    */
    private $availabilitySet;

    /**
     * @ORM\Column(type="boolean", options={"default" = true})
     */
    private $active = true;


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

    /**
     * Set active
     *
     * @param boolean $active
     * @return Availability
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }

    public function isToday() {
        $date = $this->getDate()->format('d M Y');
        $today = new \DateTime('today');
        $today = $today->format('d M Y');

        return $date == $today;
    }

    public function isTomorrow() {
        $date = $this->getDate()->format('d M Y');
        $tomorrow = new \DateTime('tomorrow');
        $tomorrow = $tomorrow->format('d M Y');

        return $date == $tomorrow;
    }

    public function getDayText() {
        if ($this->isToday()) {
            return 'today';
        } elseif ($this->isTomorrow()) {
            return 'tomorrow';
        } else {
            return $this->getDate();
        }
    }

    public function getTimeText() {
        $date = $this->getDate();
        return $date->format('H:iA');
    }
}
