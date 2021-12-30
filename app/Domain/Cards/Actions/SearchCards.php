<?php

namespace App\Domain\Cards\Actions;

use App\Domain\Cards\DataObjects\CardSearchData;
use App\Domain\Cards\DataObjects\CardSearchResults;
use App\Domain\Cards\Models\Card;
use App\Domain\Sets\Models\Set;
use Illuminate\Database\Eloquent\Builder;

class SearchCards
{
    protected Builder $cards;

    protected CardSearchData $cardSearchData;

    public function __invoke(CardSearchData $cardSearchData) : CardSearchResults
    {
        $this->cardSearchData = $cardSearchData;
        if (!$this->isValidCardSearch()) {
            return new CardSearchResults([]);
        }

        $this->cards = Card::with('set');

        if ($this->cardSearchData->card) {
            $this->filterOnCards();
        }

        if ($this->cardSearchData->set) {
            $this->filterOnSets();
        }

        if ($this->cardSearchData->sort) {
            $this->sort();
        }

        return new CardSearchResults(['builder' => $this->cards]);
    }

    public function getSetIds() : array
    {
        $searchTerm  = '%' . $this->cardSearchData->set . '%';

        return Set::where('sets.name', 'like', $searchTerm)
            ->orWhere('sets.code', 'like', $searchTerm)
            ->get()
            ->pluck('id')
            ->toArray();
    }

    protected function filterOnCards() : void
    {
        $term = preg_replace('/[^A-Za-z0-9]/', '', $this->cardSearchData->card);
        $this->cards->where('cards.name_normalized', 'like', '%' . $term . '%');
    }

    protected function filterOnSets() : void
    {
        $sets = $this->getSetIds();
        $this->cards->whereIn('cards.set_id', $sets);
    }

    protected function isValidCardSearch() : bool
    {
        return $this->cardSearchData->card || $this->cardSearchData->set;
    }

    protected function sort() : void
    {
        $sort = [];
        foreach ($this->cardSearchData->sort as $sort) {
            if (is_string($sort)) {
                $sort[] = [$sort, 'asc'];

                continue;
            }

            $sort[] = $sort;
        }
        $this->cards->sortBy($sort);
    }
}
