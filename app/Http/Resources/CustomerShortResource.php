<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Entities\Customer;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class CustomerShortResource
 * @package App\Http\Resources
 *
 * @property Customer $resource
 */
class CustomerShortResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->resource->getId(),
            'fullName' => $this->resource->getFullName(),
            'email' => $this->resource->getEmail(),
            'country' => $this->resource->getCountry(),
        ];
    }
}
