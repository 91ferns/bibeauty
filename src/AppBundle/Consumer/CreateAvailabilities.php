<?php
namespace AppBundle\Consumer;

// src/AppBundle/Consumer/CreateAvailabilities.php

use Symfony\Component\DependencyInjection\ContainerAware;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;

class CreateAvailabilities implements ConsumerInterface
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

        $logger = $this->getContainer()->get('logger');

        try {
            // $this->logger->addInfo('Start executing');
            $ID = $msg->body;

            echo 'WORKER: Creating availabilities for ' . $ID;

            //Process picture upload.
            //$msg will be an instance of `PhpAmqpLib\Message\AMQPMessage` with the $msg->body being the data sent over RabbitMQ.

            if ($this->getContainer()->has('profiler')) {
                $this->getContainer()->get('profiler')->disable();
            }

            $availabilitySetId = $ID;

            $em = $this->em;
            $em->getConnection()->getConfiguration()->setSQLLogger(null);

            $offerAvailability = $em->getRepository("AppBundle:OfferAvailabilitySet");

            $availabilitySet = $offerAvailability->findOneById($availabilitySetId);

            if (!$availabilitySet) {
                $msg = 'Could not find that availability set. Parameters: ' . $availabilitySetId;
                throw new \Exception($msg, 0);
            }

            $logger->info('Executing creation of availability set ' . $availabilitySetId);

            // Start it going
            $business = $availabilitySet->getTreatment()->getBusiness();

            // Delete previous availabilities to make this rerunable
            $queryBuilder = $em
                ->createQueryBuilder()
                ->delete('AppBundle:Availability', 'a')
                ->innerJoin('a.availabilitySet', 's')
                ->where('a.availabilitySet = :availabilitySet')
                ->setParameter(':availabilitySet', $availabilitySet);

            $queryBuilder->getQuery()->execute();

            // Offer is made. We need to make its availability now
            $matchingDates = $availabilitySet->datesThatMatchRecurrence();
            // $availabilitySets = $availabilitySet->datesToAvailabilities($matchingDates, $business);
            // $treatment = $availabilitySet->getTreatment();

            $batchSize = 20;

            foreach($matchingDates as $i => $date) {
                $x = new \AppBundle\Entity\Availability();
                $x->setDate($date);
                $x->setAvailabilitySet($availabilitySet);
                $x->setTreatment($availabilitySet->getTreatment());
                $x->setBusiness($business);
                $em->persist($x);

                if ($i !== 0 && ($i % $batchSize) === 0) {
                    $em->flush();
                    $em->clear(); // Detaches objects

                    $availabilitySet = $offerAvailability->findOneById($availabilitySetId);
                    $business = $availabilitySet->getTreatment()->getBusiness();

                }
            }

            $availabilitySet->setProcessed(true);

            $em->flush(); //Persist objects that did not make up an entire batch
            $em->clear();
            // End it

            $text = 'Created ' . count($matchingDates) . ' availabilities.';
            $logger->info($text);

            //$this->container->get('api_mailer')->sendPrivatePathInvites($pathInvite);
            /* end your code */
            // $this->logger->addInfo('End executing');
        } catch(\Exception $e) {
            echo 'Failed: ';
            $logger->addError($e->getMessage());

            return true;
        }

        return true;
    }
}
