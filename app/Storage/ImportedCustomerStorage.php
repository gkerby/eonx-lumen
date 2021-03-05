<?php

declare(strict_types=1);

namespace App\Storage;

use App\Entities\Customer;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Kerby\EonxTestTask\Contracts\Entity\CustomerDTOInterface;

class ImportedCustomerStorage implements DoctrineCustomerStorageInterface
{
    private EntityRepository $repo;
    private EntityManagerInterface $em;

    /** @var Customer[] */
    private array $customers = [];

    /** @vr bool should we flush every customer to DB or we will flush the whole bunch of customers later on */
    private bool $flushPerCustomer = false;

    /**
     * ImportedCustomerStorage constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->repo = new EntityRepository($em, $em->getClassMetaData(Customer::class));
        $this->em = $em;
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

    public function store(CustomerDTOInterface $customer): void
    {
        $emCustomer = $this->getUnique($customer);

        $emCustomer->fillFromCustomerAdapter($customer);

        $this->em->persist($emCustomer);

        if ($this->flushPerCustomer) {
            $this->em->flush();
        } else {
            /**
             * Store entity in our array to keep track of possible email duplicates
             *
             * Well, is it memory consuming, but do the trick for test application
             */
            $this->customers[$emCustomer->getEmail()] = $emCustomer;
        }
    }

    private function findByEmail(string $email): ?Customer
    {
        /** @var Customer $object */
        $object = $this->repo->findOneBy(
            [
                'email' => $email
            ]
        );

        return $object;
    }

    /**
     * Making sure that incoming customer is unique and if it is not - returning existing entity
     *
     * @param CustomerDTOInterface $customer
     * @return Customer
     */
    private function getUnique(CustomerDTOInterface $customer): Customer
    {
        /**
         * Looking up for records which are already flushed into DB
         */
        $existing = $this->findByEmail($customer->getEmail());
        if ($existing) {
            $emCustomer = $existing;
        } else {
            $emCustomer = new Customer();
        }

        /**
         * If we do not use immediate flushing
         * and we haven't found record in DB itself
         * then look up in our array of persisted items during this session
         * and if found - return THAT item for UPDATING
         */
        if (!$this->flushPerCustomer && !$existing && array_key_exists($customer->getEmail(), $this->customers)) {
            return $this->customers[$customer->getEmail()];
        }

        return $emCustomer;
    }
}
