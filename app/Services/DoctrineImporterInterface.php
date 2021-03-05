<?php

declare(strict_types=1);

namespace App\Services;

use Kerby\EonxTestTask\Contracts\Importer\ImporterInterface;

interface DoctrineImporterInterface extends ImporterInterface
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
