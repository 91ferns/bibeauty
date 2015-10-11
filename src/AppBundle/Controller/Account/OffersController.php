<?php

namespace AppBundle\Controller\Account;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Controller\ApplicationController as Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use AppBundle\Form\BookingType;
use AppBundle\Form\ServiceType;

use AppBundle\Entity\Business;
use AppBundle\Entity\Service;

use AppBundle\Entity\OfferAvailabilitySet;
use AppBundle\Entity\Offer;

class OffersController extends Controller
{
    /**
     * @Route("/account/offers/{id}/{slug}", name="admin_business_offers_path")
     * @Method("GET")
     */
    public function indexAction($id, $slug, Request $request)
    {
        $offers = $this->getRepo('Offer');
        $business = $this->businessBySlugAndId($slug, $id);

        $offers   = $offers->findByBusiness($business);

        //print_r($offers);

        return $this->render('account/offers/index.html.twig', array(
            'offers' => $offers,
            'business' => $business,
        ));
    }

    /**
     * @Route("/account/offers/show/{id}/{slug}/{bookingId}/", name="admin_show_booking_path")
     * @Method("GET")
     */
    public function showAction($id, $slug, $bookingId, Request $request)
    {
        $booking = new Booking();
        $booking->find($bookingId);

        // replace this example code with whatever you need
        return $this->render('account/offers/show.html.twig', array(
            'booking' => $booking,
            'business' => $booking->getBusiness(),
            'service' => $booking->getService(),
            'serviceType'=> $service->getServiceType(),
        ));
    }


    /**
     * @Route("/account/offers/{id}/{slug}/new", name="admin_new_booking_path")
     * @Method({"GET"})
     */
    public function createAction($id, $slug, Request $request) {
       $booking = new Booking();

       $form = $this->createForm(new BookingType(), $booking);
       $business = $this->businessBySlugAndId($slug, $id);

       return $this->render(
          'account/offers/new.html.twig',
          array(
             'form' => $form->createView(),
             'business' => $business,
          )
       );
    }

    /**
     * @Route("/account/offers/{id}/{slug}/new")
     * @Method("POST")
     */
    public function createCheckAction($id, $slug, Request $request) {

        $booking = new Booking();
        $business = $this->businessBySlugAndId($slug, $id);
        $booking->setBusinessId($business);
        $form = $this->createForm(new BookingType(), $booking);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $txAv = $booking->getTreatmentAvailabilities();
            $em->persist($txAv);

            $em->persist($booking);
            $em->flush();

            return $this->redirectToRoute('admin_bookings_path');

        } else {
            return $this->render('account/offers/index.html.twig', array(
                'form' => $form->createView()
            ));
        }

    }

    /**
     * @Route("/account/offers/{id}/{slug}/{treatmentId}/new", name="admin_treatment_availability_new_path")
     * @Method("POST")
     */
    public function checkCreateAvailabilityAction($id, $slug, $treatmentId, Request $request) {
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

        $discount = $request->request->get('Discount', false);

        if (!$discount) {
            return $this->redirectToRoot($slug, $id, $treatmentId, array(
                'error',
                'You must specify a discount.'
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

        if ((float) $discount >= (float) $treatment->getOriginalPrice()) {
            return $this->redirectToRoot($slug, $id, $treatmentId, array(
                'error',
                'Discount must be cheaper than the original price.'
            ));
        }

        $em = $this->getDoctrine()->getManager();

        $offer = new Offer();
        $offer->setBusiness($business);
        $offer->setTreatment($treatment);
        $offer->setCurrentPrice($discount);
        $em->persist($offer);

        $startDateTime = new \DateTime($date);

        $availabilitySet = new OfferAvailabilitySet();
        $availabilitySet->setOffer($offer);
        $availabilitySet->setStartDate($startDateTime);
        $availabilitySet->setDaysOfTheWeek($recurrenceDOWs);
        $availabilitySet->setTimes($times);
        $availabilitySet->setRecurrenceType($recurrenceType);

        $em->persist($availabilitySet);
        $em->flush();

        // Offer is made. We need to make its availability now
        $matchingDates = $availabilitySet->datesThatMatchRecurrence($date, $times, $recurrenceDOWs, $recurrenceType);
        $availabilitySets = $availabilitySet->datesToAvailabilities($matchingDates, $business);

        $batchSize = 20;

        foreach ($availabilitySets as $i => $availabilitySet) {
            $em->persist($availabilitySet);
            if (($i % $batchSize) === 0) {
                $em->flush();
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
