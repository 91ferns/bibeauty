<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\ServiceType;
use AppBundle\Entity\ServiceCategory;


class LoadServiceData implements FixtureInterface
{

    private $services  = [
      "Pedicure" =>[
          [
            "label"=>"The Best Pedicure Ever",
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
          $serviceTypeId = $this->getServiceTypeIdByName($serviceType);
          foreach($services as $service){
              $service = new Service();
              $service->setLabel($service['label']);
              $service->setDescription($service['description']);
              $service->setHours($service['hours']);
              $service->setMinutes($service['minutes']);
              $service->setOriginalPrice($service['originalPrice']);
              $service->setcurrentPrice($service['originalPrice']);
              $service->setServiceTypeId($serviceTypeId);
              $txAvailability = $this->getSetTreatmentAvailabilitySets($service['availability'],$manager);
              $service->setTreatmentAvailabilitySets($txAvailability);
              $manager->persist($service);
          }
          $manager->flush();
        }
        $manager->flush();
    }

    public function getSetTreatmentAvailabilitySets($availability,$manager)
    {
      $now = new \DateTime("now");
      $time = new \DateTime($availability['time']);

      $txAvailability = new TreatmentAvailabilitySet();
      $txAvailability->setDate($now->modify($availability['date']));
      $txAvailability->setTime($time);
      $txAvailability->setTime($availability['isOpen']);
      $manager->persist($txAvailability);
      $manager->flush();
      return $txAvailability;
    }

    public function getServiceTypeIdByName($serviceType){
        $cat = new ServiceType();
        return $cat->findOneBy(['label'=>$serviceType])->getId();
    }
    public function getOrder()
    {
      return 5;
    }
}
