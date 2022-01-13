<?php

namespace App\Domain\Stores\Presenters;

use App\Actions\PaginateSearchResults;
use App\App\Contracts\PresenterInterface;
use App\Domain\Stores\Actions\GetStores;
use App\Domain\Stores\DataObjects\StoreData;
use App\Domain\Stores\DataObjects\StoreSearchData;
use App\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Domain\Stores\DataObjects\StoreSearchParameterData;

class StoreShowPresenter implements PresenterInterface
{
    private ?array $pagination;

    public function __construct(?array $pagination = [])
    {
        $this->pagination = $pagination;
    }

    public function present() : array
    {
        $stores = (new GetStores)();
        $search = new StoreSearchParameterData([
            'data' => $stores,
            'search' => new StoreSearchData([
                'paginator' => $this->pagination,
            ]),
        ]);
        
        $paginated = (new PaginateSearchResults)(null, $search);


        return [
            'stores' => $paginated,
        ];
    }
}