<?php

namespace App\Domain\Collections\Aggregate\Actions;

use App\Domain\Collections\Aggregate\CollectionAggregateRoot;
use App\Domain\Collections\Aggregate\DataObjects\CollectionData;

class UpdateCollection
{
    public function __invoke(array $collection)
    {
        $data = (new CollectionData($collection))->toArray();
        $uuid = $data['uuid'];
        CollectionAggregateRoot::retrieve($uuid)
            ->updateCollection($data)
            ->persist();

        return $uuid;
    }
}
