<?php

namespace App\Domain\Cards\Actions;

use App\Domain\Base\SearchParameterData;
use App\Domain\Collections\Models\CollectionCardSummary;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class FormatCardsWithPagination
{
    public function __invoke(Builder $builder, ?SearchParameterData $collectionCardSearchParameterData = null) : Collection
    {
        $collection     = $collectionCardSearchParameterData->uuid ?? null;
        $collectionMap  = [];

        CollectionCardSummary::where('collection_uuid', '=', $collection)
            ->each(function (CollectionCardSummary $s) use (&$collectionMap) {
                if (!isset($collectionMap[$s->card_uuid])) {
                    $collectionMap[$s->card_uuid] = [];
                }
                $collectionMap[$s->card_uuid][$s->finish] = $s->quantity;
            });

        return (new TransformCardCollection())($builder->get(), $collectionMap);
    }
}
