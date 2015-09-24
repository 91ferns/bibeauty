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

}
