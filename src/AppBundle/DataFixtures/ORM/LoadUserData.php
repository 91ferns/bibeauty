<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\User;


class LoadUsersData implements OrderedFixtureInterface
{

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
        foreach($this->users as $k=>$user){
              $the_user = new User();
              $the_user->setEmail($user['email']);
              $the_user->selectPassword($user['password']);
              $the_user->setFirstName($user['firstname']);
              $the_user->setlasttName($user['lastname']);
              $manager->persist($the_user);
        }
        $manager->flush();
    }

    public function getOrder()
    {
      return 0;
    }
}
