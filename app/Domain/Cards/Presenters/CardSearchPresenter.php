<?php

namespace App\Domain\Cards\Presenters;

use App\App\Contracts\CardSearchDataInterface;
use App\App\Contracts\PresenterInterface;
use App\Domain\Cards\Actions\FormatCards;
use App\Domain\Cards\Actions\SearchCards;
use App\Domain\Cards\DataObjects\CardSearchData;
use App\Domain\Cards\DataObjects\CardSearchParameterData;

class CardSearchPresenter implements PresenterInterface
{
    private SearchCards $searchCards;

    private CardSearchData $searchData;

    public function __construct(CardSearchDataInterface $searchData)
    {
        $this->searchData   = $searchData;
        $this->searchCards  = new SearchCards;
    }

    public function present() : array
    {
        $results    = ($this->searchCards)($this->searchData);
        $parameter  = new CardSearchParameterData([
            'search'    => $this->searchData,
            'builder'   => $results->builder,
        ]);

        return [
            'data' => (new FormatCards())($results->builder, $parameter),
        ];
    }
}
