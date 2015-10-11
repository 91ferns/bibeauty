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

    /**
     * @Route("/account/availability/{id}/{slug}/{treatmentId}/new", name="admin_treatment_availability_new_path")
     * @Method("POST")
     */
    public function newAction($id, $slug, $treatmentId, Request $request) {
        // this is absolutely something that would be offloaded to the worker
        $business = $this->businessBySlugAndId($slug, $id);

        $date = $request->request->get('Date', false);
        $times = $request->request->get('Times', array());
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

        $em = $this->getDoctrine()->getManager();

        $offer = new Offer();
        $offer->setBusiness($business);
        $offer->setTreatment($treatment);
        $em->persist($offer);

        $startDateTime = new \DateTime($date);

        $availabilitySet = new OfferAvailabilitySet();
        $availabilitySet->setOffer($offer);
        $availabilitySet->setStartDate($startDateTime);
        $availabilitySet->setDaysOfTheWeek($recurrenceDOWs);
        $availabilitySet->setTimes($times);
        $availabilitySet->setRecurrenceType($recurrenceType);

        $em->persist($availabilitySet);

        // Offer is made. We need to make its availability now
        $matchingDates = $availabilitySet->datesThatMatchRecurrence($date, $times, $recurrenceDOWs, $recurrenceType);
        $availabilitySets = $availabilitySet->datesToAvailabilities($matchingDates, $business);

        $batchSize = 20;

        foreach ($availabilitySets as $i => $availabilitySet) {
            $em->persist($availabilitySet);
            if (($i % $batchSize) === 0) {
                $em->flush();
                $em->clear(); // Detaches all objects from Doctrine!
            }
        }

        $em->flush(); //Persist objects that did not make up an entire batch
        $em->clear();

        // we now need to create the availability set

        return $this->redirectToRoot($slug, $id, $treatmentId, array(
            'notice',
            'Successfully created ' + count($availabilitySets) . ' availabilities'
        ));
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


}
