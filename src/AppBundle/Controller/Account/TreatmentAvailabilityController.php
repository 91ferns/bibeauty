<?php

namespace AppBundle\Controller\Account;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Controller\ApplicationController as Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use AppBundle\Form\TreatmentAvailabilitySetType;

use AppBundle\Entity\Business;
use AppBundle\Entity\ServiceCategory;
use AppBundle\Entity\Service;
use AppBundle\Entity\TreatmentAvailabilitySet;
use AppBundle\Entity\Booking;

class TreatmentAvailabilityController extends Controller
{

    /**
     * @Route("/account/availability/{id}/{slug}/{serviceid}/new", name="admin_service_availability_new_path")
     * @Method("POST")
     */
    public function newAction($id, $slug, $serviceid, Request $request) {
        $business = $this->businessBySlugAndId($slug, $id);
        $date     = $request->request->get('Day',false);
        $time     = $request->request->get('Time',false) . ':00';
        if(!$date || !$time ){return false;}
        $recurring    = $request->request->get('Recurring',false);
        $services     = $this->getRepo('Service');
        $service      = $services->findOneBy(['id'=>$serviceid]);
          $em = $this->getEm();
        $em->persist($service);
        $availability = $this->buildInsertAvailability($service,$recurring,$date,$time);

        $booking  = new Booking();
        $booking->setBusiness($business);
        $booking->setService($service);
        $booking->setAvailabilityId($availability);
        $em->persist($booking);
        $em->flush();
        return $this->redirectToRoute('admin_service_show_path',["slug"=>$slug,"id"=>$id,"serviceid"=>$serviceid]);
    }

    protected function buildInsertAvailability($service,$recurring,$date,$time)
    {
      $availability = new TreatmentAvailabilitySet();
      $availability->setServiceId($service);
      $availability->setRecurring($recurring);
      $availability->setDate(new \DateTime($date));
      //$time = new \DateTime($time);
      //var_dump($time); exit;
      $availability->setTime(new \DateTime($time));

      $em = $this->getEm();
      $em->persist($availability);
      $em->flush();
      return $availability;
    }
    protected function getRepo($name)
    {
      $em = $this->getEm();
      $repository = $em->getRepository("AppBundle:{$name}");
      return $repository;
    }
    protected function getEm()
    {
      return $this->getDoctrine()->getManager();
    }
    private function getCurrentBusiness($id, $slug)
    {
      $business = $this->getRepo('Business');
      return $business->findOneBy(['owner'=>$this->getUser()->getId()]);
    }

}
