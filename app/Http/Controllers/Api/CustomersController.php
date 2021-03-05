<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Exceptions\CustomerNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Resources\CustomerResource;
use App\Http\Resources\CustomerShortResourceCollection;
use App\Repository\CustomerRepositoryInterface;

class CustomersController extends Controller
{
    private CustomerRepositoryInterface $repo;

    public function __construct(CustomerRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function index(): CustomerShortResourceCollection
    {
        return new CustomerShortResourceCollection($this->repo->findBy([], [], 30, 0));
    }

    public function get($id): CustomerResource
    {
        $customer = $this->repo->find($id);

        if (!$customer) {
            throw new CustomerNotFoundException();
        }

        return new CustomerResource($customer);
    }
}
