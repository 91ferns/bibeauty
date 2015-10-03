<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {

        //$twilio = $this->get('twilio.factory');
        //$twilio->sendMessage(9149438239, 'Hi Stuart');

        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..'),
        ));
    }

    /**
     * @Route("/about", name="about_path")
     */
     public function aboutAction(Request $request)
     {
        return $this->render('default/about.html.twig', array());
     }

    /**
     * @Route("/privacy", name="privacy_path")
     */
    public function privacyAction(Request $request)
    {
        return $this->render('default/privacy.html.twig', array());
    }

    /**
     * @Route("/terms", name="terms_path")
     */
    public function termsAction(Request $request)
    {
        return $this->render('default/terms.html.twig', array());
    }

    /**
     * @Route("/contact", name="contact_path")
     * @Method({"GET"})
     */
    public function contactAction(Request $request)
    {

        return $this->render('default/contact.html.twig', array(
            'form' => $this->getContactForm()->createView()
        ));
    }

    /**
     * @Route("/contact", name="do_contact_path")
     * @Method({"POST"})
     */
    public function doContactAction(Request $request)
    {

        $form = $this->getContactForm();
        $form->handleRequest($request);

        if ($form->isValid()) {

            $data = $form->getData();

            $name = $data['name'];
            $email = $data['email'];
            $subject = $data['subject'];
            $description = $data['description'];

            $message = \Swift_Message::newInstance()
                ->setSubject('New Contact on BiBeauty: ' . $subject)
                ->setFrom('info@bibeauty.com')
                ->setReplyTo($email)
                ->setTo('hello@bibeauty.com') //
                ->setBody(
                    $this->renderView(
                        'emails/contact.html.twig',
                        array(
                            'name' => $name,
                            'email' => $email,
                            'subject' => $subject,
                            'description' => $description,
                        )
                    ),
                'text/html'
                );

            $x = $this->get('mailer')->send($message);

            return $this->render('default/contact.html.twig', array(
                'message' => "Thank you for your submission"
            ));

        } else {
            return $this->render('default/contact.html.twig', array(
                'message' => "There was an error submitting your data",
                'form' => $form->createView()
            ));
        }
    }

    private function getContactForm() {
        $defaultData = array('message' => 'Type your message here');
        $form = $this->createFormBuilder($defaultData)
            ->add('name', 'text')
            ->add('email', 'email')
            ->add('subject', 'text')
            ->add('description', 'textarea')
            ->getForm();

        return $form;

    }

}
