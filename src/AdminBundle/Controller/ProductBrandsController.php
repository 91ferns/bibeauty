<?php

namespace AdminBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Component\HttpFoundation\Request;

use AppBundle\Controller\ApplicationController as Controller;

use AppBundle\Entity\ProductBrand;
use AppBundle\Form\ProductBrandType;

class ProductBrandsController extends Controller
{
    /**
     * @Route("/account/product-brands", name="admin_product_brands_path")
     * @Method({"GET"})
     */
    public function indexAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository("AppBundle:ProductBrand");

        // replace this example code with whatever you need
        return $this->render('account/product-brands/index.html.twig', array(
            'productbrands' => $repository->findAll(),
        ));

    }

    /**
     * @Route("/account/product-brands/new", name="admin_new_product_brands_path")
     * @Method({"GET"})
     */
    public function newAction(Request $request) {

        $brand = new ProductBrand();
        $form = $this->createForm(new ProductBrandType(), $brand);
        // replace this example code with whatever you need

        // replace this example code with whatever you need
        return $this->render('account/product-brands/show.html.twig', array(
            'form' => $form->createView()
        ));

    }

    /**
     * @Route("/account/product-brands/new")
     * @Method({"POST"})
     */
    public function createAction(Request $request) {

        $brand = new ProductBrand();
        $form = $this->createForm(new ProductBrandType(), $brand);

        $form->handleRequest($request);

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $em->persist($brand);
            $em->flush();

            $this->redirectToRoute('admin_product_brand_path', ['id' => $brand->getId()]);

        } else {

            return $this->render('account/product-brands/show.html.twig', array(
                'form' => $form->createView()
            ));

        }

        // replace this example code with whatever you need
        return $this->redirectToRoute('admin_product_brands_path');

    }

    /**
     * @Route("/account/product-brands/{id}", name="admin_product_brand_path")
     * @Method({"GET"})
     */
    public function showAction($id, Request $request) {

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository("AppBundle:ProductBrand");

        $brand = $repository->findOneById($id);

        if (!$product) {
            return $this->redirectToRoute('admin_product_brands_path');
        }

        $form = $this->createForm(new ProductBrandType(), $brand);

        // replace this example code with whatever you need
        return $this->render('account/product-brands/show.html.twig', array(
            'form' => $form->createView(),
        ));

    }

    /**
     * @Route("/account/product-brands/{id}")
     * @Method({"POST"})
     */
    public function updateAction($id, Request $request) {

        // replace this example code with whatever you need
        return $this->redirectToRoute('admin_product_brand_path', ['id' => $id]);

    }

    /**
     * @Route("/account/product-brands/{id}/delete", name="admin_delete_product_brand_path")
     * @Method({"POST"})
     */
    public function deleteAction($id, Request $request) {

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository("AppBundle:ProductBrand");

        $product = $repository->findOneById($id);

        if (!$product) {
            return $this->redirectToRoute('admin_product_brands_path');
        }

        $em->remove($product);
        $em->flush();

        return $this->redirectToRoute('admin_product_brands_path');

    }

}
