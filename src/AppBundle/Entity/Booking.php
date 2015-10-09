<?php

// src/AppBundle/Entity/Booking.php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="app_bookings")
 */
class Booking {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

}
