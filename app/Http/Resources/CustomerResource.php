<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Entities\Customer;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class CustomerResource
 * @package App\Http\Resources
 *
 * @property Customer $resource
 */
class CustomerResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->resource->getId(),
            'fullName' => $this->resource->getFullName(),
//            'firstName' => $this->resource->getFirstName(),
//            'lastName' => $this->resource->getLastName(),
            'email' => $this->resource->getEmail(),
            'country' => $this->resource->getCountry(),
            'username' => $this->resource->getUsername(),
            'gender' => $this->resource->getGender(),
            'city' => $this->resource->getCity(),
            'phone' => $this->resource->getPhone(),
        ];
    }
}
