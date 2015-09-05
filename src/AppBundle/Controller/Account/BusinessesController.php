<?php

namespace AppBundle\Controller\Account;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Form\BusinessType;
use AppBundle\Form\AddressType;
use AppBundle\Entity\Business;
use AppBundle\Entity\Address;

class BusinessesController extends Controller
{
    /**
     * @Route("/account/businesses", name="admin_businesses_path")
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
}
