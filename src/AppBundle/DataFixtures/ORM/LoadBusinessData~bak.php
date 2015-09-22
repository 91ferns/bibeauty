<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use AppBundle\Entity\Business;
use AppBundle\Entity\Address;
use AppBundle\Entity\User;
use AppBundle\Entity\Attachment;
use AppBundle\Entity\ServiceCategory;
use AppBundle\Entity\Review;
use AppBundle\Entity\OperatingSchedule;
use AppBundle\Entity\Service;
use AppBundle\Enitity\TreatmentAvailabilitySet;


class LoadBusinessData implements FixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;
    private $businesses  = [
      [
        'Name' => 'Some Salon',
        'YelpLink' => 'http://www.yelp.com/rrr',
        'Description' => 'Lorem Ipsum Dor',
        'Website'     => 'http://www.google.com',
        'Email'       => 'test@email.com',
        'AcceptsCash' => 1,
        'AcceptsCredit'=> 1
        'AverageRating'=> '4'
      ],
    ]; //RELATIONS = Address,Owner,LogoAttachement,HeaderAttachment,ServiceCategory;


    private $addresses = [
        [
          'Street'=> '13 Streetname Way',
          'Line2' => 'Ste. 333',
          'City'  => 'Norwalk',
          'State' => 'CT',
          'Zip'   => '06854',
          'Phone' => '2035555055',
          'Country'=> 'US',
          'Longitude'=> '-73.419839',
          'Lattitude'=>'41.114789',
          'Active'=>1
        ],
    ];

    private $users = [
        [
          'Password'=> '13 Streetname Way',
          'Email' => 'Ste. 333',
          'FirstName'  => 'Norwalk',
          'UserName'   => '06854',
          'IsActive' => '2035555055',
        ],
    ];//RELATIONS = Subscriptions

    private $services = [
        [
          'Label'=> 'Haircut',
          'Description' => 'Get a great haircut',
          'Hours'  => '1',
          'Minutes' => '30',
          'OriginalPrice'   => '100',
          'CurrentPrice' => '50',
        ],
    ];//RELATIONS - Business, TreatmentAvailabilitySet

    private $treatmentAvailabilitySets = [
      [
        'Day'=>'',
        'Time'=>'',
        'IsOpen'=>1
      ],
    ];

    private $attachments1 = [
      [
        'Key'=>'',
        'Size'=>'',
        'Mime'=>1,
        'Processed'=>1,
      ],
    ];//RELATIONS USER

    private $attachments2 = [
      [
        'Key'=>'',
        'Size'=>'',
        'Mime'=>1,
        'Processed'=>1,
      ],
    ];//RELATIONS USER

    private $reviews = [
      [
        'Rating'=>'',
        'VisitedDate'=>'',
        'Content'=>1,
        'Reports'=>0,
        'Verified'=>1
      ],
    ];//RELATIONS POSTER(User), BUSINESS


    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        foreach($this->businesses as $k=>$business){
          list($bus,$s_cat) = $this->buildBusiness($manager,$business,$k);
          //Add Models to Service
          $this->services[$k]['BusinessId']      = $bus;
          $this->services[$k]['ServiceCategory'] = $s_cat;
          //Build Service
          $Service = $this->buildModel('Service',$this->services[$k],$manager);
          //Append Service to TreatmentAvailability Set
          $this->treatmentAvailabilitySets[$k]['ServiceId'] = $Service;
          //Build TreatmentAvailabilitySet
          $TxAvSet = $this->buildModel('TreatmentAvailabilitySet',$this->services[$k],$manager);
        }
    }

    protected function buildBusiness($manager,$bus,$key){
      //Build Relations
      $bus['Address']           = $this->buildModel('Address', $this->addresses[$k],$manager);
      $bus['Owner']             = $this->buildModel('User', $this->owners[$k],$manager);
      $bus['HeaderAttachment']  = $this->buildModel('Attachement', $this->attachments1[$k],$manager);
      $bus['LogoAttachment']    = $this->buildModel('Attachement', $this->attachments2[$k],$manager);
      $bus['Review']            = $this->buildModel('Review', $this->reviews[$k],$manager);
      $bus['ServiceCategory']   = $this->buildModel('ServiceCategory', $this->serviceCategories[$k],$manager);
      $bus['OperatingSchedule'] = $this->buildModel('OperatingSchedule', $this->operatingSchedules[$k],$manager);
      //Build business w/relations
      $bus = $this->buildModel('Business',$bus,$manager);
      //Send back models for further use
      return [$business,$bus['ServiceCategory']];
    }

    protected function buildModel($entity,$data,$manager = null){
      $model = new $entity();
      foreach($data as $field => $val){
        $meth = 'set'.$field;
        $model->$meth($val);
      }
      if($manager != null) $manager->persist($model);
      return $model;
    }
}
