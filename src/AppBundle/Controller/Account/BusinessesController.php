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

use AppBundle\Controller\AdminAwareController;


use Stevenmaguire\Yelp\Exception as YelpException;

class BusinessesController extends Controller implements AdminAwareController
{
    /**
     * @Route("/account/businesses", name="admin_businesses_path")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {

        // replace this example code with whatever you need
        return $this->render('account/businesses/index.html.twig', array());

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

                $yelp = $this->get('yelp.factory');
                $response = $yelp->getBusiness($business->getYelpId());

                if ($response->rating) {
                    $business->setAverageRating($response->rating);
                }

            } catch (YelpException $e) {
                $business->setAverageRating(null);
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
            'form' => $form->createView(),
            'business' => $business
        ));

    }

    /**
     * @Route("/account/businesses/{id}/{slug}/delete", name="admin_business_delete_path")
     * @Method("GET")
     */
    public function deleteAction($id, $slug, Request $request)
    {
        $business = $this->businessBySlugAndId($slug, $id);

        $em = $this->getDoctrine()->getManager();
        $em->remove($business);

        $em->flush();

        // replace this example code with whatever you need
        return $this->redirectToRoute('admin_businesses_path');

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

                $yelp = $this->get('yelp.factory');
                $response = $yelp->getBusiness($business->getYelpId());

                if ($response->rating) {
                    $business->setAverageRating($response->rating);
                }

            } catch (YelpException $e) {
                $business->setAverageRating(null);
            } catch (Exception $e) {

            }


            $em->persist($business);
            $em->flush();

            return $this->redirectToRoute('admin_businesses_path');

        } else {
            return $this->redirectToRoute('admin_businesses_path');
        }

    }

}
