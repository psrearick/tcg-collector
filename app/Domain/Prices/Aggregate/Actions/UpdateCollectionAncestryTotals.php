<?php

namespace App\Domain\Prices\Aggregate\Actions;

use App\Domain\Collections\Models\Collection;
use App\Domain\Prices\Aggregate\Actions\GetCollectionTotals;
use App\Domain\Prices\Aggregate\Actions\UpdateFolderAncestryTotals;

class UpdateCollectionAncestryTotals
{
    public function __invoke(Collection $collection)
    {
        $collectionTotals = (new GetCollectionTotals)($collection);
        $collection->summary->update($collectionTotals);
        $collectionFolder = $collection->folder;
        if ($collectionFolder) {
            (new UpdateFolderAncestryTotals)($collectionFolder);
        }
    }
}