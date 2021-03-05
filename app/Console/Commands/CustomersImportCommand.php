<?php

namespace App\Console\Commands;

use App\Services\RandomUserImporter;
use App\Services\RandomUserImporterInterface;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Illuminate\Console\Command;
use InvalidArgumentException;
use Kerby\EonxTestTask\Contracts\Importer\ImporterInterface;
use Kerby\EonxTestTask\DataProvider\GenericFetcherFilter;

class CustomersImportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'customers:import {--provider=randomUser} {--resultsNumber=2} {--nationality=au}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports users from given data provider';

    private ImporterInterface $importer;

    private $importerClasses = [
        'randomUser' => RandomUserImporterInterface::class
    ];

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $this->prepare();

            $filter = (new GenericFetcherFilter())
                ->setResultsNumber($this->option('resultsNumber'))
                ->setNationality($this->option('nationality'));

            $this->info("Starting import...");
            $this->importer->import($filter);
            $this->info("Import is finished successfully");

            return 0;
        } catch (Exception $exception) {
            $this->error(get_class($exception) . ': ' . $exception->getMessage());

            return 255;
        }
    }

    private function prepare(): void
    {
        $providerId = $this->option('provider');

        if (!array_key_exists($providerId, $this->importerClasses)) {
            throw new InvalidArgumentException("Provider $providerId not found");
        }

        $this->importer = app($this->importerClasses[$providerId]);
    }
}
