<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\ServiceCategory;
use Doctrine\Common\DataFixtures\AbstractFixture;


class LoadServiceCategoryData extends AbstractFixture implements OrderedFixtureInterface
{

    private $categories  = [
      'Hair','Hair Removal','Massage',
      'Nails','Face','Body',
      //'AcceptsCredit','AverageRating'
    ];
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        foreach($this->categories as $category){
          $cat = new ServiceCategory();
          $cat->setLabel($category);

          $manager->persist($cat);
          $this->addReference($category, $cat);
          $manager->flush();
        }
    }

    public function getOrder()
    {
      return 2;
    }
}
