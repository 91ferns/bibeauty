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

        return $business;
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

}
