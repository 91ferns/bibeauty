<?php

// src/AppBundle/Entity/Address.php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="app_operating_schedules")
 */
class OperatingSchedule {

   /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
   protected $id;

   /**
     * @ORM\Column(type="string", length=8, nullable=true)
     */
   protected $mondayStart;

   /**
     * @ORM\Column(type="string", length=8, nullable=true)
     */
   protected $mondayEnd;

   /**
     * @ORM\Column(type="string", length=8, nullable=true)
     */
   protected $tuesdayStart;

   /**
     * @ORM\Column(type="string", length=8, nullable=true)
     */
   protected $tuesdayEnd;

   /**
     * @ORM\Column(type="string", length=8, nullable=true)
     */
   protected $wednesdayStart;

   /**
     * @ORM\Column(type="string", length=8, nullable=true)
     */
   protected $wednesdayEnd;

   /**
     * @ORM\Column(type="string", length=8, nullable=true)
     */
   protected $thursdayStart;

   /**
     * @ORM\Column(type="string", length=8, nullable=true)
     */
   protected $thursdayEnd;

   /**
     * @ORM\Column(type="string", length=8, nullable=true)
     */
   protected $fridayStart;

   /**
     * @ORM\Column(type="string", length=8, nullable=true)
     */
   protected $fridayEnd;

   /**
     * @ORM\Column(type="string", length=8, nullable=true)
     */
   protected $saturdayStart;

   /**
     * @ORM\Column(type="string", length=8, nullable=true)
     */
   protected $saturdayEnd;

   /**
     * @ORM\Column(type="string", length=8, nullable=true)
     */
   protected $sundayStart;

   /**
     * @ORM\Column(type="string", length=8, nullable=true)
     */
   protected $sundayEnd;


   public static function getTimes() {
       $arr = array();

       for ($i=0; $i <= 2350; $i = $i + 50) {
           $timeWithoutExtras = $i / 50;

           $minuteMod = $timeWithoutExtras % 2;
           $minute = $minuteMod * 30;

           $hourMod = floor($timeWithoutExtras / 2);
           if ($hourMod < 1 || $hourMod == 12) $hour = 12;
           else $hour = $hourMod % 12;

           $arr[$i] = sprintf('%d:%s %s', $hour, str_pad($minute, 2 , "0"), $hourMod < 12 ? 'a.m.' : 'p.m');
       }

       return $arr;

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
     * Set mondayStart
     *
     * @param string $mondayStart
     * @return OperatingSchedule
     */
    public function setMondayStart($mondayStart)
    {
        $this->mondayStart = $mondayStart;

        return $this;
    }

    /**
     * Get mondayStart
     *
     * @return string
     */
    public function getMondayStart()
    {
        return $this->mondayStart;
    }

    /**
     * Set mondayEnd
     *
     * @param string $mondayEnd
     * @return OperatingSchedule
     */
    public function setMondayEnd($mondayEnd)
    {
        $this->mondayEnd = $mondayEnd;

        return $this;
    }

    /**
     * Get mondayEnd
     *
     * @return string
     */
    public function getMondayEnd()
    {
        return $this->mondayEnd;
    }

    /**
     * Set tuesdayStart
     *
     * @param string $tuesdayStart
     * @return OperatingSchedule
     */
    public function setTuesdayStart($tuesdayStart)
    {
        $this->tuesdayStart = $tuesdayStart;

        return $this;
    }

    /**
     * Get tuesdayStart
     *
     * @return string
     */
    public function getTuesdayStart()
    {
        return $this->tuesdayStart;
    }

    /**
     * Set tuesdayEnd
     *
     * @param string $tuesdayEnd
     * @return OperatingSchedule
     */
    public function setTuesdayEnd($tuesdayEnd)
    {
        $this->tuesdayEnd = $tuesdayEnd;

        return $this;
    }

    /**
     * Get tuesdayEnd
     *
     * @return string
     */
    public function getTuesdayEnd()
    {
        return $this->tuesdayEnd;
    }

    /**
     * Set wednesdayStart
     *
     * @param string $wednesdayStart
     * @return OperatingSchedule
     */
    public function setWednesdayStart($wednesdayStart)
    {
        $this->wednesdayStart = $wednesdayStart;

        return $this;
    }

    /**
     * Get wednesdayStart
     *
     * @return string
     */
    public function getWednesdayStart()
    {
        return $this->wednesdayStart;
    }

    /**
     * Set wednesdayEnd
     *
     * @param string $wednesdayEnd
     * @return OperatingSchedule
     */
    public function setWednesdayEnd($wednesdayEnd)
    {
        $this->wednesdayEnd = $wednesdayEnd;

        return $this;
    }

    /**
     * Get wednesdayEnd
     *
     * @return string
     */
    public function getWednesdayEnd()
    {
        return $this->wednesdayEnd;
    }

    /**
     * Set thursdayStart
     *
     * @param string $thursdayStart
     * @return OperatingSchedule
     */
    public function setThursdayStart($thursdayStart)
    {
        $this->thursdayStart = $thursdayStart;

        return $this;
    }

    /**
     * Get thursdayStart
     *
     * @return string
     */
    public function getThursdayStart()
    {
        return $this->thursdayStart;
    }

    /**
     * Set thursdayEnd
     *
     * @param string $thursdayEnd
     * @return OperatingSchedule
     */
    public function setThursdayEnd($thursdayEnd)
    {
        $this->thursdayEnd = $thursdayEnd;

        return $this;
    }

    /**
     * Get thursdayEnd
     *
     * @return string
     */
    public function getThursdayEnd()
    {
        return $this->thursdayEnd;
    }

    /**
     * Set fridayStart
     *
     * @param string $fridayStart
     * @return OperatingSchedule
     */
    public function setFridayStart($fridayStart)
    {
        $this->fridayStart = $fridayStart;

        return $this;
    }

    /**
     * Get fridayStart
     *
     * @return string
     */
    public function getFridayStart()
    {
        return $this->fridayStart;
    }

    /**
     * Set fridayEnd
     *
     * @param string $fridayEnd
     * @return OperatingSchedule
     */
    public function setFridayEnd($fridayEnd)
    {
        $this->fridayEnd = $fridayEnd;

        return $this;
    }

    /**
     * Get fridayEnd
     *
     * @return string
     */
    public function getFridayEnd()
    {
        return $this->fridayEnd;
    }

    /**
     * Set saturdayStart
     *
     * @param string $saturdayStart
     * @return OperatingSchedule
     */
    public function setSaturdayStart($saturdayStart)
    {
        $this->saturdayStart = $saturdayStart;

        return $this;
    }

    /**
     * Get saturdayStart
     *
     * @return string
     */
    public function getSaturdayStart()
    {
        return $this->saturdayStart;
    }

    /**
     * Set saturdayEnd
     *
     * @param string $saturdayEnd
     * @return OperatingSchedule
     */
    public function setSaturdayEnd($saturdayEnd)
    {
        $this->saturdayEnd = $saturdayEnd;

        return $this;
    }

    /**
     * Get saturdayEnd
     *
     * @return string
     */
    public function getSaturdayEnd()
    {
        return $this->saturdayEnd;
    }

    /**
     * Set sundayStart
     *
     * @param string $sundayStart
     * @return OperatingSchedule
     */
    public function setSundayStart($sundayStart)
    {
        $this->sundayStart = $sundayStart;

        return $this;
    }

    /**
     * Get sundayStart
     *
     * @return string
     */
    public function getSundayStart()
    {
        return $this->sundayStart;
    }

    /**
     * Set sundayEnd
     *
     * @param string $sundayEnd
     * @return OperatingSchedule
     */
    public function setSundayEnd($sundayEnd)
    {
        $this->sundayEnd = $sundayEnd;

        return $this;
    }

    /**
     * Get sundayEnd
     *
     * @return string
     */
    public function getSundayEnd()
    {
        return $this->sundayEnd;
    }

    public static function getElapsedTime( $timestamp ) {
        if ($timestamp instanceof \DateTime) {
            $timestamp = $timestamp->getTimestamp();
        }
        $timestamp      = (int) $timestamp;
        $current_time   = time();
        $diff           = $current_time - $timestamp;

        //intervals in seconds
        $intervals      = array (
            'year' => 31556926, 'month' => 2629744, 'week' => 604800, 'day' => 86400, 'hour' => 3600, 'minute'=> 60
        );

        //now we just find the difference
        if ($diff == 0)
        {
            return 'just now';
        }

        if ($diff < 60)
        {
            return $diff == 1 ? $diff . ' second ago' : $diff . ' seconds ago';
        }

        if ($diff >= 60 && $diff < $intervals['hour'])
        {
            $diff = floor($diff/$intervals['minute']);
            return $diff == 1 ? $diff . ' minute ago' : $diff . ' minutes ago';
        }

        if ($diff >= $intervals['hour'] && $diff < $intervals['day'])
        {
            $diff = floor($diff/$intervals['hour']);
            return $diff == 1 ? $diff . ' hour ago' : $diff . ' hours ago';
        }

        if ($diff >= $intervals['day'] && $diff < $intervals['week'])
        {
            $diff = floor($diff/$intervals['day']);
            return $diff == 1 ? $diff . ' day ago' : $diff . ' days ago';
        }

        if ($diff >= $intervals['week'] && $diff < $intervals['month'])
        {
            $diff = floor($diff/$intervals['week']);
            return $diff == 1 ? $diff . ' week ago' : $diff . ' weeks ago';
        }

        if ($diff >= $intervals['month'] && $diff < $intervals['year'])
        {
            $diff = floor($diff/$intervals['month']);
            return $diff == 1 ? $diff . ' month ago' : $diff . ' months ago';
        }

        if ($diff >= $intervals['year'])
        {
            $diff = floor($diff/$intervals['year']);
            return $diff == 1 ? $diff . ' year ago' : $diff . ' years ago';
        }
    }
}
