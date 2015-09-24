<?php

namespace AppBundle\Controller\Account;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Controller\ApplicationController as Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use AppBundle\Entity\Business;

class TherapistsController extends Controller
{
    /**
     * @Route("/account/therapists/{id}/{slug}", name="admin_business_therapists_path")
     * @Method("GET")
     */
    public function indexAction($id, $slug, Request $request)
    {
        $business = $this->businessBySlugAndId($slug, $id);

        return $this->render('account/therapists/index.html.twig', array(
            'business' => $business,
            'therapists' => $business->getTherapists(),
        ));

    }

    /**
     * @Route("/account/therapists/{id}/{slug}/new", name="admin_business_therapists_new_path")
     * @Method("GET")
     */
    public function newAction($id, $slug, Request $request) {

        $business = $this->businessBySlugAndId($slug, $id);
        $serviceForm = $this->createForm(new ServiceType(), new Service());
        /*
            'action'   => $this->generateUrl('admin_create_service_category',["slug"=>$slug, "id"=>$id]),
            'business' => $business,
          )
      );*/

        return $this->render('account/therapists/new.html.twig', array(
            'business' => $business,
            'form' => $serviceForm->createView(),
        ));

    }



}
