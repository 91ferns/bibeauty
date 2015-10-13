<?php
// src/AppBundle/Command/CreateAvailabilitiesCommand.php
namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateAvailabilitiesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('bibeauty:generate:availabilities')
            ->setDescription('Generate availabilities for an availability set')
            ->addArgument(
                'id',
                InputArgument::REQUIRED,
                'Availability set ID'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $availabilitySetId = $input->getArgument('id');

        $em = $this->getContainer()->get('doctrine')->getManager();
        $offerAvailability = $em->getRepository("AppBundle:OfferAvailabilitySet");

        $availabilitySet = $offerAvailability->findOneById($availabilitySetId);

        if (!$availabilitySet) {
            $output->writeln('ERR: Could not find that availability set');
        }

        // Start it going
        $business = $availabilitySet->getTreatment()->getBusiness();

        // Offer is made. We need to make its availability now
        $matchingDates = $availabilitySet->datesThatMatchRecurrence();
        $availabilitySets = $availabilitySet->datesToAvailabilities($matchingDates, $business);

        $batchSize = 20;

        foreach ($availabilitySets as $i => $availabilitySet) {
            $em->persist($availabilitySet);
            if (($i % $batchSize) === 0) {
                $em->flush();
            }
        }

        $availabilitySet->setProcessed(true);

        $em->flush(); //Persist objects that did not make up an entire batch
        $em->clear();
        // End it

        $output->writeln('Created ' . count($matchingDates) . ' availabilities.');
    }
}
