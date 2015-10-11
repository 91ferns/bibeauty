<?php

namespace AppBundle\Controller\Account;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Controller\ApplicationController as Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use AppBundle\Entity\Business;
use AppBundle\Entity\ServiceCategory;
use AppBundle\Entity\Service;
use AppBundle\Entity\OfferAvailabilitySet;
use AppBundle\Entity\Offer;
use AppBundle\Entity\RecurringAppointments;

class TreatmentAvailabilityController extends Controller
{

    const MAX_TIME_FORWARD = 31536000; // 60 * 60 * 24 * 365;
    const DAY_IN_SECONDS = 86400; // 60 * 60 * 24

    /**
     * @Route("/account/availability/{id}/{slug}/{treatmentId}/new", name="admin_treatment_availability_new_path")
     * @Method("POST")
     */
    public function newAction($id, $slug, $treatmentId, Request $request) {
        ?><pre><?php
        $business = $this->businessBySlugAndId($slug, $id);

        $date     = $request->request->get('Date', false);
        $times     = $request->request->get('Times', array());
        $recurrenceType = $request->request->get('RecurrenceType', 'never');

        if (!$date || !$times || count($times) < 1 ){
            return $this->redirectToRoot($slug, $id, $treatmentId, array(
                'error',
                'Date and time must be specified.'
            ));
        }

        $recurrenceDOWs = $request->request->get('RecurrenceDates', array());
        $recurrenceDOWs = array_unique($recurrenceDOWs);

        $treatments = $this->getRepo('Treatment');
        $treatment = $treatments->findOneBy(array( 'id'=>$treatmentId ));

        if (!$treatment) {
            return $this->redirectToRoot($slug, $id, $treatmentId, array(
                'error',
                'Treatment not found.'
            ));
        }

        $em = $this->getEm();

        $offer = new Offer();
        $offer->setBusiness($business);
        $offer->setTreatment($treatment);
        $em->persist($offer);

        $availabilitySet = new OfferAvailabilitySet();

        // Offer is made. We need to make its availability now
        $matchingDays = $this->getDaysThatMatchRecurrence($date, $times, $recurrenceDOWs, $recurrenceType);
        // we now need to create the availability set

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

    protected function buildInsertAvailability($treatment,$recurring,$date,$time)
    {
      $availability = new OfferAvailabilitySet();
      $availability->setTreatment($treatment);
      $availability->setRecurring($recurring);
      $availability->setDate(new \DateTime($date));
      //$time = new \DateTime($time);
      //var_dump($time); exit;

      $em = $this->getEm();
      $em->persist($availability);
      $em->flush();
      return $availability;
    }

    protected function dateAndTime($date, $time, $isString = true) {
        if (!$isString) {
            $datestring = strtotime($date);
        } else {
            $datestring = $date;
        }
        // Let's do time ourselves
        list($hour, $minute) = explode(':', $time);

        $hourstring = $hour * 60 * 60;
        $minutestring = $minute * 60;

        return $datestring + $minutestring + $hourstring;
    }

    protected function buildDateString($month, $day, $year) {

        $string = sprintf('%s-%s-%s', $year, $month, $day);
        return strtotime($string);

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
