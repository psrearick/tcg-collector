<?php

namespace App\Domain\Collections\Presenters;

use App\App\Contracts\PresenterInterface;
use App\Domain\Cards\DataObjects\CardSearchData;
use App\Domain\Collections\Aggregate\Actions\CalculateSummary;
use App\Domain\Collections\Aggregate\Actions\FormatCollectionCards;
use App\Domain\Collections\Aggregate\Actions\GetCollectionCards;
use App\Domain\Collections\Aggregate\Actions\SearchCollection;
use App\Domain\Collections\Aggregate\DataObjects\CollectionCardSearchData;
use App\Domain\Collections\Aggregate\DataObjects\CollectionData;
use App\Domain\Collections\Aggregate\Queries\CollectionCardsSummary;
use GetCollection;

class CollectionsPresenter implements PresenterInterface
{
    private CollectionData $collection;

    private array $request;

    private string $uuid;

    public function __construct(array $request, string $uuid)
    {
        $this->request         = $request;
        $this->uuid            = $uuid;
        $this->collection      = (new GetCollection)($uuid);
    }

    public function present() : array
    {
        $collectionCards = (new GetCollectionCards)($this->uuid);
        $searchData      = new CollectionCardSearchData([
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
}
