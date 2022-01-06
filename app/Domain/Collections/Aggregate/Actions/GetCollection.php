<?php

use App\Domain\Collections\Aggregate\DataObjects\CollectionData;
use App\Domain\Collections\Models\Collection;

class GetCollection
{
    public function __invoke(string $uuid, bool $inGroup = false)
    {
        if ($inGroup) {
            return new CollectionData(Collection::inCurrentGroup()->where('uuid', '=', $uuid)->first()->toArray());
        }
        
        return new CollectionData(Collection::uuid($uuid)->toArray());
    }
}
