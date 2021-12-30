<?php

namespace App\Domain\Collections\Aggregate\Actions;

use App\Domain\Collections\Aggregate\CollectionAggregateRoot;

class MoveCollection
{
    public function __invoke(string $uuid, string $destination, int $userId) : string
    {
        CollectionAggregateRoot::retrieve($uuid)
            ->moveCollection($uuid, $destination, $userId)
            ->persist();

        return $uuid;
    }
}
