<?php

namespace App\Domain\Collections\Aggregate\Actions;

use App\Domain\Collections\Aggregate\DataObjects\CollectionData;
use App\Domain\Collections\Aggregate\CollectionAggregateRoot;


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
