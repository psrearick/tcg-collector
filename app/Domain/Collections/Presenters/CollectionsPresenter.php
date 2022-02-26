<?php

namespace App\Domain\Collections\Presenters;

use App\App\Contracts\PresenterInterface;
use App\Domain\Cards\DataObjects\CardSearchData;
use App\Domain\Collections\Aggregate\Actions\CalculateSummary;
use App\Domain\Collections\Aggregate\Actions\FormatCollectionCards;
use App\Domain\Collections\Aggregate\Actions\GetCollection;
use App\Domain\Collections\Aggregate\Actions\GetCollectionCards;
use App\Domain\Collections\Aggregate\Actions\SearchCollection;
use App\Domain\Collections\Aggregate\DataObjects\CollectionCardSearchParameterData;
use App\Domain\Collections\Aggregate\DataObjects\CollectionData;
use Brick\Money\Exception\UnknownCurrencyException;

class CollectionsPresenter implements PresenterInterface
{
    private CollectionData $collection;

    private array $request;

    private string $uuid;

    public function __construct(array $request, string $uuid)
    {
        $this->request         = $request;
        $this->uuid            = $uuid;
        $this->collection      = app(GetCollection::class)->execute($uuid);
    }

    /**
     * @throws UnknownCurrencyException
     */
    public function present() : array
    {
        $this->processRequest();
        $collectionCards = (new GetCollectionCards)($this->uuid);
        $searchData      = new CollectionCardSearchParameterData([
            'data'      => $collectionCards,
            'uuid'      => $this->uuid,
            'search'    => new CardSearchData($this->request),
        ]);
        $searchResults    = (new SearchCollection)($searchData)->collection;
        $list             = (new FormatCollectionCards)($searchResults, $searchData);
        $summary          = (new CalculateSummary)($searchResults);

        return [
            'collection'    => $this->collection,
            'list'          => $list,
            'search'        => $searchData->search,
            'summary'       => $summary,
        ];
    }

    private function processRequest() : void
    {
        $sort = $this->request['sort'] ?? [];
        if ($sort) {
            foreach ($sort as $field => $value) {
                if ($field === 'display_acquired_price') {
                    $sort['acquired_price'] = $value;
                    unset($sort['display_acquired_price']);
                }

                if ($field === 'display_price') {
                    $sort['price'] = $value;
                    unset($sort['display_price']);
                }
                $this->request['sort'] = $sort;
            }
        }

        $sortOrder = $this->request['sortOrder'] ?? [];
        if ($sortOrder) {
            foreach ($sortOrder as $field => $order) {
                if ($field === 'display_acquired_price') {
                    $sortOrder['acquired_price'] = $order;
                    unset($sortOrder['display_acquired_price']);
                }

                if ($field === 'display_price') {
                    $sortOrder['price'] = $order;
                    unset($sortOrder['display_price']);
                }
            }
            $this->request['sortOrder'] = $sortOrder;
        }

        $filters = $this->request['filters'] ?? [];
        if ($filters) {
            foreach ($filters as $field => $filter) {
                if ($field === 'display_acquired_price') {
                    $filter['field']           = 'acquired_price';
                    $filters['acquired_price'] = $filter;
                    unset($filters['display_acquired_price']);
                }

                if ($field === 'display_price') {
                    $filter['field']  = 'price';
                    $filters['price'] = $filter;
                    unset($filters['display_price']);
                }
            }
            $this->request['filters'] = $filters;
        }
    }
}
