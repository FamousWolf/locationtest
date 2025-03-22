<?php

namespace App\Command;

use App\Exception\FileException;
use App\Service\LocationService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:import-locations',
    description: 'Import a CSV file with locations',
)]
class ImportLocationsCommand extends Command
{
    public function __construct(
        protected LocationService $locationService
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('file', InputArgument::REQUIRED, 'CSV file containing locations');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $file = $input->getArgument('file');

        if (!is_file($file)) {
            $io->error('File `' . $file . '` does not exist');
            return Command::FAILURE;
        }

        try {
            $locations = $this->readCsvFile($file);
        } catch (FileException $exception) {
            $io->error('Error while reading CSV file: ' . $exception->getMessage());
            return Command::FAILURE;
        }

        foreach ($locations as $location) {
            $this->locationService->addLocation($location['name'], $location['latitude'], $location['longitude']);
        }

        $io->success('Imported ' . count($locations) . ' locations');

        return Command::SUCCESS;
    }

    /**
     * Read CSV file
     *
     * @param string $file
     * @return array
     * @throws FileException
     */
    protected function readCsvFile(string $file): array
    {
        $rows = [];

        if (($handle = fopen($file, 'r')) !== false) {
            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                if (count($data) !== 3) {
                    continue;
                }

                // @todo Add validation

                $rows[] = [
                    'name' => $data[0],
                    'latitude' => $data[1],
                    'longitude' => $data[2],
                ];
            }
            fclose($handle);
        } else {
            throw new FileException('Unable to open file', 1742650732);
        }

        return $rows;
    }
}
