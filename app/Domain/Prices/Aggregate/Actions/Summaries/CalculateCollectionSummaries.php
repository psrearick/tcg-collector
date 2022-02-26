<?php

namespace App\Domain\Prices\Aggregate\Actions\Summaries;

use App\Domain\Collections\Models\Collection;
use App\Domain\Folders\Models\Folder;

class CalculateCollectionSummaries
{
    private CalculateCollectionTotals $calculate;

    public function __construct(CalculateCollectionTotals $calculateCollectionTotals)
    {
        $this->calculate = $calculateCollectionTotals;
    }

    public function execute(?Folder $folder = null) : array
    {
        return Collection::query()
            ->with('cards')
            ->where('folder_uuid', '=', $folder->uuid ?? null)
            ->get()
            ->map(function (Collection $collection) {
                return $this->calculate->execute($collection);
            })
            ->toArray();
    }
}
