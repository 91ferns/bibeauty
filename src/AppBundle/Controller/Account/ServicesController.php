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
     * @Route("/account/services/{id}/{slug}", name="admin_business_services_path")
     * @Method("GET")
     */
    public function indexAction($id, $slug, Request $request)
    {
        $business = $this->businessBySlugAndId($slug, $id);

        $categoryForm = $this->createForm(new ServiceCategoryType(), new ServiceCategory(), array(
            'action' => $this->generateUrl('admin_business_create_service_category', array(
                'slug' => $slug,
                'id' => $id
            ))
        ));

        $serviceForm = $this->createForm(new ServiceType(), new Service(), array(
            'action' => $this->generateUrl('admin_business_create_service', array(
                'slug' => $slug,
                'id' => $id
            )),
            'categories' => $business->getCategoriesAsChoices()
        ));

        // replace this example code with whatever you need
        return $this->render('account/services/index.html.twig', array(
            'business' => $business,
            'categoryForm' => $categoryForm->createView(),
            'serviceForm' => $serviceForm->createView()
        ));

    }

    /**
     * @Route("/account/services/{id}/{slug}/{category}", name="admin_business_services_categories_path")
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
     * @Route("/account/services/{id}/{slug}/category", name="admin_business_create_service_category")
     * @Method("POST")
     */
    public function createServiceCategoryAction($id, $slug, Request $request)
    {
        $business = $this->businessBySlugAndId($slug, $id);

        $category = new ServiceCategory();

        $category->setBusiness($business);

        $categoryForm = $this->createForm(new ServiceCategoryType(), $category);
        $categoryForm->handleRequest($request);

        if ($categoryForm->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($category);
            $em->flush();

            return $this->redirectToRoute('admin_path');

        } else {
            return $this->render('account/businesses/show.html.twig', array(
                'categoryForm' => $categoryForm->createView(),
                'business' => $business
            ));
        }

        // replace this example code with whatever you need
        return $this->render('account/businesses/show.html.twig', array(
            'categoryForm' => $categoryForm->createView(),
            'business' => $business
        ));

    }

    /**
     * @Route("/account/services/{id}/{slug}/", name="admin_business_create_service")
     * @Method("POST")
     */
    public function createServiceAction($id, $slug, Request $request)
    {
        $business = $this->businessBySlugAndId($slug, $id);

        $service = new Service();

        $service->setBusiness($business);

        $serviceForm = $this->createForm(new ServiceType(), $service);
        $serviceForm->handleRequest($request);

        if ($serviceForm->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($service);
            $em->flush();

            return $this->redirectToRoute('admin_path');

        } else {
            return $this->render('account/businesses/show.html.twig', array(
                'categoryForm' => $categoryForm->createView(),
                'business' => $business
            ));
        }

        // replace this example code with whatever you need
        return $this->render('account/businesses/show.html.twig', array(
            'categoryForm' => $categoryForm->createView(),
            'business' => $business
        ));

    }

}
