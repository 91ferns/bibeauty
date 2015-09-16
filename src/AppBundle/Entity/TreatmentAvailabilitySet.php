<?php

// src/AppBundle/Entity/TreatmentAvailabilitySet.php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

class TreatmentAvailabilitySet {
  /**
   * @ORM\Entity
   * @ORM\Table(name="app_availabilityset")
   */

   /**
    * @ORM\Column(type="integer")
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="AUTO")
    */
   protected $id;

   /**
    * @ORM\Column(type="date")
    * @Assert\NotBlank()
    */
   protected $date;

   /**
    * @ORM\Column(type="time", length=255)
    * @Assert\NotBlank()
    */
   protected $time;

   /**
    * @ORM\Column(type="integer", options={"default": 0})
    */
   protected $isOpen;


}
