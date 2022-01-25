<?php

namespace App\Domain\Prices\Aggregate\DataObjects;

use App\App\Contracts\DataObjectInterface;

class PriceProviderData implements DataObjectInterface
{
    public string $name;

    public string $uuid;

    public function __construct(array $data)
    {
        $this->uuid = $data['uuid'] ?? '';
        $this->name = $data['name'] ?? '';
    }

    public function toArray() : array
    {
        return [
            'uuid'  => $this->uuid,
            'name'  => $this->name,
        ];
    }
}
