<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\User;
use Doctrine\Common\DataFixtures\AbstractFixture;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;



class LoadUsersData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    private $users  = [
      [
        "email"=>"stephen@91ferns.com",
        "password"=>"password",
        "firstname"=>"Stephen",
        "lastname"=>"Parente",
      ],
      [
        "email"=>"paul@91ferns.com",
        "password"=>"password",
        "firstname"=>"Paul",
        "lastname"=>"Caciula",
      ],
      [
        "email"=>"joey@91ferns.com",
        "password"=>"password",
        "firstname"=>"Joe",
        "lastname"=>"Ciervo",
      ],
      [
        "email"=>"richard@bibeauty.com",
        "password"=>"password",
        "firstname"=>"Richard",
        "lastname"=>"McBeathe",
      ],
      [
        "email"=>"stuart@bibeauty.com",
        "password"=>"password",
        "firstname"=>"Stuart",
        "lastname"=>"Sharpe",
      ],
    ];
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $factory = $this->container->get('security.encoder_factory');
        foreach($this->users as $k=>$user){
              $the_user = new User();
              $encoder = $factory->getEncoder($the_user);

              $password = $encoder->encodePassword($user['password'], $the_user->getSalt());

              $the_user->setPassword($password);

              $the_user->setEmail($user['email']);
              $the_user->setFirstName($user['firstname']);
              $the_user->setlastName($user['lastname']);
              $manager->persist($the_user);

              if ($k == 0) $this->addReference('admin-user', $the_user);
              //$manager->merge($this->getReference('admin-user'))
        }
        $manager->flush();
    }

    public function getOrder()
    {
      return 1;
    }
}
