<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ApplicationController extends Controller
{

    protected function businessBySlugAndId($slug, $id) {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository("AppBundle:Business");

        $query = $repository->createQueryBuilder('b')
            ->where('b.id = :id')
            ->andWhere('b.slug = :slug')
            ->setParameter('slug', $slug)
            ->setParameter('id', $id)
            ->getQuery();

        $business = $query->setMaxResults(1)->getOneOrNullResult();

        if (!$business) {
            throw $this->createNotFoundException('We couldn\'t find that business');
        }

        $seo = $this->get('cmf_seo.presentation');
        $seo->updateSeoPage($business);

        return $business;
    }

    protected function getRecentDeals() {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository("AppBundle:Offer");

        return $repository->recentDeals();
    }

    protected function categoryInBusiness($slug, $business) {

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository("AppBundle:ServiceCategory");

        $query = $repository->createQueryBuilder('c')
            ->where('c.business = :business')
            ->andWhere('c.slug = :slug')
            ->setParameter('slug', $slug)
            ->setParameter('business', $business)
            ->getQuery();

        $category = $query->setMaxResults(1)->getOneOrNullResult();

        if (!$category) {
            throw $this->createNotFoundException('We couldn\'t find that category');
        }

        return $category;

    }

    protected function serviceBySlugAndId($slug, $id) {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository("AppBundle:Service");

        $query = $repository->createQueryBuilder('b')
            ->where('b.id = :id')
            ->andWhere('b.slug = :slug')
            ->setParameter('slug', $slug)
            ->setParameter('id', $id)
            ->getQuery();

        $service = $query->setMaxResults(1)->getOneOrNullResult();

        if (!$service) {
            throw $this->createNotFoundException('We couldn\'t find that business');
        }

        return $service;
    }

    protected function setTitle($title) {
        $seo = $this->get('sonata.seo.page');
        $seo->setTitle($title);
    }

    protected function getRepo($name){
      $em = $this->getDoctrine()->getManager();
      return $em->getRepository("AppBundle:{$name}");
    }

    protected function getSearchForm($request) {

        $allowedDays = array(
            'all' => 'all',
            'today' => 'today',
            'tomorrow' => 'tomorrow',
        );

        $allowedTimes = array(
            'all' => 'all',
            'morning' => 'morning',
            'afternoon' => 'afternoon',
            'evening' => 'evening',
        );

        $date = $request->query->get('date', $allowedDays['all']);
        $time = $request->query->get('time', $allowedTimes['all']);
        $treatment = $request->query->get('treatment', null);
        $min = intval($request->query->get('min', 0));
        $max = intval($request->query->get('max', 500));

        if ($min > 500) {
            $min = 500;
        }

        if ($max < 1) {
            $max = 1;
        }

        if (!in_array($date, $allowedDays)) {
            $date = $allowedDays[0];
        }

        if (!in_array($time, $allowedTimes)) {
            $time = $allowedTimes[0];
        }

        if ($treatment !== null && !is_integer($treatment)) {
            $treatment = intval($treatment);
            if (!$treatment) {
                $treatment = null;
            }
        }

        $defaultData = array(
            'day' => $date, //new \DateTime()
            'time' => $time, //new \DateTime()
            'location' => $request->query->get('location', 'Los Angeles'),
            'treatment' => $treatment,
            'min' => $min,
            'max' => $max
        );

        $form = $this->get('form.factory')->createNamedBuilder('', 'form', $defaultData)
          ->setMethod('GET')
          ->add('day', 'choice', array(
              'choices' => $allowedDays
          ))
          ->add('time', 'choice', array(
              'choices' => $allowedTimes
          ))
          ->add('location', 'text')
          ->add('treatment', 'integer')
          ->add('min', 'integer')
          ->add('max', 'integer')
          ->getForm();

        // $form->handleRequest($request); // Handled above
        return $form;

    }

}
