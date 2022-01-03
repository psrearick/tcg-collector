<?php

namespace App\Domain\Cards\Traits;

use App\Domain\Sets\Models\Set;

trait CardSearchBuilder
{
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

    protected function getSetIds() : array
    {
        $searchTerm  = '%' . $this->cardSearchData->set . '%';

        return Set::where('sets.name', 'like', $searchTerm)
            ->orWhere('sets.code', 'like', $searchTerm)
            ->get()
            ->pluck('id')
            ->toArray();
    }

    protected function isValidCardSearch() : bool
    {
        return true;

        return
            $this->cardSearchData->card
            || $this->cardSearchData->set
            || $this->cardSearchData->uuid
            || $this->cardSearchData->sort;
    }

    protected function sort() : void
    {
        foreach ($this->cardSearchData->sort as $field => $direction) {
            $this->cards->orderBy($field, $direction);
        }
    }
}
