<?php

use App\Domain\Collections\Aggregate\DataObjects\CollectionData;
use App\Domain\Collections\Models\Collection;

class GetCollection
{
    public function __invoke(string $uuid)
    {
        return new CollectionData(Collection::inCurrentGroup()->where('uuid', '=', $uuid)->first()->toArray());
    }
}
