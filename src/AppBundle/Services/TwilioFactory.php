<?php

// src/AppBundle/Services/AWS.php
namespace AppBundle\Services;


use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Aws\S3\S3Client;

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
        $message = $this->client->account->messages->sendMessage(
          $this->config->phone_number, // From a valid Twilio number
          $phone, // Text this number
          $message
        );
    }

    // ...
}
