<?php

namespace App\Domain\Collections\Aggregate\DataObjects;

use App\App\Contracts\DataObjectInterface;

class CollectionCardUpdateData implements DataObjectInterface
{
    public string $card_uuid;

    public int $change;

    public string $collection_uuid;

    public string $finish;

    public function __construct(array $data)
    {
    }

    public function toArray() : array
    {
        return [];
    }
}
