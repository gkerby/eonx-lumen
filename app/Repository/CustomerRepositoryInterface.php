<?php

namespace App\Repository;

/**
 * Interface CustomerRepositoryInterface
 * @package App\Repository
 *
 * Just proxy some methods from Doctrine's standard repository
 */
interface CustomerRepositoryInterface
{
    public function find($id, $lockMode = null, $lockVersion = null);

    public function findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null);
}
