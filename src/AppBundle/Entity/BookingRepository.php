<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * BookingRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class BookingRepository extends EntityRepository
{
    public function recentDeals($limit = 3) {
            $qb = $this->createQueryBuilder('Booking');
            $qb
                ->from('AppBundle:Booking', 'bk')
                ->leftJoin('bk.business','b')
                ->leftJoin('b.address', 'ba')
                ->leftJoin('bk.availability', 'tas')
                ->leftJoin('bk.service', 's')
                ->setMaxResults(3);

            $query = $qb->getQuery()
                ->getResult();

            return $query;
    }

    public function findByMulti($search){
      $qb    = $this->createQueryBuilder('Booking');
      $query = $qb
                ->from('AppBundle:Booking', 'bk')
                ->leftJoin('bk.business','b')
                ->leftJoin('b.address', 'ba')
                ->leftJoin('bk.availability', 'tas')
                ->leftJoin('bk.service', 's');
				//->leftJoin('bk.recurring_appointments', 'r');

      if($this->isAvailabilitySearch($search)){
          $this->filterBookingsByAvailability($query,$qb, $search);
      }

      if($this->isLocationSearch($search)){
        $this->filterBookingsByLocation($query, $search['latitude'], $search['longitude']);
      }

      if($this->isServiceSearch($search)){
        $this->filterBookingsByService($query,$search['serviceType']);
      }

      if($this->isCategorySearch($search)){
          $this->filterBookingsByCategory($query,$search['serviceCategory']);
      }

      if($this->isPriceSearch($search)){
          $this->filterBookingsByPrice($query,$qb,$search['price1'],$search['price2']);
      }


      $result = $query->getQuery()->getResult();
      return $result;
    }


    public function filterBookingsByPrice(&$query, $qb, $price1,$price2){
      $query->add('where',
          $qb->expr()->between(
              's.currentPrice',
              ':price1',
              ':price2'
          )
      )->setParameters([
        'price1' => $price1,
        'price2' => $price2
      ]);
    }

    public function filterBookingsByCategory(&$query,$category){
      $query->add('c')
            ->leftJoin('AppBundle:Category', 'c')
            ->add('where','b.categories = :category')
            ->setParameter('category',$category);
    }
    public function filterBookingsByService(&$query,$serviceType){
      $query->add('st')
            ->leftJoin('AppBundle:Service', 's')
            ->leftJoin('AppBundle:ServiceType','st')
            ->add('where','st.id = :serviceType')
            ->setParameter('serviceType',$serviceType);
    }

    public function filterBookingsByLocation(&$query, $latitude, $longitude){
        $miles = 3959;
        $km = 6371;
        $query
            ->setParameter('latitude', $latitude)
            ->setParameter('longitude', $longitude)
            ->setParameter('unit', $miles)
            //
            ->addSelect("( :unit * ACOS( COS( radians(:latitude) ) * COS( radians( ba.latitude ) ) * COS( radians( ba.longitude ) - radians(:longitude) ) + SIN( radians(ba.latitude) ) * SIN(radians(:latitude)) ) ) as distance")
            ->orderBy('distance', 'asc');
    }

    public function filterBookingsByAvailability(&$query,$qb,$search){
      if($search['day'] !=null){
        $query->add('where', 'tas.date = :day OR r.date = :day2')
              ->setParameter([
                'day' => $search['day'],
                'day2' => $search['day']
              ]);
      }
      if($this->isTimeRangeSearch($search)){
        $where = $this->getTimeWhere();

        $query->add('where',$where
        )->setParameters([
          'starttime' => $search['starttime'],
          'endtime' => $search['endtime'],
          'starttime2' => $search['starttime'],
          'endtime2' => $search['endtime'],
        ]);
      }else if($this->isTimeSearch($search)){
        $time = ($starttime === null) ? $endtime : $starttime;
        $query->andWhere('tas.time = :time || r.time = :time2')
              ->setParameter([
                'time'=>$time,
                'time2'=>$time
                ]);
      }
      $result=$query->getQuery()
                    ->getResult();
      return $result;
  }

    public function getTimeWhere(){
      $orX = $qbr->expr()->orX();
      $orX->add($qb->expr()->between(
          'tas.time',
          ':starttime',
          ':endtime'
      ));
      $orX->add($qb->expr()->between(
          'r.time',
          ':starttime2',
          ':endtime2'
      ));
      return $orX;
    }
    public function isPriceSearch($search){
      return ($this->has('price1',$search)
              || ($this->has('price2',$search))
              ) ? true : false;
    }

    public function isServiceSearch($search) {
      return $this->has('serviceType',$search);
    }
    public function isCategorySearch($search){
      return $this->has('serviceCategory',$search);
    }
    public function isLocationSearch($search){
      return $this->has('location',$search);
    }

    public function isTimeSearch($search){
      return ($this->has('starttime',$search)
              || ($this->has('endtime',$search))
              ) ? true : false;
    }

    public function isTimeRangeSearch($search){
        return ($this->has('starttime',$search)
                &&  ($this->has('endtime',$search))
                ) ? true : false;
    }

    public function isAvailabilitySearch($search){
      foreach(['day','starttime','endtime'] as $field){
          if($this->has($field,$search)) return true;
      }
      return false;
    }
    public function has($key,$search)
    {
        if (is_array($search))
            return array_key_exists($key, $search) ? true : false;
        else
            return false;
    }

    public function strongParams($req) {
        //All possible search fields in format: postkey=>table_abbrev.field_name
        $keys= [
                'day'=>'date',
                'time'=>'starttime',
                'location'=>'location',
                'treatmentType'=>'serviceCategory',
                'treatment'=>'serviceType',
                'amount_left'=>'price1',
                'amount_right'=>'price2'
        ];
        //searched fields and values
        $data=[];
        //build data array of fields present in post from search and their values
        foreach($keys as $key=>$field){

          if(array_key_exists($key, $req) && $val = $req[$key] ){
              if ($field === 'location') {
                 $geo = Address::geocodeZip($val);
                 if ($geo) {
                     $data['latitude'] = $geo->getLatitude();
                     $data['longitude'] = $geo->getLongitude();
                     $data['location'] = array($data['latitude'], $data['longitude']);
                 }
             } else {
                $data[$field] = $val;
            }
          }
        }

        return $data;
    }
  //if()andWhere('r.winner IN (:ids)')  ->setParameter('ids', $ids);
}
