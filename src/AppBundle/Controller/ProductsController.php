<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Component\HttpFoundation\Request;

use AppBundle\Entity\Treatment;
use AppBundle\Entity\Review;


class ProductsController extends ApplicationController
{
    /**
     * @Route("/products", name="products_path")
     */
    public function indexAction(Request $request)
    {

        $queryTerm = $request->query->get('term', '');

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository("AppBundle:Product");

        // replace this example code with whatever you need
        return $this->render('products/index.html.twig', array(
            'products' => $repository->bySearchTerm( $queryTerm ),
        ));

    }


}
