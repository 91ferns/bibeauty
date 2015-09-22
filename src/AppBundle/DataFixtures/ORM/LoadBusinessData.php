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


class LoadBusinessData implements FixtureInterface
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
        'AcceptsCredit'=> 1,
        'AverageRating'=> '4',
        'Owner'        => 'test@test.com',
        'Address'      => '13 Streetname Way',
      ],
    ]; //RELATIONS = Address,Owner,LogoAttachement,HeaderAttachment,ServiceCategory;

    /*private $attachments1 = [
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
*/

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        return true;
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
