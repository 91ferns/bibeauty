<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Users;



class LoadAddressData implements FixtureInterface
{

    private $addresses  = [
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
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        foreach($this->addresses as $k=>$address){
              $address = new Address();
              $address->setStreet($address['street']);
              $address->selectLine2($address['line2']);
              $address->setCity($address['city']);
              $address->setState($address['state']);
              $manager->persist($address);
        }
        $manager->flush();
    }

    public function getOrder()
    {
      return 3;
    }
}
