<?php

namespace App\Domain\Prices\Aggregate\Actions;

use App\Domain\Collections\Models\Collection;

class UpdateCollectionAncestryTotals
{
    public function __invoke(Collection $collection)
    {
        $collectionTotals         = (new GetCollectionTotals)($collection);
        $collectionTotals['type'] = 'collection';
        $collection->summary()->updateOrCreate(
            ['uuid' => $collection->uuid],
            $collectionTotals);
        $collectionFolder = $collection->folder;
        if ($collectionFolder) {
            (new UpdateFolderAncestryTotals)($collectionFolder);
        }
    }
}
