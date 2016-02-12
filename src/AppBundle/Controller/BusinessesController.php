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

        $sort = $request->query->get('sort', 'low');

        $page = $request->query->get('page', 1);
        $page = intval($page);

        $em = $this->getDoctrine()->getManager();
    	$repo = $em->getRepository("AppBundle:Offer");

        $categories = $em->getRepository("AppBundle:TreatmentCategory");
        $heirarchy = $categories->getHeirarchy();

        $form = $this->getSearchForm($request);
        $params = $form->getData();

        $data = $repo->strongParams($params);
    	$result = $repo->findByMulti($data, $page, 20, $sort);

        $total = $result->count;
        $records = $result->results;
        $pageSize = $result->pageSize;

        $results = array();
        if ($records) {
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

        $totalPages = ceil($total / $pageSize);

        // Processed pages
        $processedPages = array();

        $showNumPages = 4;

        if ($totalPages > $showNumPages * 2) {
            for ($i = $page - 1; ($i >= 1 && $i > $page - $showNumPages); $i--) {
                $processedPages[] = $i;
            }
            // reverse the array for these ones
            $processedPages[] = $page;
            for ($i = $page + 1; ($i <= $totalPages && $i < $page + $showNumPages); $i++) {
                $processedPages[] = $i;
            }
            sort($processedPages);
        } else {
            for ($i = 1; $i <= $totalPages; $i++) {
                $processedPages[] = $i;
            }
        }

        return $this->render('businesses/search.html.twig', array(
            'sort' => $sort,
            'results' => $results,
            'params' => $params,
            'form' => $form->createView(),
            'categories' => $heirarchy,
            'total' => $total,
            'pageSize' => $pageSize,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'processedPages' => $processedPages
            //'services' => Service::getServicesByCategory($services->findAll())//$services->findAll()
        ));
    }

}
