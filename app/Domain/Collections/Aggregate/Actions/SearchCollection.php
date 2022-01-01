<?php

namespace App\Domain\Collections\Aggregate\Actions;

use App\Domain\Cards\DataObjects\CardSearchData;
use App\Domain\Cards\DataObjects\CardSearchResultsData;
use App\Domain\Cards\Traits\CardSearch;
use App\Domain\Collections\Aggregate\DataObjects\CollectionCardSearchData;
use App\Domain\Collections\Models\Collection;
use Illuminate\Database\Eloquent\Builder;

class SearchCollection
{
    use CardSearch;

    protected ?Builder $cards;

    protected CardSearchData $cardSearchData;

    protected bool $isCollection = false;

    protected ?string $uuid;

    public function __invoke(CollectionCardSearchData $collectionCardSearchData)
    {
        $this->cardSearchData = $collectionCardSearchData->search;
        $this->cards          = $collectionCardSearchData->builder;
        $this->uuid           = $collectionCardSearchData->uuid;

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

        return new CardSearchResultsData([
            'builder'       => $this->cards,
        ]);
    }
}
