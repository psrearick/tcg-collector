<?php

namespace App\Domain\Collections\Aggregate\Actions;

use App\Actions\PaginateSearchResults;
use App\Domain\Collections\Aggregate\DataObjects\CollectionCardSearchData;
use App\Support\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class FormatCollectionCards
{
    public function __invoke(Collection $builder, CollectionCardSearchData $collectionCardSearchData) : LengthAwarePaginator
    {
        $needsGrouped = false;
        $settings = auth()->user()->settings->first();
        if (optional($settings)->tracks_condition || optional($settings)->tracks_price) {
            $needsGrouped = true;
        }

        if ($needsGrouped) {
            $builder = $builder->mapToGroups(function ($group) {
                return [$group->uuid => $group];
            })
            ->map(function ($cardGroup) {
                return $cardGroup->mapToGroups(function ($group) {
                    return [$group->finish => $group];
                });
            })
            ->values();
        }
        
        return (new PaginateSearchResults())($builder, $collectionCardSearchData);
    }
}
