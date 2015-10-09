<?php

namespace AppBundle\Controller\Account;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Controller\ApplicationController as Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use AppBundle\Form\ServiceCategoryType;
use AppBundle\Form\ServiceType;

use AppBundle\Entity\Business;
use AppBundle\Entity\ServiceCategory;
use AppBundle\Entity\Service;

class ServicesController extends Controller
{
    /**
     * @Route("/account/services/{id}/{slug}/", name="admin_business_services_path")
     * @Method("GET")
     */
    public function indexAction($id= null,$slug = null, Request $request)
    {
        $business = $this->businessBySlugAndId($slug, $id);

        return $this->render('account/services/index.html.twig', array(
            'business' => $business,
            'services' => $business->getServices(),
        ));

    }

    /**
     * @Route("/account/services/{id}/{slug}/new", name="admin_business_services_new_path")
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

        return $this->render('account/services/new.html.twig', array(
            'business' => $business,
            'form' => $serviceForm->createView(),
        ));

    }

    /**
     * @Route("/account/services/{id}/{slug}/new")
     * @Method("POST")
     */
    public function newSubmit($slug, $id, Request $request) {

        $business = $this->businessBySlugAndId($slug, $id);
        $service = new Service();
        $service->setBusiness($business);
        $form = $this->createForm(new ServiceType(), $service);
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
     * @Route("/account/services/{id}/{slug}/show/{serviceid}", name="admin_service_show_path")
     * @Method("GET")
     */
    public function showAction($id, $slug, $serviceid, Request $request)
    {
        $business = $this->businessBySlugAndId($slug, $id);
        $services = $this->getRepo('Service');
        $service  = $services->findOneBy(['id'=>$serviceid]);

        //$form->createView()
        //$form = $this->createForm(new BusinessType(), $business);
        /*$availabilityForm = $this->createForm(new TreatmentAvailabilityType(), new TreatmentAvailabilitySet(), array(
            'action' => $this->generateUrl('admin_service_availability_new_path',['serviceid'=>$service->getId(),'id'=>$id]),
            'user' => $this->getUser() ? $this->getUser() : null,
        ));*/

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
