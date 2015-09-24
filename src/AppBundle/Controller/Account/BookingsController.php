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

class BookingsController extends Controller
{
    /**
     * @Route("/account/bookings", name="admin_bookings_path")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $bookings = $this->getRepo('Booking');
        $businessId = $this->getCurrentBusiness()->getId();
        $bookings   = $bookings->findByBusinessId($businessId);

        return $this->render('account/bookings/index.html.twig', array(
            'bookings' => $bookings
        ));
    }

    /**
     * @Route("/account/bookings/show/{id}/", name="admin_show_booking_path")
     * @Method("GET")
     */
    public function showAction($id, Request $request)
    {
        $booking = new Booking();
        $booking->find($id);

        // replace this example code with whatever you need
        return $this->render('account/bookings/show.html.twig', array(
            'booking' => $booking
        ));
    }


    /**
     * @Route("/account/bookings/new", name="admin_new_booking_path")
     * @Method({"GET"})
     */
    public function createAction() {
       $booking = new Booking();

       $form = $this->createForm(new BookingType(), $booking);

       return $this->render(
          'account/bookings/new.html.twig',
          array(
             'form' => $form->createView()
          )
       );
    }

    /**
     * @Route("/account/bookings/new")
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
            return $this->render('account/businesses/index.html.twig', array(
                'form' => $form->createView()
            ));
        }

    }
    /**
     * @Route("/account/bookings/newTreatment", name="admin_new_availability_path")
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

    private function getRepo($name){
      $em = $this->getDoctrine()->getManager();
      return $em->getRepository("AppBundle:{$name}");
    }
    private function getCurrentBusiness()
    {
      $business = $this->getRepo('Business');
      return $business->findOneBy(['owner'=>$this->getUser()->getId()]);
    }
}
