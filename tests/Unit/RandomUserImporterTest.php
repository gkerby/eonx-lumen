<?php

declare(strict_types=1);

namespace Unit;

use App\Services\HttpClientRandomUserFetcher;
use App\Services\RandomUserImporter;
use App\Services\RandomUserImporterInterface;
use App\Storage\ImportedCustomerStorage;
use Kerby\EonxTestTask\Contracts\Storage\CustomerStorageInterface;
use TestCase;

class RandomUserImporterTest extends TestCase
{
    private static string $randomUserDataString;
    private static array $randomUserData = [];

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        self::$randomUserData = json_decode(self::$randomUserDataString = file_get_contents(__DIR__ . '/../data/randomUserCustomers.json'), true, 512, JSON_THROW_ON_ERROR);
    }

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testMakeSureFlushPerCustomerIsSetAndGottenCorrectly(): void
    {
        $importer=$this->app->make(RandomUserImporterInterface::class);

        /** Assert default value */
        self::assertFalse($importer->getFlushPerCustomer());

        $importer->setFlushPerCustomer(true);
        self::assertTrue($importer->getFlushPerCustomer());
    }

    public function testImporterReceivesLowestLevelDataAndSendsResultObjectsToStorage(): void
    {
        /**
         * Mock importer class and replace factory methods of fetcher and storage to replace with fake ones
         *
         * We should mock exact class which is resolved via DI, so we should make object first and use its class
         */
        $importer=$this->app->make(RandomUserImporterInterface::class);
        $importer = $this->getMockBuilder(get_class($importer))
            ->disableOriginalConstructor()
            ->onlyMethods(['makeFetcher', 'makeStorage'])
            ->getMock();

        /**
         * Fetcher will work with text string read from our prepared file
         */
        $fetcherMock = $this->createPartialMock(HttpClientRandomUserFetcher::class, ['getApiTextResponse']);
        $fetcherMock->method('getApiTextResponse')->willReturn(self::$randomUserDataString);

        $storageMock = $this->createMock(CustomerStorageInterface::class);

        $importer->method('makeStorage')->willReturn($storageMock);
        $importer->method('makeFetcher')->willReturn($fetcherMock);

        $importer->__construct($this->em);

        /**
         * For storage we only need to make sure that we are storing as many customers as we have in API result
         */
        $storageMock
            ->expects(self::exactly(count(self::$randomUserData['results'])))
            ->method('store');

        $importer->import();
    }
}
