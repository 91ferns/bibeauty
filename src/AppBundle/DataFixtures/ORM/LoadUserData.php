<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Users;



class LoadUsersData implements FixtureInterface
{

    private $users  = [
      [
        "email"=>"test@t.com",
        "password"=>"test",
        "firstname"=>"Larry",
        "lastname"=>"Smith",
      ],
      [
        "email"=>"test2@t.com",
        "password"=>"test",
        "firstname"=>"John",
        "lastname"=>"Smith",
      ],
    ];
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        foreach($this->users as $k=>$user){
              $user = new User();
              $user->setEmail($user['email']);
              $user->selectPassword($user['password']);
              $user->setFirstName($user['firstname']);
              $user->setlasttName($user['lastname']);
              $manager->persist($user);
        }
        $manager->flush();
    }

    public function getOrder()
    {
      return 3;
    }
}
