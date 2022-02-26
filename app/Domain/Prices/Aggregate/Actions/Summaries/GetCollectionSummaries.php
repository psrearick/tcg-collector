<?php

namespace App\Domain\Prices\Aggregate\Actions\Summaries;

use App\Domain\Collections\Models\CollectionGeneral;
use App\Domain\Folders\Models\Folder;

class GetCollectionSummaries
{
    public function execute(?Folder $folder = null) : array
    {
        return CollectionGeneral::query()
            ->with('summary')
            ->where('folder_uuid', '=', $folder->uuid ?? null)
            ->get()
            ->map(fn (CollectionGeneral $collection) => $collection->summary->attributesToArray())
            ->toArray();
    }
}
