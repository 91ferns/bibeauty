<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Component\HttpFoundation\Request;

use AppBundle\Entity\Treatment;
use AppBundle\Entity\Review;


class ProductsController extends ApplicationController
{
    /**
     * @Route("/products", name="products_path")
     */
    public function indexAction(Request $request)
    {

        $queryTerm = $request->query->get('term', '');
        $brandTerm = $request->query->get('brands', array());

        $email = $request->query->get('email', false);
        $workIn = $request->query->get('occupation', false);

        if ($email && $workIn) {
            return $this->sendBrandEmail($email, $workIn, $brandTerm);
        }

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository("AppBundle:Product");

        $prods = $em->getRepository("AppBundle:ProductBrand");

        // replace this example code with whatever you need
        return $this->render('products/index.html.twig', [
            'products' => $repository->bySearchTerm( $queryTerm, $brandTerm ),
            'brands' => $prods->findAll(),
            'term' => $queryTerm,
            'bterms' => $brandTerm
        ]);

    }

    protected function sendBrandEmail($email, $workIn, $brandTerm) {
        try {

            $em = $this->getDoctrine()->getManager();

            $repository = $em->getRepository("AppBundle:ProductBrand");
            $brands = $repository->findById($brandTerm);

            $message = \Swift_Message::newInstance()
                    ->setSubject('Bibeauty Brand Request')
                    ->setFrom('info@bibeauty.com')
                    ->setTo('richard@bibeauty.com')
                    ->setBody(
                        $this->renderView(
                            'emails/brandemail.html.twig',
                            array(
                                'email' => $email,
                                'workIn' => $workIn,
                                'brands' => $brands
                            )
                    ),
                    'text/html'
            );

            $x = $this->get('mailer')->send($message);

        } catch (\Exception $e) {
            return $this->redirectToRoute('products_path', array(
                'term' => $this->getRequest()->query->get('term', ''),
                'error' => $e->getMessage()
            ));
        }

        return $this->redirectToRoute('products_path', array(
            'term' => $this->getRequest()->query->get('term', ''),
            'error' => 'Successfully submitted your request'
        ));
    }


}
