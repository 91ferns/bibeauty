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
        $em = $this->getDoctrine()->getManager();

        $repository = $em->getRepository("AppBundle:TreatmentCategory");

        $treatments = $repository->getHeirarchy();
        /*
            'action'   => $this->generateUrl('admin_create_treatment_category',["slug"=>$slug, "id"=>$id]),
            'business' => $business,
          )
      );*/

        return $this->render('account/treatments/new.html.twig', array(
            'business' => $business,
            'treatments' => $treatments
        ));

    }

    /**
     * @Route("/account/treatments/{id}/{slug}/new")
     * @Method("POST")
     */
    public function newSubmit($slug, $id, Request $request) {

        $business = $this->businessBySlugAndId($slug, $id);

        $post = $request->get('autoadd');
        $em = $this->getDoctrine()->getManager();
        $failed = 0;
        $total = count($post);

        $errors = array();

        foreach($post as $treatmentData) {
            $treatmentData = (object) $treatmentData;

            $treatmentCategoryId = intval($treatmentData->treatmentCategory);
            $tcRepo = $em->getRepository('AppBundle:TreatmentCategory');

            $treatmentCategory = $tcRepo->findOneById($treatmentCategoryId);

            if (!$treatmentCategory) {
                $failed++;
                continue;
            }

            if (!$treatmentData->name) {
                $total--;
                continue;
            }

            $treatment = new Treatment();
            $treatment->setBusiness($business);
            $treatment->setTreatmentCategory($treatmentCategory);
            $treatment->setName($treatmentData->name);
            $treatment->setDuration(intval($treatmentData->duration));
            $treatment->setOriginalPrice(floatval($treatmentData->originalPrice));
            $treatment->setDescription($treatmentData->description);

            $validator = $this->get('validator');
            $validationErrors = $validator->validate($treatment);

            if (count($validationErrors) < 1) {
                $em->persist($treatment);
            } else {
                $errors[] = (string) $validationErrors;
                $failed[] = $treatment;
            }
        }

        $numFailed = count($failed);

        if ($numFailed === $total) {
            // All failed
            $this->addFlash(
                'error',
                implode(' ', $errors)
            );
        } elseif ($numFailed > 0) {
            // Some failed
            $this->addFlash(
                'error',
                'We could not add ' . $failed . ' of your new treatments.'
            );
            $em->flush();
        } else {
            $this->addFlash(
                'notice',
                'Successfully added all of your new treatments.'
            );
            $em->flush();
            // All succeeded
            return $this->redirectToRoute('admin_business_treatments_path',["slug"=>$slug,"id"=>$id]);
        }

        return $this->render('account/treatments/show.html.twig', array(
            //'businessForm' => $form->createView(),
            'business' => $business,
            'failed'  => $failed,
        ));

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
