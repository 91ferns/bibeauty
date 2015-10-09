<?php

namespace AppBundle\Controller\Account;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Controller\ApplicationController as Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;


class BookingsController extends Controller
{
    /**
     * @Route("/account/businesses/{id}/{slug}/offers", name="admin_business_bookings_path")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {

    }
}
