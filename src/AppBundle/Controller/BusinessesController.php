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

use Stevenmaguire\Yelp\Exception as YelpException;


class BusinessesController extends Controller
{

    /**
     * @Route("/businesses/{id}/{slug}", name="business_path")
     * @Method({"GET"})
     */
    public function showAction($id, $slug, Request $request)
    {

        $business = $this->businessBySlugAndId($slug, $id);

        try {

            if ($business->getYelpId()) {
                $yelp = $this->get('yelp.factory');
                $response = $yelp->getBusiness($business->getYelpId());

                foreach($response->reviews as $review) {
                    $theReview = Review::fromYelp($review);
                    $business->addReview($theReview);
                }
            }

        } catch (YelpException $e) {
            
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
    	$repo = $em->getRepository("AppBundle:Offer");

        $categories = $em->getRepository("AppBundle:TreatmentCategory");
        $heirarchy = $categories->getHeirarchy();

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
                  $offer = $record[0];
                  $distance = $record['distance'];
              } else {
                  $offer = $record;
                  $distance = false;
              }

              $b = $offer->getBusiness();
              $b->setDistanceFrom($distance);
              $id = $b->getId();

              if (array_key_exists($id, $results)) {

              } else {
                  $results[$id] = $b;
              }

              $results[$id]->addOffer($offer);
          }

        }

        return $this->render('businesses/search.html.twig', array(
            'results' => $results,
            'params' => $params,
            'form' => $form->createView(),
            'categories' => $heirarchy,
            //'services' => Service::getServicesByCategory($services->findAll())//$services->findAll()
        ));
    }

    protected function getSearchForm($request) {

        $allowedDays = array(
            'all' => 'all',
            'today' => 'today',
            'tomorrow' => 'tomorrow',
        );

        $allowedTimes = array(
            'all' => 'all',
            'morning' => 'morning',
            'afternoon' => 'afternoon',
            'evening' => 'evening',
        );

        $date = $request->query->get('date', $allowedDays['all']);
        $time = $request->query->get('time', $allowedTimes['all']);
        $treatment = $request->query->get('treatment', null);
        $min = intval($request->query->get('min', 0));
        $max = intval($request->query->get('max', 500));

        if ($min > 500) {
            $min = 500;
        }

        if ($max < 1) {
            $max = 1;
        }

        if (!in_array($date, $allowedDays)) {
            $date = $allowedDays[0];
        }

        if (!in_array($time, $allowedTimes)) {
            $time = $allowedTimes[0];
        }

        if ($treatment !== null && !is_integer($treatment)) {
            $treatment = intval($treatment);
            if (!$treatment) {
                $treatment = null;
            }
        }

        $defaultData = array(
            'day' => $date, //new \DateTime()
            'time' => $time, //new \DateTime()
            'location' => $request->query->get('location', null),
            'treatment' => $treatment,
            'min' => $min,
            'max' => $max
        );

        $form = $this->createFormBuilder($defaultData)
          ->setMethod('GET')
          ->add('day', 'choice', array(
              'choices' => $allowedDays
          ))
          ->add('time', 'choice', array(
              'choices' => $allowedTimes
          ))
          ->add('location', 'text', array(
              'disabled' => true,
              'data' => 'Los Angeles'
          ))
          ->add('treatment', 'integer')
          ->add('min', 'integer')
          ->add('max', 'integer')
          ->getForm();

        // $form->handleRequest($request); // Handled above
        return $form;

    }

}
