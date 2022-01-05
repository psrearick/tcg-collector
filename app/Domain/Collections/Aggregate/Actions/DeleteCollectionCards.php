<?php

namespace App\Domain\Collections\Aggregate\Actions;

use App\Domain\Collections\Aggregate\CollectionAggregateRoot;

class DeleteCollectionCards
{
    public function __invoke(string $uuid, array $cards) : string
    {
        CollectionAggregateRoot::retrieve($uuid)
            ->deleteCollectionCards($uuid, $cards)
            ->persist();

        return $uuid;
    }
}
