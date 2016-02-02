<?php

namespace AdminBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Component\HttpFoundation\Request;

use AppBundle\Controller\ApplicationController as Controller;

use AppBundle\Entity\Product;
use AppBundle\Form\ProductType;

class ProductsController extends Controller
{
    /**
     * @Route("/account/products", name="admin_products_path")
     * @Method({"GET"})
     */
    public function indexAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository("AppBundle:Product");

        // replace this example code with whatever you need
        return $this->render('account/products/index.html.twig', array(
            'products' => $repository->findAll(),
        ));

    }

    /**
     * @Route("/account/products/new", name="admin_new_products_path")
     * @Method({"GET"})
     */
    public function newAction(Request $request) {

        $product = new Product();
        $form = $this->createForm(new ProductType(), $product);
        // replace this example code with whatever you need

        // replace this example code with whatever you need
        return $this->render('account/products/show.html.twig', array(
            'form' => $form->createView()
        ));

    }

    /**
     * @Route("/account/products/new")
     * @Method({"POST"})
     */
    public function createAction(Request $request) {

        $product = new Product();
        $form = $this->createForm(new ProductType(), $product);

        $form->handleRequest($request);

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $thumbnail = $product->getThumbnail();

            try {

                if ($thumbnail) {

                    $upload = $this->get('aws.factory')->upload(
                        $thumbnail->attachment->getRealPath(),
                        $thumbnail->attachment->getClientOriginalName()
                    );

                    if ($upload instanceof Exception) {
                        throw $upload;
                    }

                    $thumbnail->setUploadState($upload);
                    $thumbnail->setOwner($this->getUser());

                    $em->persist($thumbnail);

                }

            } catch (\Exception $e) {

            }

            $em->persist($product);
            $em->flush();

            $this->redirectToRoute('admin_product_path', ['id' => $product->getId()]);

        } else {

            return $this->render('account/products/show.html.twig', array(
                'form' => $form->createView()
            ));

        }

        // replace this example code with whatever you need
        return $this->redirectToRoute('admin_products_path');

    }

    /**
     * @Route("/account/products/{id}", name="admin_product_path")
     * @Method({"GET"})
     */
    public function showAction($id, Request $request) {

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository("AppBundle:Product");

        $product = $repository->findOneById($id);

        if (!$product) {
            return $this->redirectToRoute('admin_products_path');
        }

        $form = $this->createForm(new ProductType(), $product);

        // replace this example code with whatever you need
        return $this->render('account/products/show.html.twig', array(
            'form' => $form->createView(),
        ));

    }

    /**
     * @Route("/account/products/{id}")
     * @Method({"POST"})
     */
    public function updateAction($id, Request $request) {

        // replace this example code with whatever you need
        return $this->redirectToRoute('admin_product_path', ['id' => $id]);

    }

    /**
     * @Route("/account/products/{id}/delete", name="admin_delete_product_path")
     * @Method({"POST"})
     */
    public function deleteAction($id, Request $request) {

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository("AppBundle:Product");

        $product = $repository->findOneById($id);

        if (!$product) {
            return $this->redirectToRoute('admin_products_path');
        }

        $em->remove($product);
        $em->flush();

        return $this->redirectToRoute('admin_products_path');

    }

}
