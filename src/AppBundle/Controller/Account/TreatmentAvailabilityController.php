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
use AppBundle\Entity\Offer;
use AppBundle\Entity\RecurringAppointments;

class TreatmentAvailabilityController extends Controller
{

    /**
     * @Route("/account/availability/{id}/{slug}/{treatmentId}/new", name="admin_treatment_availability_new_path")
     * @Method("POST")
     */
    public function newAction($id, $slug, $treatmentId, Request $request) {
        echo '<pre>';
        $recurring = $this->checkGetRecurrence($request->request);
        $business = $this->businessBySlugAndId($slug, $id);

        $date     = $request->request->get('Day', false);
        $time     = $request->request->get('Time', false);

        if (!$date || !$time ){
            return $this->redirectToRoot($slug, $id, $treatmentId, array(
                'error',
                'Date and time must be specified.'
            ));
        }

        $recurring = $request->request->get('Recurring', false);

        $treatments = $this->getRepo('Treatment');
        $treatment = $treatments->findOneBy(array( 'id'=>$treatmentId ));

        if (!$treatment) {
            return $this->redirectToRoot($slug, $id, $treatmentId, array(
                'error',
                'Treatment not found.'
            ));
        }

        $em = $this->getEm();
        // $em->persist($treatment); // Why?

        $offer  = new Offer();
        $offer->setBusiness($business);
        $offer->setTreatment($treatment);
        $em->persist($offer);

        // Offer is made. We need to make its availability now

        die();

        $availability = $this->buildInsertAvailability($treatment,$recurring,$date,$time);
        $recurring = $this->checkGetRecurrence($request->request);

        $this->buildInsertRecurrences($recurring, $availability, $date, new \DateTime($time));

        $em->flush();

        return $this->redirectToRoot($slug, $id, $treatmentId);
    }

    protected function redirectToRoot($slug, $id, $treatmentId, $flash = false) {
        if ($flash) {
            list($type, $message) = $flash;
            $this->addFlash(
                $type,
                $message
            );
        }
        return $this->redirectToRoute(
            'admin_treatment_show_path',
            array( "slug"=> $slug,
                   "id"=> $id,
                   "treatmentId"=> $treatmentId
            )
        );
    }

    protected function checkGetRecurrence($req){
      if ($req->get('Recurring_Monthly',false)){
        return 11;
      }
      if ($req->get('Recurring_Weekly',false)){
        return 51;
      }
      if ($req->get('Recurring_Daily',false)){
        return 364;
      }
      return false;
    }

    protected function buildInsertRecurrences($recurrences, $availability, $startDate, $startTime){
        $em = $this->getEm();

        switch ($recurrences) {
            case 11:
                $intervalUnit = 'month';
                break;
            case 51:
                $intervalUnit = 'week';
                break;
            case 364:
                $intervalUnit = 'day';
                break;
            default:
                $intervalUnit = 'N/A';
        }
        for ($i=1; $i<=$recurrences; $i++){
            $date = new \DateTime($startDate);
            $appt = new RecurringAppointments();
            $appt->setDate($date->modify($i.$intervalUnit));
            $appt->setTime($startTime);
            $appt->setAvailability($availability);
            $em->persist($appt);
        }
        $em->flush();

    }

    protected function buildInsertAvailability($treatment,$recurring,$date,$time)
    {
      $availability = new TreatmentAvailabilitySet();
      $availability->setTreatment($treatment);
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
