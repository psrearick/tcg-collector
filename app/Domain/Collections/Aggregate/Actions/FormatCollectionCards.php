<?php

namespace App\Domain\Collections\Aggregate\Actions;

use App\Domain\Cards\Actions\BuildCard;
use App\Domain\Collections\Aggregate\DataObjects\CollectionCardData;
use App\Domain\Collections\Aggregate\DataObjects\CollectionCardSearchData;
use App\Domain\Prices\Aggregate\Actions\GetLatestPrices;
use App\Domain\Prices\Aggregate\Actions\MatchFinish;
use App\Support\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class FormatCollectionCards
{
    public function __invoke(Collection $builder, CollectionCardSearchData $collectionCardSearchData) : LengthAwarePaginator
    {
        $search = $collectionCardSearchData->search;

        if ($search->paginator) {
            $page = $search->paginator;

            $paginated = $builder->paginate(
                $page['per_page'] ?? 25, null, $page['current_page'] ?? null, 'page'
            );
        }

        if (!isset($paginated)) {
            $paginated = $builder->paginate(25);
        }

        if (!$paginated) {
            return (new Collection([]))->paginate(25);
        }

        return $paginated->withQueryString();
    }
}
