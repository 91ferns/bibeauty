<?php

// src/AppBundle/Services/AWS.php
namespace AppBundle\Services;

use Symfony\Component\HttpKernel\Log\LoggerInterface;

use Stevenmaguire\Yelp\Client as YelpClient;

class YelpFactory
{

    public static function createYelpFactory($config, LoggerInterface $logger)
    {
        return new YelpFactory($config, $container);

    }

    protected $config;
    protected $logger;

    public function __construct($config, LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->config = $config;

        $this->client = new YelpClient($this->config);
    }

    public function getBusiness($tag) {
        return $this->client->getBusiness($tag);
    }

}
