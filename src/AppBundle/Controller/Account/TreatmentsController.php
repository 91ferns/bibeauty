<?php

namespace AppBundle\Controller\Account;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Controller\ApplicationController as Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use AppBundle\Form\TreatmentCategoryType;
use AppBundle\Form\TreatmentType;

use AppBundle\Entity\Business;
use AppBundle\Entity\TreatmentCategory;
use AppBundle\Entity\Treatment;

class TreatmentsController extends Controller
{
    /**
     * @Route("/account/treatments/{id}/{slug}/", name="admin_business_treatments_path")
     * @Method("GET")
     */
    public function indexAction($id= null,$slug = null, Request $request)
    {
        $business = $this->businessBySlugAndId($slug, $id);

        return $this->render('account/treatments/index.html.twig', array(
            'business' => $business,
            'treatments' => $business->getTreatments(),
        ));

    }

    /**
     * @Route("/account/treatments/{id}/{slug}/new", name="admin_business_treatments_new_path")
     * @Method("GET")
     */
    public function newAction($id, $slug, Request $request) {

        $business = $this->businessBySlugAndId($slug, $id);
        $treatmentType = $this->createForm(new TreatmentType(), new Treatment());
        /*
            'action'   => $this->generateUrl('admin_create_treatment_category',["slug"=>$slug, "id"=>$id]),
            'business' => $business,
          )
      );*/

        return $this->render('account/treatments/new.html.twig', array(
            'business' => $business,
            'form' => $treatmentType->createView(),
        ));

    }

    /**
     * @Route("/account/treatments/{id}/{slug}/new")
     * @Method("POST")
     */
    public function newSubmit($slug, $id, Request $request) {

        $business = $this->businessBySlugAndId($slug, $id);
        $service = new Treatment();
        $service->setBusiness($business);
        $form = $this->createForm(new TreatmentType(), $service);
        $form->handleRequest($request);

        if ($form->isValid()) {
          $em = $this->getDoctrine()->getManager();
          $em->persist($service);
          $em->flush();
          return $this->redirectToRoute('admin_business_services_path',["slug"=>$slug,"id"=>$id]);
        } else {
          return $this->render('account/businesses/new.html.twig', array(
            'form' => $form->createView()
          ));
        }
    }

    /**
     * @Route("/account/treatments/{id}/{slug}/show/{serviceid}", name="admin_service_show_path")
     * @Method("GET")
     */
    public function showAction($id, $slug, $serviceid, Request $request)
    {
        $business = $this->businessBySlugAndId($slug, $id);
        $services = $this->getRepo('Service');
        $service  = $services->findOneBy(array('id'=>$serviceid));

        // replace this example code with whatever you need
        return $this->render('account/services/show.html.twig', array(
            //'businessForm' => $form->createView(),
            'business' => $business,
            'service'  => $service,
            'slug'=>$slug,
            'id'=>$id
        ));

    }
    protected function getRepo($name)
    {
      $em = $this->getDoctrine()->getManager();
      $repository = $em->getRepository("AppBundle:{$name}");
      return $repository;
    }

    private function getCurrentBusiness($id, $slug)
    {
      //$business = $this->getRepo('Business');
    //return $business->findOneBy(['owner'=>$this->getUser()->getId()]);
    }

}
