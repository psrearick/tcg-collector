<?php

namespace App\Domain\Collections\Aggregate\Actions;

use App\Domain\Cards\DataObjects\CardSearchData;
use App\Domain\Cards\DataObjects\CardSearchResultsData;
use App\Domain\Cards\Traits\CardSearchCollection;
use App\Domain\Collections\Aggregate\DataObjects\CollectionCardSearchData;
use App\Domain\Collections\Models\Collection;
use App\Support\Collection as SupportCollection;
use Illuminate\Database\Eloquent\Builder;

class SearchCollection
{
    use CardSearchCollection;

    protected SupportCollection $cards;

    protected CardSearchData $cardSearchData;

    protected ?string $uuid;

    public function __invoke(CollectionCardSearchData $collectionCardSearchData) : CardSearchResultsData
    {
        $this->cardSearchData = $collectionCardSearchData->search;
        $this->cards          = $collectionCardSearchData->data;
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
            'collection'       => $this->cards,
        ]);
    }
}
