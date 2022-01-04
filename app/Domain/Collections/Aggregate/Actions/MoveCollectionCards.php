<?php

namespace App\Domain\Collections\Aggregate\Actions;

use App\Domain\Collections\Aggregate\CollectionAggregateRoot;

class MoveCollectionCards
{
    public function __invoke(string $uuid, string $destination, array $cards) : string
    {
        CollectionAggregateRoot::retrieve($uuid)
            ->moveCollectionCards($uuid, $destination, $cards)
            ->persist();

        return $uuid;
    }
}
