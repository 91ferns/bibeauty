<?php

// src/AppBundle/Entity/TreatmentAvailabilitySet.php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="app_treatment_availability_sets")
 */
class TreatmentAvailabilitySet {

   /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
   protected $id;

   /**
     * @ORM\Column(type="string", length=255)
     */
   protected $street;

   /**
     * @ORM\Column(type="string", length=255)
     */
   protected $line2 = '';

   /**
     * @ORM\Column(type="string", length=150)
     */
   protected $city;

   /**
     * @ORM\Column(type="string", length=4)
     */
   protected $state;

   /**
     * @ORM\Column(type="string", length=12)
     */
   protected $zip;

   /**
     * @ORM\Column(type="string", length=14)
     */
   protected $phone;

   /**
     * @ORM\Column(type="string", length=50)
     */
   protected $country = 'US';

   /**
     * @ORM\Column(type="float", length=10, nullable=true)
     */
   protected $longitude = 0.0;

   /**
     * @ORM\Column(type="float", length=10, nullable=true)
     */
   protected $latitude = 0.0;

   /**
     * @ORM\Column(type="boolean")
     */
   protected $active = false;

}
