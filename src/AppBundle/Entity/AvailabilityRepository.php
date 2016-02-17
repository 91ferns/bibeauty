<?php

namespace AppBundle\Entity;

use Snc\RedisBundle\Doctrine\Cache\RedisCache;
use Predis\Client;

use Doctrine\ORM\EntityRepository;

/**
 * AvailabilityRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AvailabilityRepository extends EntityRepository
{

    protected function getCacheLifetime() {
        return 3600;;
    }

    protected function getCacheDriver() {
        # init predis client
        $predis = new RedisCache();
        $predis->setRedis(new Client());

        return $predis;
    }

    public function findTodayAndTomorrowForTreatment($treatment) {

        $today = new \DateTime('today');
        $tomorrow = new \DateTime('tomorrow');

        $tomorrow->setTime(23, 59, 59);

        $qb = $this->createQueryBuilder('a');
        $qb
            ->innerJoin('a.treatment','t')
            ->innerJoin('a.availabilitySet', 'oas')
            ->innerJoin('oas.offer', 'o')
            ->innerJoin('t.business', 'b')
            ->andWhere($qb->expr()->between(
                'a.date',
                ':todaystart',
                ':tomorrowend'
            ))
            ->setParameters(array(
                'todaystart' => $today,
                'tomorrowend' => $tomorrow
            ))
            ->andWhere('o.isOpen = true')
            ->andWhere('a.active = true')
            ->andWhere('b.active = true')
            ->andWhere('t = :treatment')
            ->setParameter('treatment', $treatment)
            ->addOrderBy('a.date', 'ASC')
            ->addOrderBy('o.currentPrice',  'ASC')
            ;

        $query = $qb->getQuery()
            ->setResultCacheDriver($this->getCacheDriver())
            ->setResultCacheLifetime($this->getCacheLifetime())
            ;

        $results = $query->getResult();

        // Sort through today and tomororw

        $todayArray = array();
        $tomorrowArray = array();

        $cmpFormat = 'Y-m-d';

        foreach($results as $result) {
            $date = $result->getDate();

            $timestamp = $date->getTimestamp();

            $f = $date->format($cmpFormat);
            if ($f == $today->format($cmpFormat)) {
                if (array_key_exists($timestamp, $todayArray)) {

                } else {
                    $todayArray[$timestamp] = $result;
                }
            } elseif ($f === $tomorrow->format($cmpFormat)) {
                if (array_key_exists($timestamp, $todayArray)) {

                } else {
                    $tomorrowArray[$timestamp] = $result;
                }
            }
        }

        // Filter each of these arrays

        return array('today' => $todayArray, 'tomorrow' => $tomorrowArray);

        return $results;

    }
}
