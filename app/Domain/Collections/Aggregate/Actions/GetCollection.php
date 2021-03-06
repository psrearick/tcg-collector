<?php

namespace App\Domain\Collections\Aggregate\Actions;

use App\Domain\Collections\Aggregate\DataObjects\CollectionData;
use App\Domain\Collections\Models\Collection;

class GetCollection
{
    public function __invoke(string $uuid)
    {
        return new CollectionData(Collection::uuid($uuid)->toArray());
    }
}
