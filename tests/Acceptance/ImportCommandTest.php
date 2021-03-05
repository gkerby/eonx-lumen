<?php

namespace Acceptance;

use App\Services\HttpClientRandomUserFetcher;
use App\Services\RandomUserImporterInterface;
use TestCase;

/**
 * Class ImportCommandTest
 * @package Acceptance
 *
 * It is rather "shortened version" of command test because it seems that Lumen can't do "normal" command testing as Laravel can
 */
class ImportCommandTest extends TestCase
{
    /**
     * @var mixed
     */
    private static $randomUserData;
    /**
     * @var false|string
     */
    private static $randomUserDataString;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        self::$randomUserData = json_decode(self::$randomUserDataString = file_get_contents(__DIR__ . '/../data/randomUserCustomers.json'), true, 512, JSON_THROW_ON_ERROR);
    }

    public function testItImportsCustomersFromRandomUserDataSource(): void
    {
        $this->replaceWithFakeRandomUserImporter();

        self::assertEquals(0, $this->artisan('customers:import --provider=randomUser'));

        self::assertEquals(count(self::$randomUserData['results']), $this->getCustomersCount());
    }

    public function testItFailsWhenWrongProviderOptionIsSupplied(): void
    {
        self::assertEquals(255, $this->artisan('customers:import --provider=123123123124234234234234'));
    }

    public function getProviders(): array
    {
        return [
            'randomUser' => ['randomUser']
        ];
    }

    private function replaceWithFakeRandomUserImporter(): void
    {
        /**
         * Mock importer class and replace factory methods of fetcher to replace with fake ones
         *
         * We should mock exact class which is resolved via DI, so we make object first and use its class
         */
        $importer = $this->app->make(RandomUserImporterInterface::class);
        $importer = $this->getMockBuilder(get_class($importer))
            ->disableOriginalConstructor()
            ->onlyMethods(['makeFetcher'])
            ->getMock();

        /**
         * Fetcher will work with text string read from our prepared file
         */
        $fetcherMock = $this->createPartialMock(HttpClientRandomUserFetcher::class, ['getApiTextResponse']);
        $fetcherMock->method('getApiTextResponse')->willReturn(self::$randomUserDataString);

        $importer->method('makeFetcher')->willReturn($fetcherMock);

        $importer->__construct($this->em);

        $this->app->bind(RandomUserImporterInterface::class, fn() => $importer);
    }
}
