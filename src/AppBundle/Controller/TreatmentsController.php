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

        $availabilities = $availabilityRepo->findTodayAndTomorrowForTreatment($treatment);


        if ($business->getYelpId()) {
            $yelp = $this->get('yelp.factory');
            $response = $yelp->getBusiness('soundview-service-center-mamaroneck');

            if ($response->rating) {
                $business->setAverageRating($response->rating);
            }

            foreach($response->reviews as $review) {
                $theReview = Review::fromYelp($review);
                $business->addReview($theReview);
            }
        }

        return $this->render('treatments/select.html.twig', array(
            'business' => $business,
            'treatment' => $treatment,
            'today' => $availabilities['today'],
            'tomorrow' => $availabilities['tomorrow']
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

        $bookingForm = $this->createForm(new BookingType(), new Booking(), array(
            'action' => $this->generateUrl('listings_search_path', array()),
            'user' => $this->getUser() ? $this->getUser() : null,
        ));

        return $this->render('treatments/book.html.twig', array(
            'business' => $business,
            'availability' => $av,
            'form' => $bookingForm->createView(),
        ));

    }

}
