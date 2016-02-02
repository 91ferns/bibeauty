<?php
namespace AppBundle\Consumer;

// src/AppBundle/Consumer/CreateAvailabilities.php

use Symfony\Component\DependencyInjection\ContainerAware;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;

class ScrapePrices implements ConsumerInterface
{
    private $container;
    private $em;

    public function __construct($container)
    {
        $this->container = $container;
        $this->em = $this->container->get('doctrine')->getManager();
    }

    protected function getContainer() {
        return $this->container;
    }

    public function execute(AMQPMessage $msg)
    {
        // @todo
        

        return true;
    }
}
