<?php

namespace AppBundle\Controller\Account;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use AppBundle\Form\BusinessType;
use AppBundle\Form\AddressType;
use AppBundle\Entity\Business;
use AppBundle\Entity\Address;

class BusinessesController extends Controller
{
    /**
     * @Route("/account/businesses", name="admin_businesses_path")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $business = new Business();
        $address = new Address();

        $form = $this->createForm(new BusinessType(), $business);
        // replace this example code with whatever you need
        return $this->render('account/businesses/index.html.twig', array(
            'form' => $form->createView()
        ));

    }

    /**
     * @Route("/account/businesses")
     * @Method("POST")
     */
    public function createAction(Request $request) {

        $business = new Business();
        $form = $this->createForm(new BusinessType(), $business);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();


            $headerAttachment = $business->getHeaderAttachment();
            $address = $business->getAddress();

            $upload = $this->get('aws.factory')->upload(
                $headerAttachment->attachment->getRealPath(),
                $headerAttachment->attachment->getClientOriginalName()
            );

            if ($upload instanceof Exception) {
                // it failed
            } else {
                $headerAttachment->setUploadState($upload);
                $headerAttachment->setOwner($this->getUser());
                $em->persist($headerAttachment);
            }

            $em->persist($business);
            
            $em->flush();

            return $this->redirectToRoute('homepage');

            $form['attachment']->getData()->move($dir, $someNewFilename);

        }

    }
}
