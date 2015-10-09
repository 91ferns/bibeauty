<?php

// src/AppBundle/Entity/Address.php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="app_therapists")
 */
class Therapist {

   /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
   protected $id;

   /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(min = 60)
     */
   protected $name;

   /**
     * @ORM\Column(type="string", length=20)
     * @Assert\NotBlank()
     * @Assert\Length(min = 2)
     */
   protected $title;

   /**
     * @ORM\Column(type="string", length=20)
     * @Assert\Length(min = 8)
     */
   protected $phone = '';

   /**
     * @ORM\Column(type="string", length=150)
     * @Assert\NotBlank()
     * @Assert\Email
     */
   protected $email;

   /**
     * @ORM\Column(type="boolean")
     */
   protected $active = true;


   /**
    * @ORM\ManyToOne(targetEntity="Business", inversedBy="therapists")
    * @ORM\JoinColumn(name="business_id", referencedColumnName="id")
    */
   protected $business;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Therapist
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set phone
     *
     * @param string $phone
     * @return Therapist
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Therapist
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set active
     *
     * @param boolean $active
     * @return Therapist
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set business
     *
     * @param \AppBundle\Entity\Business $business
     * @return Therapist
     */
    public function setBusiness(\AppBundle\Entity\Business $business = null)
    {
        $this->business = $business;

        return $this;
    }

    /**
     * Get business
     *
     * @return \AppBundle\Entity\Business
     */
    public function getBusiness()
    {
        return $this->business;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Therapist
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }
}
