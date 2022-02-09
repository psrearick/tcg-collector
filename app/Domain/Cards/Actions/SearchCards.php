<?php

namespace App\Domain\Cards\Actions;

use App\Domain\Cards\DataObjects\CardSearchData;
use App\Domain\Cards\DataObjects\CardSearchResultsData;
use App\Domain\Cards\Models\Card;
use App\Domain\Cards\Base\CardSearchBuilder;
use Illuminate\Database\Eloquent\Builder;

class SearchCards extends CardSearchBuilder
{
    public function __invoke(CardSearchData $cardSearchData, ?Builder $builder = null) : CardSearchResultsData
    {
        $this->cardSearchData = $cardSearchData;

        /** @var Builder $cards */
        $cards = $builder ?: Card::notOnlineOnly()->with('set');
        $this->cards = $cards;

        if ($cardSearchData->card) {
            $this->filterOnCards();
        }

        if ($cardSearchData->set) {
            $this->filterOnSets();
        }

        if ($cardSearchData->set_id) {
            $cards->where('set_id', '=', $cardSearchData->set_id);
        }

        if ($cardSearchData->uuid) {
            $cards->where('uuid', '=', $cardSearchData->uuid);
        }

        if ($cardSearchData->sort) {
            $this->sort();
        }

        return new CardSearchResultsData([
            'builder'   => $cards,
            'search'    => $cardSearchData,
        ]);
    }
}
