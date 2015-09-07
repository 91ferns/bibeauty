<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Entity\Business;
use AppBundle\Entity\Review;

class BusinessesController extends Controller
{
    /**
     * @Route("/businesses", name="listings_path")
     * @Method({"GET"})
     */
    public function indexAction(Request $request)
    {
        return $this->render('businesses/index.html.twig', array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..'),
        ));
    }

    /**
     * @Route("/business/{slug}/{id}", name="business_path")
     * @Method({"GET"})
     */
    public function showAction($slug, $id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository("AppBundle:Business");

        $query = $repository->createQueryBuilder('b')
            ->where('b.id = :id')
            ->andWhere('b.slug = :slug')
            ->setParameter('slug', $slug)
            ->setParameter('id', $id)
            ->getQuery();

        $business = $query->setMaxResults(1)->getOneOrNullResult();

        if (!$business) {
            throw $this->createNotFoundException('We couldn\'t find that business');
        }

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
     * @Route("/businesses/search", name="listings_search_path")
     * @Method({"GET"})
     */
    public function searchAction(Request $request)
    {
    	$em = $this->getDoctrine()->getManager();
    	$records = $em->getRepository("AppBundle:Business")->findAll();
        $results = [];		
        foreach($records as $k => $record){
        	$results[$k] = $record->toJSON();
        	$results[$k]['logo'] = 'x.png';
        	$results[$k]['treatments']= [
                                	['id'=>1,'name'=>'x',
                                 	 'percent_discount'=>4,
                                 	 'start_dollars'=>20,
                                 	 'num_remaining'=>4]
                            	];
        }
        return $this->render('businesses/search.html.twig', array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..'),
            'results'  => $results/*[(object) ['business_name'=>'blah','star_img'=>'oops',
                            'reviews_num'=>5, 'map_link'=>'x',
                            'city'=>'x','state'=>'x','logo'=>'x',
                            'description'=>'x',
                            'treatments'=>
                                [
                                	['id'=>1,'name'=>'x',
                                 	 'percent_discount'=>4,'start_dollars'=>20,
                                 	 'num_remaining'=>4]
                            		],
                            	]
                          ]*/
        ));
    }
}
