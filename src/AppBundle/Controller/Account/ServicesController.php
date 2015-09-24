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
use AppBundle\Entity\ServiceType as servType;
use AppBundle\Entity\Service;

class ServicesController extends Controller
{
    /**
     * @Route("/account/services/{id}/{slug}/", name="admin_services_path", defaults={"slug" = null,"id"=null})
     * @Route("/account/services", name="admin_services_path_no_business")
     * @Method("GET")
     */
    public function indexAction($id= null,$slug = null, Request $request)
    {
        $business = $this->businessBySlugAndId($slug, $id);

        //$business = $this->getCurrentBusiness();
        $categoryForm = $this->createForm(new ServiceCategoryType(), new ServiceCategory(),  [
            'action'   => $this->generateUrl('admin_create_service_category',["slug"=>$slug, "id"=>$id]),
            'business' => $business,
          ]
        );
        $serviceForm = $this->createForm(new ServiceType(), new Service(), [
            'action'   => $this->generateUrl('admin_create_service',["slug"=>$slug, "id"=>$id]),
            'business' => $business,
          ]
        );
        // replace this example code with whatever you need
        return $this->render('account/services/index.html.twig', array(
            'business' => $business,
            'categoryForm' => $categoryForm->createView(),
            'serviceForm' => $serviceForm->createView()
        ));

    }

    /**
     * @Route("/account/services/{id}/{slug}/{category}", name="admin_services_categories_path")
     * @Method("GET")
     */
    public function showCategoryAction($id, $slug, $category, Request $request) {

        $business = $this->businessBySlugAndId($slug, $id);
        $category = $this->categoryInBusiness($category, $business);

        return $this->render('account/services/category.html.twig', array(
            'business' => $business,
            'category' => $category
        ));

    }

    /**
     * @Route("/account/services/{id}/{slug}/category", name="admin_create_service_category")
     * @Method({"GET", "POST"})
     */
    public function createServiceCategoryAction($id,$slug,Request $request)
    {

        $business = $this->businessBySlugAndId($slug, $id);
        $category = new ServiceCategory();
        $category->setBusiness($business);

        $categoryForm = $this->createForm(new ServiceCategoryType(), $category,['business'=>$business]);
        $categoryForm->handleRequest($request);
//var_dump($category);exit;
        if ($categoryForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            //$
            //$em->persist($category);
            //var_dump($business);
            foreach($categoryForm['label']->getData() as $cat){
              $business->addServiceCategory($cat);
              $em->persist($cat);
            }

            $em->persist($business);
            $em->flush();
            return $this->redirectToRoute('admin_path');


        } else {
            return $this->render('account/businesses/show.html.twig', array(
                'categoryForm' => $categoryForm->createView(),
                'business' => $business
            ));
        }

        // replace this example code with whatever you need
        return $this->render('account/services/show.html.twig', array(
            'categoryForm' => $categoryForm->createView(),
            'business'     => $business
        ));

    }

    /**
     * @Route("/account/services/", name="admin_create_service")
     * @Method("POST")
     */
    public function createServiceAction($business, Request $request)
    {
        //$business = $this->businessBySlugAndId($slug, $id);

        $service = new servType();

        $serviceForm = $this->createForm(new ServiceType(), $service, array(
            'business' => $business
        ));
        $serviceForm->handleRequest($request);

        if ($serviceForm->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($service);
            $em->flush();

            $this->addFlash(
                'notice',
                'Your service was created'
            );

            return $this->redirectToRoute('admin_path');

        } else {

            $this->addFlash(
                'error',
                $serviceForm->getErrorsAsString()
            );
            return $this->redirectToRoute('admin_business_services_path', array(
                'slug' => $business->getSlug(),
                'id' => $business->getId()
            ));
        }

        // replace this example code with whatever you need
        return $this->render('account/businesses/show.html.twig', array(
            'categoryForm' => $categoryForm->createView(),
            'business' => $business
        ));

    }

    private function getRepo($name){
      $em = $this->getDoctrine()->getManager();
      return $em->getRepository("AppBundle:{$name}");
    }
    private function getCurrentBusiness()
    {
      $business = $this->getRepo('Business');
      return $business->findOneBy(['owner'=>$this->getUser()->getId()]);
    }

}
