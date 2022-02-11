<?php

namespace App\Domain\Cards\Base;

use App\Domain\Cards\DataObjects\CardSearchData;
use App\Domain\Sets\Models\Set;
use Illuminate\Database\Eloquent\Builder;

abstract class CardSearchBuilder
{
    public Builder $cards;

    public CardSearchData $cardSearchData;

    public function filterOnCards() : void
    {
        $term        = preg_replace('/[^A-Za-z0-9]/', '', $this->cardSearchData->card);
        $this->cards = $this->cards
            ->where('cards.name_normalized', 'like', '%' . $term . '%');
    }

    public function filterOnSets() : void
    {
        $sets        = $this->getSetIds();
        $this->cards = $this->cards->whereIn('cards.set_id', $sets);
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

    public function sort() : void
    {
        foreach ($this->cardSearchData->sort as $field => $direction) {
            if ($field === 'collectorNumber') {
                $this->cards = $this->cards->orderByRaw('CAST(`collectorNumber` AS UNSIGNED) ASC');
            } else {
                $this->cards = $this->cards->orderBy($field, $direction);
            }
        }
    }
}
