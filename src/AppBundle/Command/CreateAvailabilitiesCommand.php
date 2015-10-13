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
        $this->getContainer()->get('profiler')->disable();
        $availabilitySetId = $input->getArgument('id');

        $em = $this->getContainer()->get('doctrine')->getManager();
        $em->getConnection()->getConfiguration()->setSQLLogger(null);

        $logger = $this->getContainer()->get('logger');

        $offerAvailability = $em->getRepository("AppBundle:OfferAvailabilitySet");

        $availabilitySet = $offerAvailability->findOneById($availabilitySetId);

        if (!$availabilitySet) {
            $logger->err('Could not find that availability set. Parammeters: ' . $availabilitySetId);
            $output->writeln('ERR: Could not find that availability set');
        }

        $output->writeln('Executing creation of availability set');
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

        $output->writeln($text);
    }
}
