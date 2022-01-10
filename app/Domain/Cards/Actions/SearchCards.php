<?php

namespace App\Domain\Cards\Actions;

use App\Domain\Cards\DataObjects\CardSearchData;
use App\Domain\Cards\DataObjects\CardSearchResultsData;
use App\Domain\Cards\Models\Card;
use App\Domain\Cards\Traits\CardSearchBuilder;
use Illuminate\Database\Eloquent\Builder;

class SearchCards
{
    use CardSearchBuilder;

    protected Builder $cards;

    protected CardSearchData $cardSearchData;

    public function __invoke(CardSearchData $cardSearchData, ?Builder $builder = null) : CardSearchResultsData
    {
        $this->cardSearchData = $cardSearchData;
        if (!$this->isValidCardSearch()) {
            return new CardSearchResultsData([]);
        }

        $this->cards = $builder ?: Card::with('set')->notOnlineOnly();

        if ($this->cardSearchData->card) {
            $this->filterOnCards();
        }

        if ($this->cardSearchData->set) {
            $this->filterOnSets();
        }

        if ($this->cardSearchData->set_id) {
            $this->cards->where('set_id', '=', $this->cardSearchData->set_id);
        }

        if ($this->cardSearchData->uuid) {
            $this->cards->where('uuid', '=', $this->cardSearchData->uuid);
        }

        if ($this->cardSearchData->sort) {
            $this->sort();
        }

        return new CardSearchResultsData(['builder' => $this->cards]);
    }
}
