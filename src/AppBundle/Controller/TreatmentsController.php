<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Controller\ApplicationController as Controller;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Entity\Business;
use AppBundle\Entity\Address;
use AppBundle\Entity\Review;
use AppBundle\Entity\Service;

use AppBundle\Entity\Booking;
use AppBundle\Form\BookingType;

class TreatmentsController extends Controller
{

    /**
     * @Route("/businesses/{id}/{slug}/treatments/{treatment}", name="treatments_path")
     * @Method({"GET"})
     */
    public function showAction($id, $slug, $treatment, Request $request)
    {

        $business = $this->businessBySlugAndId($slug, $id);

        $em = $this->getDoctrine()->getManager();
    	$treatmentRepo = $em->getRepository("AppBundle:Treatment");

        $treatment = $treatmentRepo->findOneBy(array(
            'business' => $business->getId(),
            'id' => $treatment
        ));

        if (!$treatment) {

        }

        $availabilityRepo = $em->getRepository('AppBundle:Availability');

        //$availabilities = $availabilityRepo->findTodayAndTomorrowForTreatment($treatment);
        $availabilities = $availabilityRepo->findTodayAndTomorrowForTreatment($treatment);

        return $this->render('treatments/select.html.twig', array(
            'business' => $business,
            'treatment' => $treatment,
            'today' => $availabilities['today'],
            'tomorrow' => $availabilities['tomorrow'],
        ));
    }

    /**
     * @Route("/availabilities/redirect", name="availabilities_redirect_path")
     * @Method({"POST"})
     */
    public function doShowAction(Request $request) {
        // All we do here is redirect based on the availability that was chosen. Everything else is static

        $availability = $request->request->get('Availability', false);

        if (!$availability) {
            return;
        }

        $em = $this->getDoctrine()->getManager();
    	$availabilityRepo = $em->getRepository("AppBundle:Availability");

        $av = $availabilityRepo->findOneBy(array('id' => $availability));

        if (!$av) {
            return;
        }

        return $this->redirectToRoute('book_treatment_path', array(
            'slug' => $av->getBusiness()->getSlug(),
            'id' => $av->getBusiness()->getId(),
            'availability' => $av->getId(),
        ));

    }

    /**
     * @Route("/businesses/{id}/{slug}/availability/{availability}", name="book_treatment_path")
     * @Method({"GET"})
     */
    public function bookAction($id, $slug, $availability, Request $request) {

        $business = $this->businessBySlugAndId($slug, $id);
        $em = $this->getDoctrine()->getManager();
    	$availabilityRepo = $em->getRepository("AppBundle:Availability");

        $av = $availabilityRepo->findOneBy(array('id' => $availability));

        $bookingForm = $this->getBookingForm($av, $business);

        return $this->render('treatments/book.html.twig', array(
            'business' => $business,
            'availability' => $av,
            'form' => $bookingForm->createView(),
        ));

    }

    /**
     * @Route("/businesses/{id}/{slug}/availability/{availability}", name="do_book_treatment_path")
     * @Method({"POST"})
     */
    public function doBookAction($id, $slug, $availability, Request $request) {

        $business = $this->businessBySlugAndId($slug, $id);
        $em = $this->getDoctrine()->getManager();
    	$availabilityRepo = $em->getRepository("AppBundle:Availability");

        $av = $availabilityRepo->findOneById($availability);

        if ($av->getActive() === false) {

            $bookingForm = $this->getBookingForm($av, $business);

            return $this->render('treatments/book.html.twig', array(
                'business' => $business,
                'availability' => $av,
                'form' => $bookingForm->createView(),
                'err' => 'Someone has taken that booking already'
            ));
        }

        $booking = new Booking();

        $form = $this->createForm(new BookingType(), $booking);
        $form->handleRequest($request);

        $booking->setAvailability($av);

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            if ($this->getUser()) {
                $booking->setUser($this->getUser());
            }

            $em->persist($booking);
            $av->setActive(false);

            $em->flush();

            $twilio = $this->get('twilio.factory');
            $mailer = $this->get('mailer.factory');
            $twilio->bookingNotification($booking);
            $mailer->bookingNotification($booking);

            return $this->redirectToRoute('do_book_confirm_path', array(
                'id' => $id,
                'slug' => $slug,
                'booking' => $av->getId(),
            ));

        } else {
            return $this->render('treatments/book.html.twig', array(
                'business' => $business,
                'availability' => $av,
                'form' => $form->createView(),
            ));
        }

    }

    protected function getBookingForm($av, $business) {
        return $this->createForm(new BookingType(), new Booking(), array(
            'action' => $this->generateUrl('do_book_treatment_path', array(
                'availability' => $av->getId(),
                'id' => $business->getId(),
                'slug' => $business->getSlug(),
            )),
            'user' => $this->getUser() ? $this->getUser() : null,
        ));
    }

    /**
     * @Route("/businesses/{id}/{slug}/availability/{booking}/confirm", name="do_book_confirm_path")
     * @Method({"GET"})
     */
    public function confirmBookPath($id, $slug, $booking, Request $request) {

        $business = $this->businessBySlugAndId($slug, $id);
        $em = $this->getDoctrine()->getManager();
    	$availabilityRepo = $em->getRepository("AppBundle:Availability");
                $avail = $availabilityRepo->findOneById($booking);
        return $this->render('treatments/confirm.html.twig', array(
            'business' => $business,
            //'booking' => $booking,
            'availability' => $avail,
        ));

    }

}
