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

class BusinessesController extends Controller
{

    /**
     * @Route("/businesses/{id}/{slug}", name="business_path")
     * @Method({"GET"})
     */
    public function showAction($id, $slug, Request $request)
    {

        $business = $this->businessBySlugAndId($slug, $id);

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

        return $this->render('businesses/show.html.twig', array(
            'business' => $business
        ));
    }

    /**
     * @Route("/businesses/{id}/{slug}/book/{treatment}", name="business_book_path")
     * @Method({"GET"})
     */
    public function bookAction($id, $slug, $treatment, Request $request)
    {
        $business = $this->businessBySlugAndId($slug, $id);

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

        $bookingForm = $this->createForm(new BookingType(), new Booking(), array(
            'action' => $this->generateUrl('listings_search_path', array()),
            'user' => $this->getUser() ? $this->getUser() : null,
        ));

        return $this->render('businesses/book.html.twig', array(
            'business' => $business,
            'treatment' => $treatment,
            'form' => $bookingForm->createView()
        ));
    }

    /**
     * @Route("/businesses/search", name="listings_search_path")
     * @Method({"GET"})
     */
    public function searchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
    	$repo = $em->getRepository("AppBundle:Booking");
        $services = $em->getRepository("AppBundle:ServiceType");
        $categories = $em->getRepository("AppBundle:ServiceCategory");

        $form = $this->getSearchForm($request);
        $params = $form->getData();


         $data = $repo->strongParams($params);
    	 $records = $repo->findByMulti($data);

        $results = array();
        if($records){
          // We got the stupid things. Now the weird part is they need to be sorted by business, which acts as the owner
          foreach($records as $record) {
              if (is_array($record)) {
                  // Location was included
                  $booking = $record[0];
                  $distance = $record['distance'];
              } else {
                  $booking = $record;
                  $distance = false;
              }

              $b = $booking->getBusiness();
              $b->setDistanceFrom($distance);
              $id = $b->getId();

              if (array_key_exists($id, $results)) {

              } else {
                  $results[$id] = $b;
              }

              $results[$id]->addBooking($booking);
          }

        }
        return $this->render('businesses/search.html.twig', array(
            'results' => $results,
            'params' => $params,
            'form' => $form->createView(),
            'categories' => $categories->findAll(),
            'services' => Service::getServicesByCategory($services->findAll())//$services->findAll()
        ));
    }

    protected function getSearchForm($request) {
        $defaultData = array(
            'day' => null, //new \DateTime()
            'time' => null,
            'location' => null,
            'treatmenttype' => '',
            'treatment' => '',
            'min' => 0,
            'max' => 500
        );

        $form = $this->createFormBuilder($defaultData)
          ->setMethod('GET')
          ->add('day', 'date', array(
              'placeholder' => 'Day'
          ))
          ->add('time', 'time')
          ->add('location', 'text', array(
              'disabled' => true,
              'data' => 'Los Angeles'
          ))
          ->add('treatmenttype', 'text')
          ->add('treatment', 'text')
          ->add('min', 'integer')
          ->add('max', 'integer')
          ->getForm();

        $form->handleRequest($request);
        return $form;

    }

}
