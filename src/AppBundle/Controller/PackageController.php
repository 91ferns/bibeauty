<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Component\HttpFoundation\Request;

class PackageController extends ApplicationController
{
    /**
     * @Route("/package", name="package_path")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        return $this->render('package/index.html.twig', array());

    }

    /**
     * @Route("/package/therapist", name="package_therapist_path")
     * @Method("GET")
     */
    public function therapistAction(Request $request)
    {
        return $this->render('package/therapist.html.twig', array());

    }

    /**
     * @Route("/package/therapist/summary", name="package_therapist_summary_path")
     * @Method("GET")
     */
    public function therapistSummaryAction(Request $request)
    {
        return $this->render('package/therapist-summary.html.twig', array());

    }

    /**
     * @Route("/voucher", name="package_voucher_path")
     * @Method("GET")
     */
    public function voucherAction(Request $request)
    {
        return $this->render('package/voucher.html.twig', array());

    }







}
