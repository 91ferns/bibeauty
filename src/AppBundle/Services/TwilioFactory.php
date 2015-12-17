<?php

// src/AppBundle/Services/AWS.php
namespace AppBundle\Services;


use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Aws\S3\S3Client;


use AppBundle\Entity\Booking;

use \Services_Twilio;

class TwilioFactory
{

    public static function createAWSFactory($config, LoggerInterface $logger)
    {
        return new TwilioFactory($config, $logger);

    }

    protected $config;
    protected $logger;
    protected $client;

    public function __construct($config, LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->config = (object) $config;

        $this->client = new Services_Twilio($config['account_sid'], $config['auth_token']);
    }

    public function sendMessage( $phone, $message ) {
        try {
            $message = $this->client->account->messages->sendMessage(
              $this->config->phone_number, // From a valid Twilio number
              $phone, // Text this number
              $message
            );
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

        // Build message
        $message = "New booking request;\n";
        $message .= sprintf("%s\n", $availability->getDayText());
        $message .= sprintf("%s\n", $availability->getTimeText());
        $message .= sprintf("%s\n", $treatment->getName());
        $message .= sprintf("Special Price: $%.2f\n", $offer->getCurrentPrice());
        $message .= sprintf("You must ACCEPT or DECLINE the booking to confirm. Go to %s\n");
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
        $message = "Booking request sent. Await confirmation SMS if Accepted or Declined.\n";
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

        // Build message
        $message = "Your Booking has unfortunately been DECLINED.\n";
        $message .= sprintf("%s\n", $availability->getDayText());
        $message .= sprintf("%s\n", $availability->getTimeText());
        $message .= sprintf("%s.\n", $treatment->getName());
        $message .= sprintf("Special: $%.2f\n", $offer->getCurrentPrice());
        $message .= sprintf("%s\n", $booking->getName());
        $message .= sprintf("%s\n", $booking->getPhone());
        $message .= "\n";
        $message .= "BiBeauty";

        if ($phone = $business->getMobile()) {
            $one = $this->sendMessage($phone, $message);
        }

        // Build message
        $message = "Your Booking has unfortunately been DECLINED.\n";
        // Hi, %s:\n", $booking->getName());
        $message .= sprintf("%s\n", $availability->getDayText());
        $message .= sprintf("%s\n", $availability->getTimeText());
        $message .= sprintf("%s\n", $treatment->getName());
        $message .= sprintf("Special Price: $%.2f\n", $offer->getCurrentPrice());
        $message .= sprintf("%s\n", $business->getName());
        $message .= sprintf("Search again: %s\n", 'https://www.bibeauty.com/businesses/search');
        $message .= sprintf("%s\n", $business->getLandline());
        $message .= "BiBeauty";

        if ($phone = $booking->getPhone()) {
            $this->sendMessage($phone, $message);
        }

    }

    // ...
}
