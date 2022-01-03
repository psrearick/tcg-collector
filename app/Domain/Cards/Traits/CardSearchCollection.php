<?php

namespace App\Domain\Cards\Traits;

use App\Domain\Sets\Models\Set;

trait CardSearchCollection
{
    protected function filterOnCards() : void
    {
        $term = preg_replace('/[^A-Za-z0-9]/', '', $this->cardSearchData->card);

        $this->cards = $this->cards->filter(function ($card) use ($term) {
            return false !== stristr($card['name_normalized'], $term);
        });
    }

    protected function filterOnSets() : void
    {
        $sets        = $this->getSetIds();
        $this->cards = $this->cards->whereIn('set_id', $sets);
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
        $sortBy = [];
        foreach ($this->cardSearchData->sort as $field => $direction) {
            $sortBy[] = [$field, $direction];
        }

        $this->cards = $this->cards->sortBy($sortBy);
    }
}
