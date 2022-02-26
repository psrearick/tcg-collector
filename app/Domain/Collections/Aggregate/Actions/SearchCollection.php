<?php

namespace App\Domain\Collections\Aggregate\Actions;

use App\Domain\Cards\Base\CardSearchCollection;
use App\Domain\Cards\DataObjects\CardSearchResultsData;
use App\Domain\Collections\Aggregate\DataObjects\CollectionCardSearchParameterData;
use App\Domain\Collections\Models\Collection;
use App\Domain\Collections\Models\CollectionGeneral;
use App\Support\Collection as SupportCollection;

class SearchCollection extends CardSearchCollection
{
    public function __invoke(CollectionCardSearchParameterData $collectionCardSearchParameterData) : CardSearchResultsData
    {
        $this->cardSearchData = $collectionCardSearchParameterData->search;
        $this->cards          = $collectionCardSearchParameterData->data;
        $this->uuid           = $collectionCardSearchParameterData->uuid;

        // If the search is invalid and there are cards, return them
        // we stopped checking for valid search
//        if (count($this->cards) !== 0) {
//            return new CardSearchResultsData(['collection' => $this->cards]);
//        }

        if ($this->uuid && count($this->cards) === 0) {
            $this->cards = CollectionGeneral::uuid($this->uuid)->cards;
        }

        if (count($this->cards) === 0) {
            return new CardSearchResultsData([]);
        }

        if ($this->cardSearchData->card) {
            $this->filterOnCards();
        }

        if ($this->cardSearchData->set) {
            $this->filterOnSets();
        }

        if ($this->cardSearchData->sort) {
            $this->sort();
        }

        if ($this->cardSearchData->filters) {
            $this->filter();
        }

        return new CardSearchResultsData([
            'collection' => new SupportCollection($this->cards->values()),
        ]);
    }
}
