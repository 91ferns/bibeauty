<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {

        $twilio = $this->get('twilio.factory');
        //$twilio->sendMessage(9149438239, 'Hi stephen');

        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..'),
        ));
    }
    
    /**
     * @Route(“/contact", name=“contact_path")
     */
     
   public function contactAction(Request $request)
   {
       return $this->render(‘default/contact.html.twig', array(
           'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..'),
       ));
   }
   
}
