<?php

namespace AppBundle\Controller\Account;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Controller\ApplicationController as Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use AppBundle\Entity\Business;

class BookingsController extends Controller
{
    /**
     * @Route("/account/bookings/{id}/{slug}/", name="admin_service_bookings_path")
     * @Method("GET")
     */
    public function indexAction($id, $slug, Request $request)
    {
        $service = $this->getServiceBySlugAndId($slug, $id);

        // replace this example code with whatever you need
        return $this->render('account/services/index.html.twig', array(
            'business' => $business
        ));

    }

}