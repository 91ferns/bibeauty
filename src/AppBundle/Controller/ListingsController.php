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
            'results'  => [
                            ['business_name'=>'Some Business Name',
                             'star_img'=>'oops',
                             'reviews_num'=>5, 
                             'map_link'=>'x',
                             'city'=>'Norwalk',
                             'state'=>'CT',
                             'logo'=>'x',
                             'description'=>'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
                             'treatments'=>[
                                ['id'=>1,'name'=>'The Works',
                                 'percent_discount'=>4,'start_dollars'=>20,
                                 'num_remaining'=>4],
                                 ['id'=>2,'name'=>'Another One',
                                 'percent_discount'=>4,'start_dollars'=>20,
                                 'num_remaining'=>3],
                              ]
                            ],
                          ]
        ));
    }
}
