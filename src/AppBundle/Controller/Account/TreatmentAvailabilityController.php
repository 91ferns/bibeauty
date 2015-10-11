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

        $offer  = new Offer();
        $offer->setBusiness($business);
        $offer->setTreatment($treatment);
        $em->persist($offer);

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

    protected function getDaysThatMatchRecurrence($startDate, $times, $DOWs = array(), $type = false) {

        $dates =  array();

        // First one that matches is always the start date
        foreach($times as $time) {
            $string = $this->dateAndTime($startDate, $time, false);
            if ($string) {
                $x = new \DateTime();
                $x->setTimestamp($string);
                $dates[] = $x;
            }
        }

        // Now we have the ones from the start date,
        // so we need to match the rules for the next days
        if (!$type) {
            return $dates; // if there is no recurrence specified, we are done.
        }

        $starttime = strtotime($startDate);

        if ($type === 'daily') {
            // If it is daily, we just need to iterate day by day for a year.
            for ($i = $starttime + self::DAY_IN_SECONDS;
                 $i < $starttime + self::MAX_TIME_FORWARD;
                 $i = $i + self::DAY_IN_SECONDS) {
                // $i is the new "startdate"
                foreach($times as $time) {
                    $string = $this->dateAndTime($i, $time);

                    if ($string) {
                        $x = new \DateTime();
                        $x->setTimestamp($string);
                        $dates[] = $x;
                    }
                }
            }

            return $dates;
        }

        // Now, monthly
        // This one we need to get the number of days in a given month, or just reformat
        // our start time so the month increments 12 times
        if ($type === 'monthly') {

            // let's get the month number
            $startMonth = date($starttime, 'n');
            $startDay = date($starttime, 'j');
            $startYear = date($starttime, 'Y');

            for ($i = 1; $i <= 12; $i++) {

                $rawMonth = $startMonth + $i;
                $currentYear = $startYear + floor($rawMonth / 12);
                $currentMonth = $rawMonth % 12;
                if ($currentMonth === 0) {
                    $currentMonth = 12;
                }
                $daysInMonth = date($this->buildDateString($currentYear, $currentMonth, 1), 't');

                if ($daysInMonth < $startDay) {
                    continue;
                }

                $buildDate = $this->buildDateString($currentYear, $currentMonth, $startDay);

                foreach($times as $time) {
                    $string = $this->dateAndTime($buildDate, $time);
                    if ($string) {
                        $x = new \DateTime();
                        $x->setTimestamp($string);
                        $dates[] = $x;
                    }
                }

            }

            return $dates;
        }

        if ($type === 'weekly') {

            // By far the hardest one. Need to iterate day by day and check to see if the day of the week matches the allowed DOWs
            $DOWs = array_map('strtolower', $DOWs);

            for ($i = $starttime + self::DAY_IN_SECONDS;
                 $i < $starttime + self::MAX_TIME_FORWARD;
                 $i = $i + self::DAY_IN_SECONDS) {
                // $i is the new "startdate"

                $dow = strtolower(date($i, 'l'));

                if (!in_array($dow, $DOWs)) {
                    continue;
                }

                foreach($times as $time) {
                    $string = $this->dateAndTime($i, $time);
                    if ($string) {
                        $x = new \DateTime();
                        $x->setTimestamp($string);
                        $dates[] = $x;
                    }
                }
            }

            return $dates;

        }

        return $dates;
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
