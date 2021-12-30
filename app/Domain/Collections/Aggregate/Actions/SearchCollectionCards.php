<?php

namespace App\Domain\Collections\Aggregate\Actions;

use App\Domain\Cards\Actions\SearchCards;
use App\Domain\Collections\Aggregate\DataObjects\CollectionCardSearchData;
use App\Domain\Collections\Aggregate\DataObjects\CollectionCardSearchResults;
use Illuminate\Database\Eloquent\Builder;

class SearchCollectionCards
{
    protected ?Builder $cards;

    protected CollectionCardSearchData $collectionCardSearchData;

    public function __invoke(CollectionCardSearchData $collectionCardSearchData) : CollectionCardSearchResults
    {
        $this->collectionCardSearchData = $collectionCardSearchData;
        $searchCards                    = new SearchCards;
        $cardSearchResults              = $searchCards($collectionCardSearchData->search);
        $this->cards                    = $cardSearchResults->builder;

        if (!$this->cards) {
            return new CollectionCardSearchResults([]);
        }

        $this->addRelations();

        return new CollectionCardSearchResults(['builder' => $this->cards]);
    }

    private function addRelations() : void
    {
        $uuid = $this->CollectionCardSearchData->uuid;

        $this->cards->load([
            'collections' => function ($query) use ($uuid) {
                $query->where('collections.uuid', '=', $uuid);
            },
            'frameEffects',
            'set',
            'prices',
            'prices.priceProvider',
        ]);
    }
}
