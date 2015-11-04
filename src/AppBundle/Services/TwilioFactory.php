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

        $date = $booking->getAvailability()->getDate();
        $dateFormatted = $date->format('D, d M Y H:i O');

        // Build message
        $message = "The booking details are:\n";
        $message .= "\n";
        $message .= sprintf("%s\n", $booking->getName());
        $message .= sprintf("%s. %s\n", $treatment->getName(), $treatment->getDuration());
        $message .= sprintf("$%.2f (Discounted from $%.2f)\n", $offer->getCurrentPrice(), $treatment->getOriginalPrice());
        $message .= "\n";
        $message .= "Big Love,";
        $message .= "The BiBeauty Team";

        if ($phone = $business->getMobile()) {
            $one = $this->sendMessage($phone, $message);
        }

        // Build message
        $message = sprintf("Hi, %s:\n", $booking->getName());
        $message .= "\n";
        $message .= sprintf("Your booking request is now with %s for:\n", $business->getName());
        $message .= sprintf("%s. %s\n", $treatment->getName(), $treatment->getDuration());
        $message .= sprintf("$%.2f (Discounted from $%.2f)\n", $offer->getCurrentPrice(), $treatment->getOriginalPrice());
        $message .= sprintf("%s\n", $dateFormatted);
        $message .= sprintf("%s\n", $business->getAddress()->getAddressString());
        $message .= "\n";
        $message .= "Big Love,";
        $message .= "The BiBeauty Team";

        if ($phone = $booking->getPhone()) {
            $two = $this->sendMessage($phone, $message);
        }

        return $one && $two;


    }

    public function bookingConfirmedNotification(Booking $booking) {

        $treatment = $booking->getAvailability()->getTreatment();
        $offer = $booking->getAvailability()->getAvailabilitySet()->getOffer();
        $business = $offer->getBusiness();

        $date = $booking->getAvailability()->getDate();
        $dateFormatted = $date->format('D, d M Y H:i O');

        // Build message
        $message = sprintf("Hi, %s:\n", $booking->getName());
        $message .= "\n";
        $message .= sprintf("Your booking request with %s has been APPROVED. Your treatment details are:\n", $business->getName());
        $message .= sprintf("%s. %s\n", $treatment->getName(), $treatment->getDuration());
        $message .= sprintf("$%.2f (Discounted from $%.2f)\n", $offer->getCurrentPrice(), $treatment->getOriginalPrice());
        $message .= sprintf("%s\n", $dateFormatted);
        $message .= "\n";
        $message .= sprintf("%s\n", $business->getAddress()->getAddressString());
        $message .= sprintf("%s\n", $business->getLandline());
        $message .= sprintf("%s\n", $business->getEmail());
        $message .= "\n";
        $message .= sprintf("If you have questions about the treatment or wish to cancel your appointment, please get in touch with %s directly.\n", $business->getName());
        $message .= "\n";
        $message .= "Enjoy ☺\n";
        $message .= "Big Love,\n";
        $message .= "The BiBeauty Team";

        if ($phone = $booking->getMobile()) {
            $this->sendMessage($phone, $message);
        }

    }

    public function bookingCancelledNotification(Booking $booking) {
        $treatment = $booking->getAvailability()->getTreatment();
        $offer = $booking->getAvailability()->getAvailabilitySet()->getOffer();
        $business = $offer->getBusiness();

        $date = $booking->getAvailability()->getDate();
        $dateFormatted = $date->format('D, d M Y H:i O');

        // Build message
        $message = sprintf("Hi, %s:\n", $booking->getName());
        $message .= "\n";
        $message .= sprintf("Your booking request with %s has been CANCELLED. Your treatment details are:\n", $business->getName());
        $message .= sprintf("%s. %s mins.\n", $treatment->getName(), $treatment->getDuration());
        $message .= sprintf("$%.2f (Discounted from $%.2f)\n", $offer->getCurrentPrice(), $treatment->getOriginalPrice());
        $message .= sprintf("%s\n", $dateFormatted);
        $message .= "\n";
        $message .= "Big Love,\n";
        $message .= "The BiBeauty Team";

        if ($phone = $booking->getMobile()) {
            $this->sendMessage($phone, $message);
        }

    }

    // ...
}
