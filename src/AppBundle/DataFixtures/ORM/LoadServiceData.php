<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\ServiceType;
use AppBundle\Entity\ServiceCategory;
use AppBundle\Entity\Service;
use AppBundle\Entity\TreatmentAvailabilitySet;
use Doctrine\Common\DataFixtures\AbstractFixture;

class LoadServiceData extends AbstractFixture implements OrderedFixtureInterface
{

    private $services  = [
      "Pedicure" =>[
          [
            "description"=>"A super excellent Pedicure",
            "hours"=>0,
            "minutes"=>45,
            "originalPrice"=>"50.00",
            "currentPrice"=>"35.00",
            "availability"=>[
              "date"=> "+1 WEEK",
              "time"=>"12:00:00",
              "isOpen"=>"true",
            ],
        ],
      ]
    ];//Associations -> treatmentAvailabilitySets,serviceType


    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        foreach($this->services as $serviceType=>$services){
          $serviceType = $this->getServiceTypeIdByName($manager, $serviceType);
          foreach($services as $service){
              $the_service = new Service();
              $the_service->setDescription($service['description']);
              $the_service->setHours($service['hours']);
              $the_service->setMinutes($service['minutes']);
              $the_service->setOriginalPrice($service['originalPrice']);
              $the_service->setcurrentPrice($service['originalPrice']);
              $the_service->setServiceType($serviceType);
              $txAvailability = $this->getSetTreatmentAvailabilitySets($service['availability'], $manager);
              $the_service->addTreatmentAvailabilitySet($txAvailability);
              $manager->persist($the_service);
          }
          $manager->flush();
        }
        $manager->flush();
    }

    public function getSetTreatmentAvailabilitySets($availability ,$manager)
    {
      $now = new \DateTime("now");
      $time = new \DateTime($availability['time']);

      $txAvailability = new TreatmentAvailabilitySet();
      $txAvailability->setDate($now->modify($availability['date']));
      $txAvailability->setTime($time);
      $txAvailability->setIsOpen(true);
      $manager->persist($txAvailability);
      $manager->flush();
      return $txAvailability;
    }

    public function getServiceTypeIdByName($em, $serviceType){

    	$records = $em->getRepository("AppBundle:ServiceType");
        return $records->findOneBy(array('label' => $serviceType));


    }
    public function getOrder()
    {
      return 4;
    }
}
