<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\ServiceCategory;


class LoadServiceCategoryData implements FixtureInterface
{

    private $categories  = [
      'hair','hair removal','massage',
      'nails','face','body',
      'AcceptsCredit','AverageRating'
    ];
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        foreach($this->categories as $k=>$category){
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
