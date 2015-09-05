<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ListingsController extends Controller
{
    /**
     * @Route("/listings", name="listings_path")
     */
    public function indexAction(Request $request)
    {
        return $this->render('listings/index.html.twig', array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..'),
        ));
    }
    /**
     * @Route("/listings_search", name="listings_search_path")
     * @Method({"GET"})
     */
    public function searchAction(Request $request)
    {
        return $this->render('listings/search.html.twig', array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..'),
            'results'  => ['business_name'=>'blah','star_img'=>'oops',
                            'num_reviews'=>5, 'map_link'=>'x',
                            'city'=>'x','state'=>'x','logo'=>'x',
                            'description'=>'x','treatment'=>
                                ['id'=>1,'name'=>'x',
                                 'percent_discount'=>4,'start_dollars'=>20,
                                 'num_remaining'=>4]
                            ],
        ));
    }
}
