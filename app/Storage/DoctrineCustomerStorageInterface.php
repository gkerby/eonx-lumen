<?php

declare(strict_types=1);

namespace App\Storage;

use App\Entities\Customer;
use App\Services\DoctrineImporterInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Kerby\EonxTestTask\Contracts\Entity\CustomerDTOInterface;
use Kerby\EonxTestTask\Contracts\Storage\CustomerStorageInterface;

interface DoctrineCustomerStorageInterface extends CustomerStorageInterface
{
    /**
     * flushPerCustomer controls if imported entity should be immediately flushed into DB or the whole bunch of entities will be flushed after import
     *
     * @param bool $flushPerCustomer
     * @return $this
     */
    public function setFlushPerCustomer(bool $flushPerCustomer): self;

    public function getFlushPerCustomer(): bool;
}
