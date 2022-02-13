<?php

namespace App\Domain\Cards\Actions;

use App\Domain\Base\SearchParameterData;
use App\Domain\Collections\Models\CollectionCardSummary;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class FormatCards
{
    public function __invoke(Builder $builder, ?SearchParameterData $searchParameterData = null) : LengthAwarePaginator
    {
        $collection = $searchParameterData->uuid ?? null;
        $search     = $searchParameterData->search ?? null;

        $collectionMap = [];
        if ($collection) {
            CollectionCardSummary::where('collection_uuid', '=', $collection)
                ->each(function (CollectionCardSummary $s) use (&$collectionMap) {
                    if (!isset($collectionMap[$s->card_uuid])) {
                        $collectionMap[$s->card_uuid] = [];
                    }
                    $collectionMap[$s->card_uuid][$s->finish] = $s->quantity;
                });
        }

        if ($search->paginator) {
            $page = $search->paginator;

            $paginated = $builder->paginate(
                $page['per_page'] ?? 25, ['*'], 'page', $page['current_page'] ?? null
            );
        } else {
            $paginated = $builder->paginate(25);
        }

        return tap($paginated, static function ($paginatedInstance) use ($collectionMap) {
            return (new TransformCardCollection())($paginatedInstance->getCollection(), $collectionMap);
        });
    }
}
