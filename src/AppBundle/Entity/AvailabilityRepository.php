<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * AvailabilityRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AvailabilityRepository extends EntityRepository
{
    public function findTodayAndTomorrowForTreatment($treatment){
      
    }
}