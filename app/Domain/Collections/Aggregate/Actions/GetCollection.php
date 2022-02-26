<?php

namespace App\Domain\Collections\Aggregate\Actions;

use App\Domain\Collections\Aggregate\DataObjects\CollectionData;
use App\Domain\Collections\Models\Collection;

class GetCollection
{
    public function execute(string $uuid) : CollectionData
    {
        $collection = Collection::uuid($uuid);

        return new CollectionData(optional($collection)->toArray() ?: []);
    }
}
