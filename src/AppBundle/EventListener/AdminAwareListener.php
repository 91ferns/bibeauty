<?php

// src/AppBundle/EventListener/AdminAwareListener.php
namespace AppBundle\EventListener;

use AppBundle\Controller\AdminAwareController;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;


class AdminAwareListener
{

    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    protected function loadBusinessesForUser($user) {
        $em = $this->em;
        $repository = $em->getRepository("AppBundle:Business");

        if ($user->getSuperAdmin() === true) {
            $businesses = $repository->findAll();

            $user->clearBusinesses();

            foreach($businesses as $business) {
                $user->addBusiness($business);
            }

        } else {
            $user->getBusinesses();
        }

        return $user;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();

        /*
         * $controller passed can be either a class or a Closure.
         * This is not usual in Symfony but it may happen.
         * If it is a class, it comes in array format
         */
        if (!is_array($controller)) {
            return;
        }

        if ($controller[0] instanceof AdminAwareController) {
            $user = $controller[0]->getUser();

            $user = $this->loadBusinessesForUser($user);
        }
    }
}
