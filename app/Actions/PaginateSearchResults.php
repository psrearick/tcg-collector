<?php

namespace App\Actions;

use Illuminate\Support\Collection;
use App\Domain\Collections\Aggregate\DataObjects\CollectionCardSearchData;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PaginateSearchResults
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
