<?php

namespace AppBundle\Controller\API;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use AppBundle\Entity\Business;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class BusinessesController extends Controller
{
    /**
     * @Route("/api/businesses")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $records = $em->getRepository("AppBundle:Business")->findAll();

        $encoders = array(new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());

        $data = array_map(function($business) {
            return $business->toJSON();
        }, $records);

        return new JSONResponse(array(
            'status' => 'ok',
            'data' => $data
        ), Response::HTTP_OK);


    }

    /**
     * @Route("/api/businesses/{id}/treatment/{treatment}/availability")
     * @Method("GET")
     */
    public function treatmentAction($id, $treatment, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $records = $em->getRepository("AppBundle:Business")->findAll();

        $encoders = array(new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());

        $data = array_map(function($business) {
            return $business->toJSON();
        }, $records);

        return new JSONResponse(array(
            'status' => 'ok',
            'data' => $data
        ), Response::HTTP_OK);
    }

    /**
     * @Route("/api/businesses/search")
     * @Method("POST")
     */
     //ENDPOINT FOR BUSINESS/SEARCH
     public function searchAction(Request $request)
     {
        $data    = $this->checkGetPost($request->request);
        $results = $this->runSearch($data);
        return new JSONResponse(array(
                 'status' => 'ok',
                 'data' => json_encode($data),
             ), Response::HTTP_OK);
     }

     private function getServiceByTxAvail()
     {
       $txAvailSet = $this->getDoctrine()
       ->getRepository('AppBundle:TreatmentAvailabilitySet')
       ->find();

       $services = $txAvailSet->getService()->getName();
     }

     private function getTxAvailByService($id)
     {
       $Service = $this->getDoctrine()
                ->getRepository('AppBundle:TreatmentAvailabilitySet')
                ->find($id);
       $TxAvailSets = $Service->getTreatmentAvailabilitySets()->getName();
       return $TxAvailSets;
     }

     private function runSearch($data)
     {
       $sql = $this->getBaseSearchSQL();
       $sql.= $this->$this->getWhere($data);
       return $this->queryRaw($sql,$data);
     }

     private function getBaseSearchSQL()
     {
       $sql = "SELECT  s.id,s.label,s.originalPrice, s.currentPrice,
                       sc.id, sc.label,
                       txav.id,txav.day,txav.time,
                       b.id,b.name,b.slug,b.description,b.reviews,b.yelpLink
                       a.id, a.city, a.state, a.zip
               FROM app_services s
               LEFT JOIN app_treatment_availability_sets txav ON s.id = txav.service_id
               LEFT JOIN app_business b ON s.business_id = b.id
               LEFT JOIN app_service_categories sc ON s.serviceCategory = sc.id
               LEFT JOIN app_address a ON b.address = a.id";
       return $sql;
     }

     private function getWhere($data){
       $where = '';
       $prices= '';
       foreach($data as $k=>$v){
         //if it's a price search
         if($k == 's.currentPrice'){
           //if it's the lower end (price_left)
           if($price != ''){
             $prices .= $k . ' BETWEEN ' . $v;
           }else{
             //if it's the second/higher end (price_right)
             $where .= $prices . " {$v} AND ";
           }
           //increment so we know if we've done the lower price already
           $prices++;
         }else{//other non-BETWEEN (price) searches
           $where .= ' ' . $k .'='. ":{$v} AND " ;
         }
       }
       return rtrim($where,' AND');
     }

     private function queryRaw($sql, $bound = []){
       $stmt = $this->getEntityManager()
                 ->getConnection()
                 ->prepare($sql);
      if($bound){
        foreach($bound as $k => $v){
          $stmt->bindValue($k, $v);
        }
      }
       $stmt->execute();
       return $stmt->fetchAll();
     }

     private function checkGetPost($req){
       //All possible search fields in format: postkey=>table_abbrev.field_name
       $keys= [
               'day'=>'txav.day',
               'time'=>'txav.time',
               'location'=>'b.location',
               'treatmentType'=>'sc.id',
               'treatment'=>'s.id',
               'amount_left'=>'s.currentPrice',
               'amount_right'=>'s.currentPrice'
       ];
       //searched fields and values
       $data=[];
       //build data array of fields present in post from search and their values
       foreach($keys as $key=>$field){
         if($val = $req->get($key,false) ){
           $data[$field] = $val;
         }
       }
       return $data;
     }

}
