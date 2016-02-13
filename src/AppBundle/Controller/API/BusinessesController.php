<?php

namespace AppBundle\Controller\API;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Controller\ApplicationController as Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use AppBundle\Entity\Business;
use AppBundle\Entity\Address;
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
     * @Method("GET")
     */
     //ENDPOINT FOR BUSINESS/SEARCH
     public function searchAction(Request $request)
     {

        $sort = $request->query->get('sort', 'low');

        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository("AppBundle:Offer");

        $form = $this->getSearchForm($request);
        $params = $form->getData();

        $data = $repo->strongParams($params);
        $result = $repo->findByMulti($data, 1, -1, $sort);

        $records = $result->results;

        $results = array();
        if ($records) {
            // We got the stupid things. Now the weird part is they need to be sorted by business, which acts as the owner
            foreach($records as $record) {
               if (is_array($record)) {
                   // Location was included
                   $offer = $record[0];
                   $distance = $record['distance'];
               } else {
                   $offer = $record;
                   $distance = false;
               }

               $b = $offer->getBusiness();
               $b->setDistanceFrom($distance);
               $id = $b->getId();

               if (array_key_exists($id, $results)) {

               } else {
                   $results[$id] = $b;
               }

               $results[$id]->addOffer($offer);

            }

        }

        $businesses = array();

        foreach ($results as $business) {
            $businesses[] = $business->toJSON(true);
        }

        return new JSONResponse(array(
                 'status' => 'ok',
                 'data' => $businesses,
             ), Response::HTTP_OK);
     }

}
