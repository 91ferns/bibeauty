<?php
// src/AppBundle/Twig/AppExtension.php
namespace AppBundle\Twig;

use AppBundle\Entity\OperatingSchedule;
use Cocur\Slugify\Slugify;

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
            new \Twig_SimpleFilter('flashify', array($this, 'doBootstrapFlashify'), array('is_safe' => array('html'))),
            new \Twig_SimpleFilter('excerptify', array($this, 'doExcerptify'))
        );
    }

    protected $bucket;

    public function __construct($aws) {
        $this->bucket = (string) $aws['bucket'];
    }

    public function doExcerptify( $value, $len = 20 ) {
        $str = $value;

        if (strlen($value) > $len) {
            $str = substr($value, 0, $len) . '...';
        }

        return $str;
    }

    public function s3Filter( $key )
    {

        return sprintf('https://%s.%s/%s', $this->bucket, self::AWS_HOST, $key);

    }

    public function doBootstrapFlashify( $errors, $type = 'notice' ) {

        if (!$errors) {
            return '';
        }

        $container = '<div class="alert alert-' . $type . '" role="alert">';

        if (is_array($errors)) {

            if (count($errors) < 1) {
                return '';
            }

            $container .= '<ul>';

            foreach($errors as $error) {
                $container .= '<li class="' . $type . '">';
                $container .= $error;
                $container .= '</li>';
            }

            $container .= '</ul>';
        } else {
            $container .= "<p>${errors}</p>";
        }

        $container .= '</div>';

        return $container;
        /*

  <ul>
    {% for flashMessage in app.session.flashbag.get('notice') %}
      <li class="notice">
        {{ flashMessage }}
      </li>
    {% endfor %}
    {% for flashMessage in app.session.flashbag.get('error') %}
      <li class="error">
        {{ flashMessage }}
      </li>
    {% endfor %}
  </ul>
</div>
*/

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

    public function getName()
    {
        return 'app_extension';
    }
}
