<?php

// src/AppBundle/Services/AWS.php
namespace AppBundle\Services;

use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Bundle\TwigBundle\TwigEngine;

use AppBundle\Entity\Booking;
use AppBundle\Entity\Business;

class Mailer
{

    protected $logger;
    protected $transport;

    public function __construct(\Swift_Mailer $transport, TwigEngine $templating, LoggerInterface $logger)
    {

        $this->templating = $templating;
        $this->transport = $transport;
        $this->logger = $logger;

    }

    protected function renderView( $path, $params = array() ) {
        return $this->templating->render($path, $params);
    }

    public function sendMessage( \Swift_Message $message ) {
        try {
            $this->transport->send($message);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function bookingNotification(Booking $booking) {

        $treatment = $booking->getAvailability()->getTreatment();
        $offer = $booking->getAvailability()->getAvailabilitySet()->getOffer();
        $business = $offer->getBusiness();
        $availability = $booking->getAvailability();

        $email = \Swift_Message::newInstance()
          ->setFrom('bookings@bibeauty.com')
          ->setSubject('You’ve received a new booking')
          ->setBody(
              $this->renderView(
                'emails/business/booking-new.html.twig',
                array(
                    'booking' => $booking,
                    'availability' => $availability,
                    'business' => $business,
                    'offer' => $offer,
                    'treatment' => $treatment,
                )
              ),
              'text/html'
          );

        if ($address = $business->getEmail()) {
            $email->setTo($address);
            $one = $this->sendMessage($email);
        }

        $email = \Swift_Message::newInstance()
          ->setFrom('bookings@bibeauty.com')
          ->setSubject('Your booking request has been sent')
          ->setBody(
              $this->renderView(
                'emails/business/booking-new.html.twig',
                array(
                    'booking' => $booking,
                    'availability' => $availability,
                    'business' => $business,
                    'offer' => $offer,
                    'treatment' => $treatment,
                )
              ),
              'text/html'
          );

        if ($address = $booking->getEmail()) {
            $email->setTo($address);
            $two = $this->sendMessage($email);
        }

        return true;

    }

    public function bookingConfirmedNotification(Booking $booking) {


        $treatment = $booking->getAvailability()->getTreatment();
        $offer = $booking->getAvailability()->getAvailabilitySet()->getOffer();
        $business = $offer->getBusiness();
        $availability = $booking->getAvailability();

        $email = \Swift_Message::newInstance()
          ->setFrom('bookings@bibeauty.com')
          ->setSubject('You booking request is APPROVED')
          ->setBody(
              $this->renderView(
                'emails/customer/booking-confirmed.html.twig',
                array(
                    'booking' => $booking,
                    'availability' => $availability,
                    'business' => $business,
                    'offer' => $offer,
                    'treatment' => $treatment,
                )
              ),
              'text/html'
          );

        if ($address = $booking->getEmail()) {
            $email->setTo($address);
            $this->sendMessage($email);
        }

    }

    public function businessDeletedNotification(Business $business) {
        $email = \Swift_Message::newInstance()
          ->setFrom('bookings@bibeauty.com')
          ->setTo($business->getEmail())
          ->setSubject('You’ve successfully been removed from BiBeauty')
          ->setBody(
              $this->renderView(
                'emails/business/deleted.html.twig',
                array(
                    'business' => $business,
                )
              ),
              'text/html'
          );

          return $this->sendMessage($email);

    }

    public function bookingCancelledNotification(Booking $booking) {

        $treatment = $booking->getAvailability()->getTreatment();
        $offer = $booking->getAvailability()->getAvailabilitySet()->getOffer();
        $business = $offer->getBusiness();
        $availability = $booking->getAvailability();

        $email = \Swift_Message::newInstance()
          ->setFrom('bookings@bibeauty.com')
          ->setSubject('Booking cancelled')
          ->setBody(
              $this->renderView(
                'emails/business/booking-declined.html.twig',
                array(
                    'booking' => $booking,
                    'availability' => $availability,
                    'business' => $business,
                    'offer' => $offer,
                    'treatment' => $treatment,
                )
              ),
              'text/html'
          );

        if ($address = $business->getEmail()) {
            $email->setTo($address);
            $one = $this->sendMessage($email);
        }


        $email = \Swift_Message::newInstance()
          ->setFrom('bookings@bibeauty.com')
          ->setSubject('Your booking request is declined')
          ->setBody(
              $this->renderView(
                'emails/customer/booking-declined.html.twig',
                array(
                    'booking' => $booking,
                    'availability' => $availability,
                    'business' => $business,
                    'offer' => $offer,
                    'treatment' => $treatment,
                    'link' => 'https://www.bibeauty.com/businesses/search'
                )
              ),
              'text/html'
          );

        if ($address = $booking->getEmail()) {
            $email->setTo($address);
            $this->sendMessage($email);
        }

    }

    // ...
}
