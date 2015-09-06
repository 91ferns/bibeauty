<?php

// src/AppBundle/Entity/Booking.php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use AppBundle\Entity\User;

/**
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="app_reviews")
 */
class Review {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="float", length=3)
     */
    protected $rating;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $visitedDate;

    /**
     * @ORM\Column(type="text")
     */
    protected $content;

    /**
     * @ORM\Column(type="integer", length=4)
     */
    protected $reports = 0;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $verified = false;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $poster;

    /**
     * @ORM\ManyToOne(targetEntity="Business", inversedBy="reviews", cascade={"persist"})
     * @ORM\JoinColumn(name="business_id", referencedColumnName="id")
     */
    protected $business;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $updated;

    /**
     * @ORM\PrePersist
     */
    public function setAutomaticFields() {
        if (!$this->createdAt) {
            $this->createdAt = new \DateTime();
        }
        $this->updated = new \DateTime();
        $this->setSlug($this->generateSlug());
    }


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
     * Set rating
     *
     * @param float $rating
     * @return Review
     */
    public function setRating($rating)
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * Get rating
     *
     * @return float
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * Set visitedDate
     *
     * @param \DateTime $visitedDate
     * @return Review
     */
    public function setVisitedDate($visitedDate)
    {
        $this->visitedDate = $visitedDate;

        return $this;
    }

    /**
     * Get visitedDate
     *
     * @return \DateTime
     */
    public function getVisitedDate()
    {
        return $this->visitedDate;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Review
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set reports
     *
     * @param integer $reports
     * @return Review
     */
    public function setReports($reports)
    {
        $this->reports = $reports;

        return $this;
    }

    /**
     * Get reports
     *
     * @return integer
     */
    public function getReports()
    {
        return $this->reports;
    }

    /**
     * Set verified
     *
     * @param boolean $verified
     * @return Review
     */
    public function setVerified($verified)
    {
        $this->verified = $verified;

        return $this;
    }

    /**
     * Get verified
     *
     * @return boolean
     */
    public function getVerified()
    {
        return $this->verified;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Review
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     * @return Review
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set poster
     *
     * @param \AppBundle\Entity\User $poster
     * @return Review
     */
    public function setPoster(\AppBundle\Entity\User $poster = null)
    {
        $this->poster = $poster;

        return $this;
    }

    /**
     * Get poster
     *
     * @return \AppBundle\Entity\User
     */
    public function getPoster()
    {
        return $this->poster;
    }

    /**
     * Set business
     *
     * @param \AppBundle\Entity\Business $business
     * @return Review
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

    public static function fromYelp($reviewObject) {

        $review = new Review();

        $review->setRating($reviewObject->rating);
        $review->setContent($reviewObject->excerpt);

        $date = new \DateTime();
        $date->setTimestamp($reviewObject->time_created);
        $review->setCreatedAt($date);

        $theUser = new User();

        $user = $reviewObject->user;
        if ($user) {
            $theUser->setFirstName($user->name);
        }

        $review->setPoster($theUser);

        return $review;

    }
}
