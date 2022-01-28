<?php

namespace App\Domain\Prices\Aggregate\Actions;

use App\Domain\Collections\Models\Collection;

class UpdateCollectionAncestryTotals
{
    public function __invoke(Collection $collection) : void
    {
        $collectionTotals         = (new GetCollectionTotals)($collection);
        $collection->summary()->updateOrCreate([
            'uuid'  => $collection->uuid,
            'type'  => 'collection,'
        ], $collectionTotals);
        $collectionFolder = $collection->folder;
        if ($collectionFolder) {
            (new UpdateFolderAncestryTotals)($collectionFolder);
        }
    }
}
