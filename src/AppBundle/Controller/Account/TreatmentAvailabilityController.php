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
use AppBundle\Entity\RecurringAppointments;

class TreatmentAvailabilityController extends Controller
{

    /**
     * @Route("/account/availability/{id}/{slug}/{serviceid}/new", name="admin_service_availability_new_path")
     * @Method("POST")
     */
    public function newAction($id, $slug, $serviceid, Request $request) {
        echo '<pre>';
        $recurring = $this->checkGetRecurrence($request->request);
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
        $recurring = $this->checkGetRecurrence($request->request);
        if($recurring){
          $this->buildInsertRecurrences($recurring, $availability, $date, new \DateTime($time));
        }
        $booking  = new Booking();
        $booking->setBusiness($business);
        $booking->setService($service);
        $booking->setAvailability($availability);
        $em->persist($booking);
        $em->flush();
        return $this->redirectToRoute('admin_service_show_path',["slug"=>$slug,"id"=>$id,"serviceid"=>$serviceid]);
    }

    protected function checkGetRecurrence($req){
      if($req->get('Recurring_Monthly',false)){
        return 11;
      }
      if($req->get('Recurring_Weekly',false)){
        return 51;
      }
      return false;
    }

    protected function buildInsertRecurrences($recurrences, $availability,$startDate,$startTime){
      $em = $this->getEm();
      $intervalUnit = ($recurrences == 11) ? ' month' : ' week';
      for($i=1; $i<=$recurrences; $i++){
        $date = new \DateTime($startDate);
        $appt = new RecurringAppointments();
        $appt->setDate($date->modify($i.$intervalUnit));
        $appt->setTime($startTime);
        $appt->setAvailability($availability);
        $em->persist($appt);
      }
      $em->flush();
    }

    protected function buildInsertAvailability($service,$recurring,$date,$time)
    {
      $availability = new TreatmentAvailabilitySet();
      $availability->setService($service);
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
