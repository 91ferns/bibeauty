<?php

// src/AppBundle/Services/AWS.php
namespace AppBundle\Services;

use Symfony\Component\HttpKernel\Log\LoggerInterface;
use AppBundle\Entity\Booking;
use Symfony\Bundle\TwigBundle\TwigEngine;

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

        $one = false;
        $two = false;

        $treatment = $booking->getAvailability()->getTreatment();
        $offer = $booking->getAvailability()->getAvailabilitySet()->getOffer();
        $business = $offer->getBusiness();
        $availability = $booking->getAvailability();

        // Build message
        $message = "New booking request;\n";
        $message .= sprintf("%s\n", $availability->getDayText());
        $message .= sprintf("%s\n", $availability->getTimeText());
        $message .= sprintf("%s\n", $treatment->getName());
        $message .= sprintf("Special Price: $%.2f\n", $offer->getCurrentPrice());
        $message .= sprintf("You must ACCEPT or DECLINE the booking to confirm. Go to %s\n", 'https://www.bibeauty.com/account');
        $message .= sprintf("%s\n", $booking->getName());
        $message .= $booking->getPhone();
        $message .= "\n";
        $message .= "BiBeauty";

        if ($phone = $business->getMobile()) {
            $one = $this->sendMessage($phone, $message);
        }

        // Build message
        $message = "Booking request sent. Await confirmation SMS if Accepted or Declined.\n";
        // Hi, %s:\n", $booking->getName());
        $message .= sprintf("%s\n", $availability->getDayText());
        $message .= sprintf("%s\n", $availability->getTimeText());
        $message .= sprintf("%s\n", $treatment->getName());
        $message .= sprintf("Special Price: $%.2f\n", $offer->getCurrentPrice());
        $message .= sprintf("%s\n", $business->getName());
        $message .= sprintf("Location: %s\n", $business->getAddress()->getGoogleMapsUrl());
        $message .= sprintf("%s\n", $business->getLandline());
        $message .= "BiBeauty";

        if ($phone = $booking->getPhone()) {
            $two = $this->sendMessage($phone, $message);
        }

        return $one && $two;


    }

    public function bookingConfirmedNotification(Booking $booking) {


        $treatment = $booking->getAvailability()->getTreatment();
        $offer = $booking->getAvailability()->getAvailabilitySet()->getOffer();
        $business = $offer->getBusiness();
        $availability = $booking->getAvailability();

        // Build message
        $message = "Booking CONFIRMED.\n";
        $message .= sprintf("%s\n", $availability->getDayText());
        $message .= sprintf("%s\n", $availability->getTimeText());
        $message .= sprintf("%s\n", $treatment->getName());
        $message .= sprintf("Special: $%.2f\n", $offer->getCurrentPrice());
        $message .= sprintf("%s\n", $booking->getName());
        $message .= sprintf("%s\n", $booking->getPhone());
        $message .= "\n";
        $message .= "BiBeauty";

        if ($phone = $business->getMobile()) {
            $one = $this->sendMessage($phone, $message);
        }

        // Build message
        $message = "Your Booking is CONFIRMED.\n";
        // Hi, %s:\n", $booking->getName());
        $message .= sprintf("%s\n", $availability->getDayText());
        $message .= sprintf("%s\n", $availability->getTimeText());
        $message .= sprintf("%s\n", $treatment->getName());
        $message .= sprintf("Special Price: $%.2f\n", $offer->getCurrentPrice());
        $message .= sprintf("%s\n", $business->getName());
        $message .= sprintf("Salon on BiBeauty: http://www.bibeauty.com/businesses/%s/%s\n", $business->getId(), $business->getSlug());
        $message .= sprintf("%s\n", $business->getLandline());
        $message .= "BiBeauty";

        if ($phone = $booking->getPhone()) {
            $this->sendMessage($phone, $message);
        }

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
