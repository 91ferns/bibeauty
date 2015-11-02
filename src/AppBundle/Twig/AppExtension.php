<?php
// src/AppBundle/Twig/AppExtension.php
namespace AppBundle\Twig;

use AppBundle\Entity\OperatingSchedule;
use Cocur\Slugify\Slugify;
use Symfony\Bridge\Doctrine\RegistryInterface;
use AppBundle\Entity\Business;

class AppExtension extends \Twig_Extension
{
    const AWS_HOST = 's3.amazonaws.com';

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('s3', array($this, 's3Filter')),
            new \Twig_SimpleFilter('rating',
                array($this, 'ratingStarsFilter'),
                array('is_safe' => array('html'))
            ),
            new \Twig_SimpleFilter('elapsed', array($this, 'elapsedFilter')),
            new \Twig_SimpleFilter('phone', array($this, 'phoneNumberFilter')),
            new \Twig_SimpleFilter('slugify', array($this, 'slugFilter')),
            new \Twig_SimpleFilter('meridian', array($this, 'hourMeridianFilter')),
            new \Twig_SimpleFilter('nicehour', array($this, 'formatHourFilter')),
            new \Twig_SimpleFilter('duration', array($this, 'formatMinutesFilter')),
            new \Twig_SimpleFilter('today', array($this, 'formatTodayFilter')),
        );
    }
    public function getFunctions()
  {
      return array(
          new \Twig_SimpleFunction('get_businesses', array($this, 'getBusinesses')),
      );
  }
    protected $bucket;

    public function __construct($aws, RegistryInterface $doctrine) {
        $this->bucket = (string) $aws['bucket'];
        $this->doctrine = $doctrine;
    }

    public function s3Filter( $key )
    {
        return sprintf('http://%s.%s/%s', $this->bucket, self::AWS_HOST, $key);
    }

    public function slugFilter( $string ) {
        $slugify = new Slugify();
        return $slugify->slugify($string); // hello-world
    }

    public function elapsedFilter( $timestamp ) {
        //type cast, current time, difference in timestamps
        return OperatingSchedule::getElapsedTime($timestamp);
    }

    public function formatMinutesFilter( $originalMinutes ) {

        if ($originalMinutes < 60) {
            $minutes = $originalMinutes;
            $hours = 0;
        } else {
            $hours = floor($originalMinutes / 60);
            $minutes = $originalMinutes % 60;
        }

        $text = '';
        if ($hours > 0) {
            $text .= $hours . ' hour';
            if ($hours > 1) {
                $text .= 's';
            }
            $text .= ' ';
        }

        if ($minutes > 0) {
            $text .= $minutes . ' minute';
            if ($minutes > 1) {
                $text .= 's';
            }
        }

        return $text;

    }

    public function ratingStarsFilter( $number ) {
        $stars = array();
        for ($i = 1; $i <= 5; $i++) {
            if ($i - 1 < $number && $i > $number) {
                $stars[] = '<i class="fa fa-star-half-o active"></i>';
            } else {
                $active = ($i <= $number) ? ' active' : '-o';
                $stars[] = sprintf('<i class="fa fa-star%s"></i>', $active);
            }
        }

        return join('', $stars);
    }

    public function formatHourFilter( $hour ) {
        if ($hour === 0 || $hour === 12) {
            return $hour;
        }
        $mod = $hour % 12;
        return $mod;
    }

    public function hourMeridianFilter( $hour ) {
        if ($hour < 12) {
            return 'AM';
        }

        return 'PM';
    }

    public function phoneNumberFilter( $phone, $country = 'US' ) {
        $phoneUtil = \libphonenumber\PhoneNumberUtil::getInstance();
        try {
            $usNumberProto = $phoneUtil->parse($phone, $country);
            return $phoneUtil->format($usNumberProto, \libphonenumber\PhoneNumberFormat::NATIONAL);
        } catch (\libphonenumber\NumberParseException $e) {
            return '';
        }
    }

    public function formatTodayFilter( $date ) {
        $timestamp = "2014.09.02T13:34";

        $today = new \DateTime(); // This object represents current date/time
        $today->setTime( 0, 0, 0 ); // reset time part, to prevent partial comparison

        $date->setTime( 0, 0, 0 ); // reset time part, to prevent partial comparison

        $diff = $today->diff( $date );
        $diffDays = (integer) $diff->format( "%R%a" ); // Extract days count in interval

        switch( $diffDays ) {
            case 0:
                return "Today";
            case -1:
                return "Yesterday";
            case +1:
                return "Tomorrow";
            default:
                return "Sometime";
        }
    }

    public function getBusinesses($user)
    {
      //$user = $this->getUser();
      $em = $this->doctrine->getManager();
      $repository = $em->getRepository("AppBundle:Business");

      return ($user->getSuperAdmin() === true)  ?
        $repository->findAll() :
          $repository->findByOwner($user);
    }

    public function getName()
    {
        return 'app_extension';
    }
}
