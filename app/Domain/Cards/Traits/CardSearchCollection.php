<?php

namespace App\Domain\Cards\Traits;

use App\Actions\NormalizeString;

trait CardSearchCollection
{
    protected function filter() : void
    {
        $filters = $this->cardSearchData->filters;
        foreach ($filters as $filter) {
            if (!isset($filter['query_component'])) {
                continue;
            }

            $this->cards =
                app('App\\QueryFilters\\' . $filter['query_component'])
                ->query($this->cards, $filter);
        }
    }

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
        $sortOrder = collect($this->cardSearchData->sort_order)->sort();
        $sort      = $this->cardSearchData->sort;
        $sortBy    = [];

        if ($sortOrder->count()) {
            $sortOrder->each(function ($order, $key) use (&$sortBy, $sort) {
                if (isset($sort[$key])) {
                    $sortBy[] = [$key, $sort[$key]];
                }
            });
        } else {
            foreach ($sort as $field => $direction) {
                $sortBy[] = [$field, $direction];
            }
        }

        $this->cards = $this->cards->sortBy($sortBy);
    }
}
