<?php

namespace App\Actions;

use App\Domain\Base\SearchParameterData;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class PaginateSearchResults
{
    public function __invoke(?Collection $builder, SearchParameterData $searchParameterData) : LengthAwarePaginator
    {
        $search  = $searchParameterData->search;
        $builder = $builder ?: $searchParameterData->data;

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
