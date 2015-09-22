<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\ServiceCategory;


class LoadServiceCategoryData implements FixtureInterface
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
          $manager->flush();
        }
    }

    public function getOrder()
    {
      return 1;
    }
}
