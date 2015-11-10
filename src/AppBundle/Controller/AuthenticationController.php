<?php
// src/AppBundle/Controller/AuthenticationController.php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use AppBundle\Form\PasswordResetType;

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
    * @Route("/logout", name="logout_path")
    * @Method({"GET", "POST"})
    */
   public function logoutAction() {
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

         $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
         $this->get('security.token_storage')->setToken($token);

         return $this->redirectToRoute('admin_businesses_path');
      } else {

         return $this->render(
            'authentication/signup.html.twig',
            array(
               'form' => $form->createView()
            )
         );

      }


   }
    /**
    * @Route("/forgotpassword", name="forgot_password")
    * @Method({"GET"})
    */
   function forgotPassword(Request $request){
            return $this->render(
                'authentication/forgotpassword.html.twig'
            );
   }

    /**
    * @Route("/forgotpasswordreset/{token}", name="forgot_password_token")
    * @Method({"GET","POST"})
    */
    function forgotPasswordToken(Request $request,$token =null){
        $em = $this->getDoctrine()->getManager();
        //var_dump($user); exit;
        $form = $this->createForm(new PasswordResetType(),null,['token'=>$token]);//,$user
        $factory = $this->get('security.encoder_factory');

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $user = $em->getRepository("AppBundle:User")->findOneBy(['resetToken'=>$token]);
                //var_dump($user); exit;
        $encoder = $factory->getEncoder($user);

            $password = $encoder->encodePassword($request->request->get('password'), $user->getSalt());
            //$user->updatePassword($user,$password);
            $user->setPassword($password);

            //$em = $this->getDoctrine()->getManager();

            $em->persist($user);
            $em->flush();

            $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
            $this->get('security.token_storage')->setToken($token);

            return $this->redirectToRoute('homepage');
        }
        return $this->render(
                'authentication/resetpassword.html.twig',
                array(
                    'form' => $form->createView(),
                    'error'=> false,                )
        );

    }

   /**
    * @Route("/forgotpassword", name="forgot_password_email")
    * @Method({"POST"})
    */
    function forgotPasswordEmail(Request $request){
        $email = $request->request->get('email');
        $token = sha1(uniqid($email, true));
         $message = \Swift_Message::newInstance()
                ->setSubject('Bibeauty Password Reset')
                ->setFrom('infofo@bibeauty.com')
                ->setTo($email)
                ->setBody(
                    $this->renderView(
                    'emails/passwordreset.html.twig',
                    array('token' => $token)
                ),
                'text/html'
        );

        $x = $this->get('mailer')->send($message);
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository("AppBundle:User")->findBy(['email'=>$email]);
        $user->setResetToken($token);
        $em->flush();
    }
}
