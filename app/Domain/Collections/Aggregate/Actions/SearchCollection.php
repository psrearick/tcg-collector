<?php

namespace App\Domain\Collections\Aggregate\Actions;

use App\Domain\Cards\DataObjects\CardSearchData;
use App\Domain\Cards\DataObjects\CardSearchResultsData;
use App\Domain\Cards\Traits\CardSearchCollection;
use App\Domain\Collections\Aggregate\DataObjects\CollectionCardSearchParameterData;
use App\Domain\Collections\Models\Collection;
use App\Support\Collection as SupportCollection;

class SearchCollection
{
    use CardSearchCollection;

    protected SupportCollection $cards;

    protected CardSearchData $cardSearchData;

    protected ?string $uuid;

    public function __invoke(CollectionCardSearchParameterData $collectionCardSearchParameterData) : CardSearchResultsData
    {
        $this->cardSearchData = $collectionCardSearchParameterData->search;
        $this->cards          = $collectionCardSearchParameterData->data;
        $this->uuid           = $collectionCardSearchParameterData->uuid;

        if (!$this->isValidCardSearch()) {
            if ($this->cards) {
                return new CardSearchResultsData(['collection' => $this->cards]);
            }

            return new CardSearchResultsData([]);
        }

        if ($this->uuid && !$this->cards) {
            $this->cards = Collection::where('uuid', '=', $this->uuid);
        }

        if (!$this->cards) {
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
            'collection'       => $this->cards->values(),
        ]);
    }
}
