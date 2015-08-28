<?php
// src/AppBundle/Controller/AuthenticationController.php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Entity\User;
use AppBundle\Form\UserType;

class AuthenticationController extends Controller {

    /**
     * @Route("/login", name="login_route")
     * @Method({"GET"})
     */
    public function loginAction(Request $request) {

        $authenticationUtils = $this->get('security.authentication_utils');

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render(
            'authentication/login.html.twig',
            array(
                // last username entered by the user
                'last_username' => $lastUsername,
                'error'         => $error,
            )
        );

    }

   /**
    * @Route("/login_check", name="login_check")
    * @Method({"GET", "POST"})
    */
   public function loginCheckAction() {
      // this controller will not be executed,
      // as the route is handled by the Security system
   }

   /**
    * @Route("/signup", name="signup_route")
    * @Method({"GET"})
    */
   public function signupAction() {
      // this controller will not be executed,
      // as the route is handled by the Security system
      $user = new User();

      $form = $this->createForm(new UserType(), $user);

      return $this->render(
         'authentication/signup.html.twig',
         array(
            'form' => $form->createView()
         )
      );
   }

   /**
    * @Route("/signup", name="signup_check")
    * @Method({"POST"})
    */
   public function signupCheckAction(Request $request) {
      // this controller will not be executed,
      // as the route is handled by the Security system

      $user = new User();
      $factory = $this->get('security.encoder_factory');
      $encoder = $factory->getEncoder($user);

      $form = $this->createForm(new UserType(), $user);
      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {

         $password = $encoder->encodePassword($user->getPassword(), $user->getSalt());
         $user->setPassword($password);

         $em = $this->getDoctrine()->getManager();

         $em->persist($user);
         $em->flush();

         return $this->redirectToRoute('homepage');
      } else {

         return $this->render(
            'authentication/signup.html.twig',
            array(
               'form' => $form->createView()
            )
         );

      }


   }

}
