<?php

declare(strict_types=1);

namespace App\Services;

use App\Storage\ImportedCustomerStorage;
use Doctrine\ORM\EntityManagerInterface;
use Kerby\EonxTestTask\Contracts\DataProvider\CustomerValidatorInterface;
use Kerby\EonxTestTask\Contracts\DataProvider\DataProviderInterface;
use Kerby\EonxTestTask\Contracts\DataProvider\FetcherFilterInterface;
use Kerby\EonxTestTask\Contracts\DataProvider\FetcherInterface;
use Kerby\EonxTestTask\Contracts\Entity\CustomerDTOFactoryInterface;
use Kerby\EonxTestTask\Contracts\Importer\ImporterInterface;
use Kerby\EonxTestTask\Contracts\Storage\CustomerStorageInterface;
use Kerby\EonxTestTask\DataProvider\GenericCustomerValidator;
use Kerby\EonxTestTask\DataProvider\GenericDataProvider;
use Kerby\EonxTestTask\DataProvider\RandomUser\RandomUserCustomerDTOFactory;
use Kerby\EonxTestTask\Importer\GenericImporter;

/**
 * Class RandomUserImporter
 * @package App\Services
 *
 * We COULD use DI here to put all required dependencies into constructor
 * But we would need too many unnecessary interfaces to my taste
 * So we go with factory methods
 */
class RandomUserImporter implements RandomUserImporterInterface
{
    private EntityManagerInterface $em;
    private bool $flushPerCustomer = false;
    private ImporterInterface $importer;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;

        $fetcher = $this->makeFetcher();
        $customerFactory = $this->makeCustomerAdapterFactory();
        $dataProvider = $this->makeCustomersProvider($fetcher, $customerFactory);
        $repo = $this->makeStorage();

        $this->importer = $this->makeImporter($dataProvider, $repo);
    }

    public function setFlushPerCustomer(bool $flushPerCustomer): self
    {
        $this->flushPerCustomer = $flushPerCustomer;

        return $this;
    }

    public function getFlushPerCustomer(): bool
    {
        return $this->flushPerCustomer;
    }

    public function import(?FetcherFilterInterface $filter = null): void
    {
        $this->importer->import($filter);

        if (!$this->flushPerCustomer) {
            $this->em->flush();
        }
    }

    protected function makeFetcher(): FetcherInterface
    {
        return new HttpClientRandomUserFetcher();
    }

    protected function makeCustomerValidator(): CustomerValidatorInterface
    {
        return new GenericCustomerValidator();
    }

    protected function makeCustomerAdapterFactory(): CustomerDTOFactoryInterface
    {
        return new RandomUserCustomerDTOFactory($this->makeCustomerValidator());
    }

    protected function makeCustomersProvider(FetcherInterface $fetcher, CustomerDTOFactoryInterface $customerFactory): DataProviderInterface
    {
        return new GenericDataProvider($fetcher, $customerFactory);
    }

    protected function makeStorage(): CustomerStorageInterface
    {
        return new ImportedCustomerStorage($this->em);
    }

    protected function makeImporter(DataProviderInterface $dataProvider, CustomerStorageInterface $repo): ImporterInterface
    {
        return new GenericImporter($dataProvider, $repo);
    }
}
