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

   const MAX_TIME_FORWARD = 31536000; // 60 * 60 * 24 * 365;
   const DAY_IN_SECONDS = 86400; // 60 * 60 * 24

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
    * @ORM\Column(type="simple_array", nullable=true)
    */
   private $daysOfTheWeek = array();

   /**
    * @ORM\Column(type="simple_array")
    * @Assert\NotBlank()
    */
   private $times = array();

    /**
     * @ORM\OneToOne(targetEntity="Offer")
     * @ORM\JoinColumn(name="offer_id", referencedColumnName="id")
     **/
    private $offer;

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

    public function createAvailability($date) {

    }

    protected function dateAndTime($date, $time, $isString = true) {
        if (!$isString) {
            $datestring = strtotime($date);
        } else {
            $datestring = $date;
        }
        // Let's do time ourselves
        list($hour, $minute) = explode(':', $time);

        $hourstring = $hour * 60 * 60;
        $minutestring = $minute * 60;

        return $datestring + $minutestring + $hourstring;
    }

    protected function buildDateString($month, $day, $year) {

        $string = sprintf('%s-%s-%s', $year, $month, $day);
        return strtotime($string);

    }

    public function datesThatMatchRecurrence() {
        $startDate = $this->getStartDate();
        $startDate = $startDate->format('m/d/Y');

        $times = $this->getTimes();
        $DOWs = $this->getDaysOfTheWeek();
        $type = $this->getRecurrenceType();

        $dates =  array();

        // First one that matches is always the start date
        foreach($times as $time) {
            $string = $this->dateAndTime($startDate, $time, false);
            if ($string) {
                $x = new \DateTime();
                $x->setTimestamp($string);
                $dates[] = $x;
            }
        }

        // Now we have the ones from the start date,
        // so we need to match the rules for the next days
        if (!$type) {
            return $dates; // if there is no recurrence specified, we are done.
        }

        $starttime = strtotime($startDate);

        if ($type === 'daily') {
            // If it is daily, we just need to iterate day by day for a year.
            for ($i = $starttime + self::DAY_IN_SECONDS;
                 $i < $starttime + self::MAX_TIME_FORWARD;
                 $i = $i + self::DAY_IN_SECONDS) {
                // $i is the new "startdate"
                foreach($times as $time) {
                    $string = $this->dateAndTime($i, $time);

                    if ($string) {
                        $x = new \DateTime();
                        $x->setTimestamp($string);
                        $dates[] = $x;
                    }
                }
            }

            return $dates;
        }

        // Now, monthly
        // This one we need to get the number of days in a given month, or just reformat
        // our start time so the month increments 12 times
        if ($type === 'monthly') {

            // let's get the month number
            $startMonth = date('n', $starttime);
            $startDay = date('j', $starttime);
            $startYear = date('Y', $starttime);

            for ($i = 1; $i <= 12; $i++) {

                $rawMonth = $startMonth + $i;
                $currentYear = $startYear + floor($rawMonth / 12);
                $currentMonth = $rawMonth % 12;
                if ($currentMonth === 0) {
                    $currentMonth = 12;
                }
                $daysInMonth = date('t', $this->buildDateString($currentYear, $currentMonth, 1));

                if ($daysInMonth < $startDay) {
                    continue;
                }

                $buildDate = $this->buildDateString($currentYear, $currentMonth, $startDay);

                foreach($times as $time) {
                    $string = $this->dateAndTime($buildDate, $time);
                    if ($string) {
                        $x = new \DateTime();
                        $x->setTimestamp($string);
                        $dates[] = $x;
                    }
                }

            }

            return $dates;
        }

        if ($type === 'weekly') {

            // By far the hardest one. Need to iterate day by day and check to see if the day of the week matches the allowed DOWs
            $DOWs = array_map('strtolower', $DOWs);

            for ($i = $starttime + self::DAY_IN_SECONDS;
                 $i < $starttime + self::MAX_TIME_FORWARD;
                 $i = $i + self::DAY_IN_SECONDS) {
                // $i is the new "startdate"

                $dow = strtolower(date('l', $i));

                if (!in_array($dow, $DOWs)) {
                    continue;
                }

                foreach($times as $time) {
                    $string = $this->dateAndTime($i, $time);
                    if ($string) {
                        $x = new \DateTime();
                        $x->setTimestamp($string);
                        $dates[] = $x;
                    }
                }
            }

            return $dates;

        }

        return $dates;
    }

    public function datesToAvailabilities( $dates = array(), \AppBundle\Entity\Business $business ) {
        $availabilities = array();

        foreach($dates as $date) {
            $x = new \AppBundle\Entity\Availability();
            $x->setDate($date);
            $x->setAvailabilitySet($this);
            $x->setTreatment($this->getTreatment());
            $x->setBusiness($business);

            $availabilities[] = $x;
        }

        return $availabilities;
    }

    /**
     * Set offer
     *
     * @param \AppBundle\Entity\Offer $offer
     * @return OfferAvailabilitySet
     */
    public function setOffer(\AppBundle\Entity\Offer $offer = null)
    {
        $this->offer = $offer;

        return $this;
    }

    /**
     * Get offer
     *
     * @return \AppBundle\Entity\Offer
     */
    public function getOffer()
    {
        return $this->offer;
    }
}
