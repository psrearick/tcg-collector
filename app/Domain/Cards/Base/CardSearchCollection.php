<?php

namespace App\Domain\Cards\Base;

use App\Actions\NormalizeString;
use App\Domain\Cards\DataObjects\CardSearchData;
use Illuminate\Support\Collection;

abstract class CardSearchCollection
{
    protected Collection $cards;

    protected CardSearchData $cardSearchData;

    protected ?string $uuid;

    public function filter() : void
    {
        foreach ($this->cardSearchData->filters as $filter) {
            if (!isset($filter['query_component'])) {
                continue;
            }

            $this->cards =
                app('App\\QueryFilters\\' . $filter['query_component'])
                ->query($this->cards, $filter);
        }
    }

    public function filterOnCards() : void
    {
        $term = (new NormalizeString)($this->cardSearchData->card);

        $this->cards = $this->cards->filter(function ($card) use ($term) {
            return stripos($card->name_normalized, $term) !== false;
        });
    }

    public function filterOnSets() : void
    {
        $term = (new NormalizeString)($this->cardSearchData->set);

        $this->cards = $this->cards->filter(function ($card) use ($term) {
            $set_name = (new NormalizeString)($card->set_name);

            return (stripos($card->set, $term) !== false)
                || (stripos($set_name, $term) !== false);
        });
    }

    public function sort() : void
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
