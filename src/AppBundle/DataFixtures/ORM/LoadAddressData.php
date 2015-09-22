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
            'Latitude'=>'41.114789',
            'Active'=>1
          ],
    ];
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        foreach($this->addresses as $address){
            $the_address = new Address();
            $the_address->setStreet($address['Street']);
            $the_address->setLine2($address['Line2']);
            $the_address->setCity($address['City']);
            $the_address->setState($address['State']);
            $the_address->setZip($address['Zip']);
            $the_address->setPhone($address['Phone']);
            $manager->persist($the_address);
        }
        $manager->flush();
    }

    public function getOrder()
    {
      return 4;
    }
}
