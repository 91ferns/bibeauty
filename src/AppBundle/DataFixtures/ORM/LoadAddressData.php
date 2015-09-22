<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Users;

use AppBundle\Entity\Address;



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
        foreach($this->addresses as $k=>$addr){
              $address = new Address();
              $address->setStreet($addr['Street']);
              $address->setLine2($addr['Line2']);
              $address->setCity($addr['City']);
              $address->setState($addr['State']);
              $address->setZip($addr['Zip']);
              $address->setPhone($addr['Phone']);
              $address->setCountry($addr['Country']);
              $address->setLongitude($addr['Longitude']);
              $address->setLatitude($addr['Lattitude']);

              $manager->persist($address);
        }
        $manager->flush();
    }

    public function getOrder()
    {
      return 3;
    }
}
