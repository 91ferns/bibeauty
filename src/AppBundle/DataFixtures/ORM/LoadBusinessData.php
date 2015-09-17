<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use AppBundle\Entity\Business;
use AppBundle\Entity\Address;
use AppBundle\Entity\User;

class LoadBusinessData implements FixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

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

        $address  = new Address();
        $owner    = new User();
        $business = new Business();
        $business->setSlug('admin')
                 ->setName('')
                 ->setYelpLink('')
                 ->setDescription('')
                 ->setCreatedAt('')
                 ->setAddress($address)
                 ->setOwner($owner);
        $manager->persist($business);

        /*$encoder = $this->container
            ->get('security.encoder_factory')
            ->getEncoder($business)
        ;
        $business->setPassword($encoder->encodePassword('secret', $business->getSalt()));*/

        $manager->persist($business);
        $manager->flush();
    }

    protected function getAddress($data)
    {
      $address = new Address();
      foreach($data as $field => $val){

      }
    }
}
