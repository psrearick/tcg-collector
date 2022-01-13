<?php

namespace App\Domain\Stores\DataObjects;

use App\App\Contracts\DataObjectInterface;

class StoreData implements DataObjectInterface
{
    public ?string $created_at;

    public string $name;

    public function __construct(array $data)
    {
        $this->name       = $data['name'] ?? '';
        $this->created_at = $data['created_at'] ?? null;
    }

    public function toArray() : array
    {
        return [
            'name'       => $this->name,
            'created_at' => $this->created_at,
        ];
    }
}
