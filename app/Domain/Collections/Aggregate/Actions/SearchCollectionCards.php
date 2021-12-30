<?php

namespace App\Domain\Collections\Aggregate\Actions;

use App\Domain\Cards\Actions\SearchCards;
use App\Domain\Collections\Aggregate\DataObjects\CollectionCardSearchData;
use App\Domain\Collections\Aggregate\DataObjects\CollectionCardSearchResultsData;
use Illuminate\Database\Eloquent\Builder;

class SearchCollectionCards
{
    protected ?Builder $cards;

    protected CollectionCardSearchData $collectionCardSearchData;

    public function __invoke(CollectionCardSearchData $collectionCardSearchData) : CollectionCardSearchResultsData
    {
        $this->collectionCardSearchData = $collectionCardSearchData;
        $searchCards                    = new SearchCards;
        $cardSearchResults              = $searchCards($collectionCardSearchData->search);
        $this->cards                    = $cardSearchResults->builder;

        if (!$this->cards) {
            return new CollectionCardSearchResultsData([]);
        }

        $this->addRelations();

        return new CollectionCardSearchResultsData(['builder' => $this->cards]);
    }

    private function addRelations() : void
    {
        $uuid = $this->collectionCardSearchData->uuid;

        $this->cards->with([
            'collections' => function ($query) use ($uuid) {
                $query->where('collections.uuid', '=', $uuid);
            },
            'finishes',
            'frameEffects',
            'set',
            'prices',
            'prices.priceProvider',
        ]);
    }
}
