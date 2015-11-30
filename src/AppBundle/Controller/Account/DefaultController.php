<?php

namespace AppBundle\Controller\Account;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use AppBundle\Controller\AdminAwareController;


class DefaultController extends Controller implements AdminAwareController
{
    /**
     * @Route("/account", name="admin_path")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        return $this->redirectToRoute('admin_businesses_path');
    }

}
