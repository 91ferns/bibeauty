<?php

namespace AppBundle\Controller\API;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
     * @Method("POST")
     */
     //ENDPOINT FOR BUSINESS/SEARCH
     public function searchAction(Request $request)
     {
        $data    = $this->checkGetPost($request->request);
        $results = $em->getRepository("AppBundle:Bookings")->findByMulti($data);
        return new JSONResponse(array(
                 'status' => 'ok',
                 'data' => json_encode($results),
             ), Response::HTTP_OK);
     }

}
