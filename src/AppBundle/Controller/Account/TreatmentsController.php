<?php

namespace AppBundle\Controller\Account;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Routing\Router;
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
        $business   = $this->businessBySlugAndId($slug, $id);
        $em         = $this->getDoctrine()->getManager();
        $treatments = $em->getRepository("AppBundle:Business")->findBusinessTreatmentsCategory($business);
        return $this->render('account/treatments/index.html.twig', array(
            'business' => $business,
            'treatments' => $treatments,
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
          return $this->redirectToRoute('admin_business_treatments_path',["slug"=>$slug,"id"=>$id]);
        } else {
          return $this->render('account/businesses/new.html.twig', array(
            'form' => $form->createView()
          ));
        }
    }

    /**
     * @Route("/account/treatments/{id}/{slug}/show/{treatmentId}", name="admin_business_treatments_show_path")
     * @Method({"GET","POST"})
     */
    public function showAction($id, $slug, $treatmentId, Request $request)
    {
        $business = $this->businessBySlugAndId($slug, $id);
        $treatments = $this->getRepo('Treatment');
        $treatment  = $treatments->findOneBy(array('id'=>$treatmentId));

        $treatmentType = $this->createForm(new TreatmentType(), $treatment);
        $treatmentType->handleRequest($request);

        if ($treatmentType->isValid()) {
          $em = $this->getDoctrine()->getManager();
          $em->persist($treatment);
          $em->flush();
          $this->get('session')->getFlashBag()->add('notice','Treatment successfully updated.');
          return $this->redirectToRoute('admin_business_treatments_show_path',["slug"=>$slug,"id"=>$id,"treatmentId"=>$treatmentId]);
        }

        // replace this example code with whatever you need
        return $this->render('account/treatments/show.html.twig', array(
            //'businessForm' => $form->createView(),
            'business' => $business,
            'treatment'  => $treatment,
            'slug'=> $slug,
            'id'=> $id,
            'form' => $treatmentType->createView(),
        ));

    }

    /**
     * @Route("/account/treatments/{id}/{slug}/remove/{treatmentId}", name="admin_delete_treatment")
     * @Method("GET")
     */
    public function deleteOffer($id,$slug,$treatmentId,Request $request)
    {
      $em = $this->getDoctrine()->getManager();
      $tx = $em->getRepository("AppBundle:Treatment")->findOneBy(['id'=>$treatmentId]);
      $em->persist($tx);
      $em->remove($tx);
      $em->flush();
      $this->get('session')->getFlashBag()->add('notice','Treatment successfully removed.');
      return $this->redirectToRoute('admin_business_treatments_path',["slug"=>$slug,"id"=>$id]);
 }
}
