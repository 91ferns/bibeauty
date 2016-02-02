<?php
// src/AppBundle/Command/CreateAvailabilitiesCommand.php
namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ScrapeProductsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('bibeauty:scrape:prices')
            ->setDescription('Scrape prices for products in the database')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        if ($this->getContainer()->has('profiler')) {
            $this->getContainer()->get('profiler')->disable();
        }

        $logger = $this->getContainer()->get('logger');
        $em = $this->getContainer()->get('doctrine')->getManager();
        $em->getConnection()->getConfiguration()->setSQLLogger(null);

        try {
            // $this->logger->addInfo('Start executing');
            echo 'I can show you incredible things';
        } catch (\Exception $e) {

        }

        return 0;

    }
}
