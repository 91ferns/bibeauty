<?php

namespace AppBundle\Controller\Account;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Controller\ApplicationController as Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use AppBundle\Entity\Business;
use AppBundle\Entity\Service;
use AppBundl\Entity\Booking;
use AppBundle\Entity\TreatmentAvailabilitySet as TxAv;

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

    /**
     * @Route("/account/bookings/create", name="admin_service_bookings_create")
     * @Method("POST")
     */
    public function createAction(Request $request)
    {
        $post = $request->request->all();
        $business = new Business();
        $service  = new Service();
        $user     = new User();
        $business->findBy($post['businessId']);
        $service->findBy($post['serviceId']);
        $user->findBy($post['userId']);

        $txAvail  = new TxAv();
        $txAvail->setTime($post['availabilityTime']);
        $txAvail->setDate($post['availabilityDate']);
        $txAvail->setServiceId($post['serviceId']);
        $txAvail->setIsOpen(1);
        $txAvail->persist($txAvail);
        $txAvail->flush();

        $booking = new Booking();
        $booking->setBusinessId($business);
        $booking->setServiceId($service);
        $booking->setAvailabilityId($txAvail);
        $booking->setUserId($user);
        $booking->setUserName($post['name']);
        $booking->setPhone($post['phone']);
        $booking->setEmail($post['email']);
        $booking->persist($booking);
        $booking->flush();

        return $this->redirectToRoute('admin_service_bookings_path');
    }
}
