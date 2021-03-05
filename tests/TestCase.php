<?php

use App\Entities\Customer;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\Persistence\ObjectRepository;
use Laravel\Lumen\Application;
use Laravel\Lumen\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected EntityManagerInterface $em;
    protected ObjectRepository $customersRepo;

    /**
     * Creates the application.
     *
     * @return Application
     */
    public function createApplication()
    {
        return require __DIR__ . '/../bootstrap/app.php';
    }

    public function setUp(): void
    {
        parent::setUp();

        $this->em = app(EntityManagerInterface::class);

        /**
         * Recreate schema
         */
        $schema = new SchemaTool($this->em);
        $schema->dropSchema($this->em->getMetadataFactory()->getAllMetadata());
        $schema->createSchema($this->em->getMetadataFactory()->getAllMetadata());

        $this->customersRepo = $this->em->getRepository(Customer::class);
    }

    protected function getCustomersCount(): int
    {
        return (int)$this->customersRepo->createQueryBuilder('u')
            ->select('count(u.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
