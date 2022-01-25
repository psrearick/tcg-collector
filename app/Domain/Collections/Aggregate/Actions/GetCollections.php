<?php

namespace App\Domain\Collections\Aggregate\Actions;

use App\Domain\Collections\Aggregate\DataObjects\CollectionData;
use App\Domain\Collections\Models\Collection;

class GetCollections
{
    public function __invoke() : array
    {
        return Collection::all()->map(function ($collection) {
            return (new CollectionData($collection->toArray()))->toArray();
        })->values()->toArray();
    }
}
