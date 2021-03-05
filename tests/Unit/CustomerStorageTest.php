<?php

declare(strict_types=1);

namespace Unit;

use App\Entities\Customer;
use App\Storage\ImportedCustomerStorage;
use Kerby\EonxTestTask\Contracts\Entity\CustomerDTOInterface;
use Kerby\EonxTestTask\Entity\CustomerDTO;
use TestCase;

class CustomerStorageTest extends TestCase
{
    /** @var CustomerDTOInterface[] */
    private static array $testData = [];

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        $testData = json_decode(file_get_contents(__DIR__ . '/../data/adaptedCustomers.json'), true, 512, JSON_THROW_ON_ERROR);

        foreach ($testData as $data) {
            $customer = new CustomerDTO();

            $customer
                ->setFirstName($data['firstName'] ?? null)
                ->setLastName($data['lastName'] ?? null)
                ->setEmail($data['email'] ?? null)
                ->setCountry($data['country'] ?? null)
                ->setCity($data['city'] ?? null)
                ->setGender($data['gender'] ?? null)
                ->setUsername($data['username'] ?? null)
                ->setPhone($data['phone'] ?? null);

            self::$testData[] = $customer;
        }
    }

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testMakeSureFlushPerCustomerIsSetAndGottenCorrectly(): void
    {
        $storage = new ImportedCustomerStorage($this->em);

        /** Assert default value */
        self::assertFalse($storage->getFlushPerCustomer());

        $storage->setFlushPerCustomer(true);
        self::assertTrue($storage->getFlushPerCustomer());
    }

    public function testCustomerIsStored(): void
    {
        $storage = new ImportedCustomerStorage($this->em);

        $testCustomer = reset(self::$testData);

        $storage->store($testCustomer);
        $this->em->flush();

        self::assertEquals(1, $this->getCustomersCount());
    }

    public function testCustomerIsNotStoredUntilEmIsFlushedManuallyWhenStorageIsNotInstructedToFlushOnEveryCustomer(): void
    {
        $this->storeSingleCustomer(false);

        /**
         * It should be zero since we didn't tell storage to flush immediately and we haven't flushed ourselves
         */
        self::assertEquals(0, $this->getCustomersCount());

        $this->em->flush();

        /**
         * Now it should be 1 since we flushed manually
         */
        self::assertEquals(1, $this->getCustomersCount());
    }

    public function testCustomerIsFlushedImmediatelyWhenStorageIsInstructedTo(): void
    {
        $this->storeSingleCustomer(true);

        /**
         * It is zero since we didn't tell storage to flush immediately and we haven't flushed ourselves
         */
        self::assertEquals(1, $this->getCustomersCount());
    }

    public function testCustomerIsNotDuplicatedButUpdatedWhenItHasSameEmailAsAlreadyStoredEvenWhenNotFlushedOnEveryCustomer(): void
    {
        $this->helperStoreCustomerThenAddCustomerWithSameEmail(false);
    }

    public function testCustomerIsNotDuplicatedButUpdatedWhenItHasSameEmailAsAlreadyStoredEvenWhenItIsFlushedOnEveryCustomer(): void
    {
        $this->helperStoreCustomerThenAddCustomerWithSameEmail(true);
    }

    /**
     * @param bool $flushPerCustomer
     */
    private function storeSingleCustomer(bool $flushPerCustomer): void
    {
        $storage = new ImportedCustomerStorage($this->em);
        $storage->setFlushPerCustomer($flushPerCustomer);

        $testCustomer = reset(self::$testData);

        $storage->store($testCustomer);
    }

    /**
     * @param bool $flushPerCustomer
     */
    private function helperStoreCustomerThenAddCustomerWithSameEmail(bool $flushPerCustomer): void
    {
        $storage = new ImportedCustomerStorage($this->em);
        $storage->setFlushPerCustomer($flushPerCustomer);

        $customer1 = reset(self::$testData);
        $customer2 = next(self::$testData);

        $storage->store($customer1);
        $storage->store($customer2);

        $customer3 = new CustomerDTO();
        $customer3->setEmail($customer1->getEmail());
        $customer3->setFirstName('10');
        $customer3->setLastName('20');
        $customer3->setCity('30');
        $customer3->setCountry('40');
        $customer3->setPhone('50');
        $customer3->setGender('60');
        $customer3->setUsername('70');

        $storage->store($customer3);

        $this->em->flush();

        self::assertEquals(2, $this->getCustomersCount());

        /** @var Customer $newCustomer */
        $newCustomer = $this->customersRepo->findOneBy(['email' => $customer3->getEmail()]);

        self::assertEquals($customer3->getEmail(), $newCustomer->getEmail());
        self::assertEquals($customer3->getFirstName(), $newCustomer->getFirstName());
        self::assertEquals($customer3->getLastName(), $newCustomer->getLastName());
        self::assertEquals($customer3->getCity(), $newCustomer->getCity());
        self::assertEquals($customer3->getCountry(), $newCustomer->getCountry());
        self::assertEquals($customer3->getPhone(), $newCustomer->getPhone());
        self::assertEquals($customer3->getGender(), $newCustomer->getGender());
        self::assertEquals($customer3->getUsername(), $newCustomer->getUsername());
    }
}
