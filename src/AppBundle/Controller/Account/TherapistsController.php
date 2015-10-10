<?php

namespace AppBundle\Controller\Account;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Controller\ApplicationController as Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use AppBundle\Entity\Business;
use AppBundle\Entity\Therapist;

use AppBundle\Form\TherapistType;

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
        $form = $this->createForm(new TherapistType(), new Therapist());
        /*
            'action'   => $this->generateUrl('admin_create_service_category',["slug"=>$slug, "id"=>$id]),
            'business' => $business,
          )
      );*/

        return $this->render('account/therapists/new.html.twig', array(
            'business' => $business,
            'form' => $form->createView(),
        ));

    }

    /**
     * @Route("/account/therapists/{id}/{slug}/new", name="admin_business_therapists_create_path")
     * @Method("POST")
     */
    public function createAction($id, $slug, Request $request) {

        $business = $this->businessBySlugAndId($slug, $id);

        $therapist = new Therapist();
        $form = $this->createForm(new TherapistType(), $therapist);

        $form->handleRequest($request);

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $therapist->setBusiness($business);

            $em->persist($therapist);
            $em->flush();

            return $this->redirectToRoute('admin_business_therapists_path', array(
                'id' => $id,
                'slug' => $slug,
            ));

        } else {
            return $this->render('account/therapists/new.html.twig', array(
                'business' => $business,
                'form' => $form->createView(),
            ));
        }
        /*
            'action'   => $this->generateUrl('admin_create_service_category',["slug"=>$slug, "id"=>$id]),
            'business' => $business,
          )
      );*/

    }



}
