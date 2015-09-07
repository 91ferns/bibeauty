<?php

// src/AppBundle/Services/AWS.php
namespace AppBundle\Services;

use Symfony\Component\HttpKernel\Log\LoggerInterface;

class BraintreeFactory
{

    public static function createBraintreeFactory($config, LoggerInterface $logger)
    {
        return new BraintreeFactory($config, $container);

    }

    protected $config;
    protected $logger;

    public function __construct($config, LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->config = $config;
    }

}
