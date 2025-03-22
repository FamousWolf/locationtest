<?php

namespace App\Command;

use App\Service\LocationService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:add-location',
    description: 'Add a location',
)]
class AddLocationCommand extends Command
{
    public function __construct(
        protected LocationService $locationService
    )
    {
        parent::__construct();
    }

    /**
     * Configure
     *
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->addArgument('name', InputArgument::REQUIRED, 'Name of the location')
            ->addArgument('longitude', InputArgument::REQUIRED, 'Longitude of the location')
            ->addArgument('latitude', InputArgument::REQUIRED, 'Latitude of the location');
    }

    /**
     * Execute
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $location = $this->locationService->addLocation($input->getArgument('name'), $input->getArgument('longitude'), $input->getArgument('latitude'));

        $io->success('Location `' . $input->getArgument('name') . '` as been added with id ' . $location->getId());

        return Command::SUCCESS;
    }
}
