<?php

namespace Acceptance;

use App\Entities\Customer;
use App\Exceptions\CustomerNotFoundException;
use Doctrine\Persistence\ObjectRepository;
use TestCase;

class CustomersControllerTest extends TestCase
{
    private ObjectRepository $repo;

    public function setUp(): void
    {
        parent::setUp();

        $this->repo = $this->em->getRepository(Customer::class);
    }

    public function testCustomersIndexApiReturnsEmptyArrayWhenThereAreNoDataInDatabase(): void
    {
        $this->get('/customers');

        $result = json_decode($this->response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEmpty($result);
    }

    public function testCustomersIndexApiReturnsCorrectData(): void
    {
        [$customer1, $customer2] = $this->saveTwoCustomersToDatabase();

        $this->get('/customers');

        $result = json_decode($this->response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertCount(2, $result);

        [$returnedCustomer1, $returnedCustomer2] = $result;

        $this->assertShortDataTheSame($returnedCustomer1, $customer1);
        $this->assertShortDataTheSame($returnedCustomer2, $customer2);
    }

    public function testCustomersSingleApiReturnsNotFoundErrorWhenThereIsNoSuchCustomerId(): void
    {
        $this->get('/customers/73849123409123801283013');

        self::assertInstanceOf(CustomerNotFoundException::class, $this->response->exception);
    }

    public function testCustomersSingleApiReturnsCorrectData(): void
    {
        [$customer, $customer2] = $this->saveTwoCustomersToDatabase();

        $this->get('/customers/' . $customer->getId());

        $returnedCustomer = json_decode($this->response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $this->assertDataTheSame($returnedCustomer, $customer);
    }

    private function assertDataTheSame(array $arrayData, Customer $customer): void
    {
        $keys = [
            'id',
            'fullName',
            'email',
            'country',
            'username',
            'gender',
            'city',
            'phone',
        ];

        self::assertSame($keys, array_keys($arrayData));

        self::assertEquals($arrayData['id'], $customer->getId());
        self::assertEquals($arrayData['fullName'], trim($customer->getFirstName() . ' ' . $customer->getLastName()));
        self::assertEquals($arrayData['email'], $customer->getEmail());
        self::assertEquals($arrayData['country'], $customer->getCountry());
        self::assertEquals($arrayData['username'], $customer->getUsername());
        self::assertEquals($arrayData['gender'], $customer->getGender());
        self::assertEquals($arrayData['city'], $customer->getCity());
        self::assertEquals($arrayData['phone'], $customer->getPhone());
    }

    private function assertShortDataTheSame(array $arrayData, Customer $customer): void
    {
        $keys = [
            'id',
            'fullName',
            'email',
            'country',
        ];

        self::assertSame($keys, array_keys($arrayData));

        self::assertEquals($arrayData['id'], $customer->getId());
        self::assertEquals($arrayData['fullName'], trim($customer->getFirstName() . ' ' . $customer->getLastName()));
        self::assertEquals($arrayData['email'], $customer->getEmail());
        self::assertEquals($arrayData['country'], $customer->getCountry());
    }

    /**
     * @return Customer[]
     */
    private function saveTwoCustomersToDatabase(): array
    {
        $customer1 = new Customer();
        $customer1->setEmail('email1@test.com');
        $customer1->setFirstName('10');
        $customer1->setLastName('20');
        $customer1->setCity('30');
        $customer1->setCountry('40');
        $customer1->setPhone('50');
        $customer1->setGender('60');
        $customer1->setUsername('70');

        $this->em->persist($customer1);

        $customer2 = new Customer();
        $customer2->setEmail('email2@test.com');
        $customer2->setFirstName('210');
        $customer2->setLastName('220');
        $customer2->setCity('230');
        $customer2->setCountry('240');
        $customer2->setPhone('250');
        $customer2->setGender('260');
        $customer2->setUsername('270');

        $this->em->persist($customer2);

        $this->em->flush();

        return array($customer1, $customer2);
    }
}
