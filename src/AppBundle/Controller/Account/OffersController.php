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
use AppBundle\Entity\Booking;
use AppBundle\Entity\TreatmentAvailabilitySet as TxAv;

class OffersController extends Controller
{
    /**
     * @Route("/account/offers/{id}/{slug}", name="admin_business_offers_path")
     * @Method("GET")
     */
    public function indexAction($id, $slug, Request $request)
    {
        $offers = $this->getRepo('Offer');
        $business = $this->findBusinessBySlugAndId($slug, $id);

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
    public function createCheckAction(Request $request) {

        $booking = new Booking();
        $business = $this->getCurrentBusiness();
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
     * @Route("/account/offers/newTreatment", name="admin_new_availability_path")
     * @Method({"POST"})
     */
    public function createTreatmentAvailabilitySetAction(Request $request)
    {
      $post      = $request->request->all();
      $date      = $post['Day'];
      $time      = $post['Time'];
      $recurring = $post['Recurring'];

      $Availability = new TreatmentAvailabilitySet();
      $Availability->setDate(new \DateTime($date));
      $Availability->setTime(new \DateTime($time));
      $Availability->setRecurrence($recurring);
      $em = $this->getDoctrine()->getManager();
      $em->persist($Availability);
      $em->flush();
    }
    protected function getRepo($name)
    {
      $em = $this->getDoctrine()->getManager();
      $repository = $em->getRepository("AppBundle:{$name}");
      return $repository;
    }
    private function getCurrentBusiness($id, $slug)
    {
      $business = $this->getRepo('Business');
      return $business->findOneBy(['owner'=>$this->getUser()->getId()]);
    }
}
