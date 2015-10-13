<?php

namespace AppBundle\Controller\Account;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Controller\ApplicationController as Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use AppBundle\Form\BusinessType;
use AppBundle\Form\AddressType;
use AppBundle\Form\ServiceCategoryType;

use AppBundle\Entity\Business;
use AppBundle\Entity\Address;
use AppBundle\Entity\ServiceCategory;

class BusinessesController extends Controller
{
    /**
     * @Route("/account/businesses", name="admin_businesses_path")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository("AppBundle:Business");
        $businesses = $repository->findByOwner($this->getUser());

        // replace this example code with whatever you need
        return $this->render('account/businesses/index.html.twig', array(
            'businesses' => $businesses
        ));

    }

    /**
     * @Route("/account/businesses/new", name="admin_new_businesses_path")
     * @Method("GET")
     */
    public function newAction(Request $request)
    {
        $business = new Business();
        $address  = new Address();

        $form = $this->createForm(new BusinessType(), $business);
        // replace this example code with whatever you need
        return $this->render('account/businesses/new.html.twig', array(
            'form' => $form->createView()
        ));

    }

    /**
     * @Route("/account/businesses/new")
     * @Method("POST")
     */
    public function createAction(Request $request) {

        $business = new Business();
        $form = $this->createForm(new BusinessType(), $business);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $headerAttachment = $business->getHeaderAttachment();
            $logoAttachment = $business->getLogoAttachment();
            $address = $business->getAddress();

            $business->setOwner($this->getUser());

            try {

                $yelp = $this->get('yelp.factory');
                $response = $yelp->getBusiness($business->getYelpId());

                if ($response->rating) {
                    $business->setAverageRating($response->rating);
                }

                if ($headerAttachment) {

                    $upload = $this->get('aws.factory')->upload(
                        $headerAttachment->attachment->getRealPath(),
                        $headerAttachment->attachment->getClientOriginalName()
                    );

                    if ($upload instanceof Exception) {
                        throw $upload;
                    }

                    $headerAttachment->setUploadState($upload);
                    $headerAttachment->setOwner($this->getUser());

                    $em->persist($headerAttachment);

                }

                if ($logoAttachment) {

                    $upload = $this->get('aws.factory')->upload(
                        $logoAttachment->attachment->getRealPath(),
                        $logoAttachment->attachment->getClientOriginalName()
                    );

                    if ($upload instanceof Exception) {
                        throw $upload;
                    }

                    $logoAttachment->setUploadState($upload);
                    $logoAttachment->setOwner($this->getUser());

                    $em->persist($logoAttachment);

                }

            } catch (Exception $e) {
                
            }


            $em->persist($business);
            $em->flush();

            return $this->redirectToRoute('admin_business_path', array(
                'id' => $business->getId(),
                'slug' => $business->getSlug()
            ));

        } else {
            return $this->render('account/businesses/new.html.twig', array(
                'form' => $form->createView()
            ));
        }

    }

    /**
     * @Route("/account/businesses/{id}/{slug}", name="admin_business_path")
     * @Method("GET")
     */
    public function showAction($id, $slug, Request $request)
    {
        $business = $this->businessBySlugAndId($slug, $id);
        //$form->createView()
        $form = $this->createForm(new BusinessType(), $business);


        // replace this example code with whatever you need
        return $this->render('account/businesses/show.html.twig', array(
            'businessForm' => $form->createView(),
            'business' => $business
        ));

    }

    /**
     * @Route("/account/businesses/{id}/{slug}", name="admin_business_edit_path")
     * @Method("POST")
     */
    public function editAction($id, $slug, Request $request)
    {
        $business = $this->businessBySlugAndId($slug, $id);

        $form = $this->createForm(new BusinessType(), $business);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $headerAttachment = $business->getHeaderAttachment();
            $logoAttachment = $business->getLogoAttachment();
            $address = $business->getAddress();

            try {

                if ($headerAttachment && $headerAttachment->attachment) {

                    $upload = $this->get('aws.factory')->upload(
                        $headerAttachment->attachment->getRealPath(),
                        $headerAttachment->attachment->getClientOriginalName()
                    );

                    if ($upload instanceof Exception) {
                        throw $upload;
                    }

                    $headerAttachment->setUploadState($upload);
                    $headerAttachment->setOwner($this->getUser());

                    $em->persist($headerAttachment);

                }

                if ($logoAttachment && $logoAttachment->attachment) {

                    $upload = $this->get('aws.factory')->upload(
                        $logoAttachment->attachment->getRealPath(),
                        $logoAttachment->attachment->getClientOriginalName()
                    );

                    if ($upload instanceof Exception) {
                        throw $upload;
                    }

                    $logoAttachment->setUploadState($upload);
                    $logoAttachment->setOwner($this->getUser());

                    $em->persist($logoAttachment);

                }

            } catch (Exception $e) {

            }


            $em->persist($business);
            $em->flush();

            return $this->redirectToRoute('admin_business_path', array(
                'id' => $business->getId(),
                'slug' => $business->getSlug()
            ));

        } else {
            return $this->redirectToRoute('admin_business_path', array(
                'id' => $business->getId(),
                'slug' => $business->getSlug()
            ));
        }

    }

}
