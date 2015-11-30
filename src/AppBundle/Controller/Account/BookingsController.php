<?php

namespace AppBundle\Controller\Account;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Controller\ApplicationController as Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use AppBundle\Entity\Booking;

use AppBundle\Controller\AdminAwareController;

class BookingsController extends Controller implements AdminAwareController
{
    /**
     * @Route("/account/businesses/{id}/{slug}/bookings", name="admin_business_bookings_path")
     * @Method("GET")
     */
    public function indexAction($id, $slug, Request $request)
    {
        //$this->createBooking();
        $business = $this->businessBySlugAndId($slug, $id);;
        $em = $this->getDoctrine()->getManager();
    	  $repo = $em->getRepository("AppBundle:Booking");

        $bookings = $repo->findByBusiness($business);

        return $this->render('account/bookings/index.html.twig', array(
            'business' => $business,
            'bookings' => $bookings,
            'statuses' => [1=>'unconfirmed',2=>'confirmed',3=>'canceled'],
        ));
    }

    /**
     * @Route("/account/businesses/{id}/{slug}/bookings/status", name="admin_business_bookings_status")
     * @Method("POST")
     */
    public function updateStatusAction($id, $slug, Request $request)
    {
      $req       = $request->request;
      $bookingid = $req->get('bookingid');
      $status    = $req->get('bookingstatus');
      $em        = $this->getDoctrine()->getManager();
      $booking   = $em->getRepository("AppBundle:Booking")->findOneBy(['id'=>$bookingid]);
      $booking->setStatus($status);

      $twilio = $this->get('twilio.factory');

      if ($status === 3) {
          // Cancelled
          $twilio->bookingCancelledNotification($booking);
      } elseif ($status === 2) {
          // Booking confirmed
          $twilio->bookingConfirmedNotification($booking);
      }

      $em->flush();
      return $this->redirectToRoute('admin_business_bookings_path',['id'=>$id,'slug'=>$slug]);
      //return new Response();
    }

    /** REMOVE. FOR TESTING ONLY. **/
  /*  private function createBooking()
    {
      $booking = new Booking();
      $em        = $this->getDoctrine()->getManager();
      $avail     = $em->getRepository("AppBundle:Availability")->findOneBy(['id'=>13]);
      $user      = $em->getRepository("AppBundle:User")->findOneBy(['id'=>1]);
      $em->persist($avail);
      $em->persist($user);
      $booking->setName('Larry Smith');
      $booking->setEmail('lsmith@test.com');
      $booking->setPhone('2222222222');
      $booking->setAvailability($avail);
      $booking->setUser($user);
      $booking->setAutomaticFields();
      $em->persist($booking);
      $em->flush();
    }*/
}
