<?php

namespace App\Domain\Collections\Aggregate\Actions;

use App\Domain\Collections\Aggregate\CollectionAggregateRoot;
use App\Domain\Collections\Aggregate\DataObjects\CollectionData;
use Illuminate\Support\Str;

class CreateCollection
{
    public function __invoke(array $collection) : string
    {
        $newUuid    = Str::uuid();
        $data       = (new CollectionData($collection))->toArray();
        CollectionAggregateRoot::retrieve($newUuid)
            ->createCollection($data)
            ->persist();

        return $newUuid;
    }
}
