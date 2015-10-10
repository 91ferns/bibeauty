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
        );
    }

    protected $bucket;

    public function __construct($aws) {
        $this->bucket = (string) $aws['bucket'];
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

    public function ratingStarsFilter( $number ) {
        $stars = array();
        for ($i = 1; $i <= 5; $i++) {
            $active = ($i <= $number) ? 'active' : '';
            $stars[] = sprintf('<i class="fa fa-star %s"></i>', $active);
        }

        return join('', $stars);
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

    public function getName()
    {
        return 'app_extension';
    }
}
