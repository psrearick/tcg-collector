<?php

namespace App\Domain\Cards\Actions;

use App\Domain\Cards\DataObjects\CardSearchData;
use App\Domain\Cards\DataObjects\CardSearchResultsData;
use App\Domain\Cards\Models\Card;
use App\Domain\Cards\Traits\CardSearch;
use Illuminate\Database\Eloquent\Builder;

class SearchCards
{
    use CardSearch;

    protected Builder $cards;

    protected CardSearchData $cardSearchData;

    public function __invoke(CardSearchData $cardSearchData) : CardSearchResultsData
    {
        $this->cardSearchData = $cardSearchData;
        if (!$this->isValidCardSearch()) {
            return new CardSearchResultsData([]);
        }

        $this->cards = Card::with('set')->notOnlineOnly();

        if ($this->cardSearchData->card) {
            $this->filterOnCards();
        }

        if ($this->cardSearchData->set) {
            $this->filterOnSets();
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
