<?php

namespace AppBundle\Controller\Account;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Controller\ApplicationController as Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;


class BookingsController extends Controller
{
    /**
     * @Route("/account/businesses/{id}/{slug}/bookings", name="admin_business_bookings_path")
     * @Method("GET")
     */
    public function indexAction($id, $slug, Request $request)
    {

        $business = $this->businessBySlugAndId($slug, $id);

        $em = $this->getDoctrine()->getManager();
    	$repo = $em->getRepository("AppBundle:Booking");

        $bookings = $repo->findByBusiness($business);

        return $this->render('account/bookings/index.html.twig', array(
            'business' => $business,
            'bookings' => $bookings
        ));

    }
}
