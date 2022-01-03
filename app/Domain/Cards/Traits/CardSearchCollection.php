<?php

namespace App\Domain\Cards\Traits;

use App\Actions\NormalizeString;
use App\Domain\Sets\Models\Set;

trait CardSearchCollection
{
    protected function filterOnCards() : void
    {
        $term = (new NormalizeString)($this->cardSearchData->card);

        $this->cards = $this->cards->filter(function ($card) use ($term) {
            return false !== stristr($card['name_normalized'], $term);
        });
    }

    protected function filterOnSets() : void
    {
        $term = (new NormalizeString)($this->cardSearchData->set);

        $this->cards = $this->cards->filter(function ($card) use ($term) {
            $set_name = (new NormalizeString)($card['set_name']);

            return (false !== stristr($card['set'], $term))
                || (false !== stristr($set_name, $term));
        });
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
