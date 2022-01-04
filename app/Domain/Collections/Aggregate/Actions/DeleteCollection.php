<?php

namespace App\Domain\Collections\Aggregate\Actions;

use App\Domain\Collections\Aggregate\CollectionAggregateRoot;

class DeleteCollection
{
    public function __invoke(string $uuid) : string
    {
        CollectionAggregateRoot::retrieve($uuid)
            ->deleteCollection()
            ->persist();

        return $uuid;
    }
}
