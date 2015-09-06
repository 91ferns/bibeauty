<?php

// src/AppBundle/Entity/Address.php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="app_addresses")
 */
class Address {

   /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
   protected $id;

   /**
     * @ORM\Column(type="string", length=255)
     */
   protected $street;

   /**
     * @ORM\Column(type="string", length=255)
     */
   protected $line2 = '';

   /**
     * @ORM\Column(type="string", length=150)
     */
   protected $city;

   /**
     * @ORM\Column(type="string", length=4)
     */
   protected $state;

   /**
     * @ORM\Column(type="string", length=12)
     */
   protected $zip;

   /**
     * @ORM\Column(type="string", length=14)
     */
   protected $phone;

   /**
     * @ORM\Column(type="string", length=50)
     */
   protected $country = 'US';

   /**
     * @ORM\Column(type="float", length=10, nullable=true)
     */
   protected $longitude = 0.0;

   /**
     * @ORM\Column(type="float", length=10, nullable=true)
     */
   protected $latitude = 0.0;

   /**
     * @ORM\Column(type="boolean")
     */
   protected $active = false;

   /**
    * @ORM\PrePersist
    */
   public function geocode() {
       $curl     = new \Ivory\HttpAdapter\CurlHttpAdapter();
       $geocoder = new \Geocoder\Provider\GoogleMaps($curl);

       try {
           $result = $geocoder->limit(1)->geocode($this->getAddressString())->first();

           $this->active = true;
           $this->latitude = $result->getLatitude();
           $this->longitude = $result->getLongitude();
       } catch (\Exception $e) {
           $this->active = false;
       }

   }

   public static function getCountries() {
    return array(
        'US' => 'United States'
    );
    }

    public static function haversine() {

    }

    public static function getStates() {
        $states = array(
            'AL'=>'Alabama',
            'AK'=>'Alaska',
            'AZ'=>'Arizona',
            'AR'=>'Arkansas',
            'CA'=>'California',
            'CO'=>'Colorado',
            'CT'=>'Connecticut',
            'DE'=>'Delaware',
            'DC'=>'District of Columbia',
            'FL'=>'Florida',
            'GA'=>'Georgia',
            'HI'=>'Hawaii',
            'ID'=>'Idaho',
            'IL'=>'Illinois',
            'IN'=>'Indiana',
            'IA'=>'Iowa',
            'KS'=>'Kansas',
            'KY'=>'Kentucky',
            'LA'=>'Louisiana',
            'ME'=>'Maine',
            'MD'=>'Maryland',
            'MA'=>'Massachusetts',
            'MI'=>'Michigan',
            'MN'=>'Minnesota',
            'MS'=>'Mississippi',
            'MO'=>'Missouri',
            'MT'=>'Montana',
            'NE'=>'Nebraska',
            'NV'=>'Nevada',
            'NH'=>'New Hampshire',
            'NJ'=>'New Jersey',
            'NM'=>'New Mexico',
            'NY'=>'New York',
            'NC'=>'North Carolina',
            'ND'=>'North Dakota',
            'OH'=>'Ohio',
            'OK'=>'Oklahoma',
            'OR'=>'Oregon',
            'PA'=>'Pennsylvania',
            'RI'=>'Rhode Island',
            'SC'=>'South Carolina',
            'SD'=>'South Dakota',
            'TN'=>'Tennessee',
            'TX'=>'Texas',
            'UT'=>'Utah',
            'VT'=>'Vermont',
            'VA'=>'Virginia',
            'WA'=>'Washington',
            'WV'=>'West Virginia',
            'WI'=>'Wisconsin',
            'WY'=>'Wyoming',
        );

        return $states;
    }

    public function getAddressString() {

        $string = $this->getStreet();
        $string .= ',' . $this->getCity();
        $string .= ',' . $this->getState();
        $string .= ',' . $this->getCountry();

        return $string;
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
     * Set street
     *
     * @param string $street
     * @return Address
     */
    public function setStreet($street)
    {
        $this->street = $street;

        return $this;
    }

    /**
     * Get street
     *
     * @return string
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * Set line2
     *
     * @param string $line2
     * @return Address
     */
    public function setLine2($line2)
    {
        if (empty($line2)) {
            $this->line2 = '';
        } else {
            $this->line2 = $line2;
        }

        return $this;
    }

    /**
     * Get line2
     *
     * @return string
     */
    public function getLine2()
    {
        return $this->line2;
    }

    /**
     * Set city
     *
     * @param string $city
     * @return Address
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set state
     *
     * @param string $state
     * @return Address
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set zip
     *
     * @param string $zip
     * @return Address
     */
    public function setZip($zip)
    {
        $this->zip = $zip;

        return $this;
    }

    /**
     * Get zip
     *
     * @return string
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * Set phone
     *
     * @param string $phone
     * @return Address
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set country
     *
     * @param string $country
     * @return Address
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set longitude
     *
     * @param float $longitude
     * @return Address
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * Get longitude
     *
     * @return float
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Set latitude
     *
     * @param float $latitude
     * @return Address
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * Get latitude
     *
     * @return float
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set active
     *
     * @param boolean $active
     * @return Address
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
        return $this->active === true;
    }

    public function getGoogleMapsUrl() {
        return sprintf('http://maps.google.com/?q=%s', urlencode($this->getFullAddressString()));
    }

    public function getFullAddressString() {
        return sprintf(
            '%s, %s, %s, %s',
            $this->getStreet(),
            $this->getCity(),
            $this->getState(),
            $this->getCountry()
        );
    }

}
