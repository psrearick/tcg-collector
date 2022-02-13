<?php

namespace App\Domain\Prices\Aggregate\Actions;

use App\Actions\GetGainLossValues;
use App\Domain\Base\Collection;

class GetCollectionTotals
{
    private GetGainLossValues $getGainLossValues;

    public function __construct()
    {
        $this->getGainLossValues = new GetGainLossValues();
    }

    public function __invoke(Collection $collection) : array
    {
        $totals = [
            'total_cards'       => 0,
            'current_value'     => 0,
            'acquired_value'    => 0,
        ];

        $collection->cards->each(function ($card) use (&$totals) {
            $count = $card->pivot->quantity;
            $last  = $card
                ->prices
                ->where('type', '=', (new MatchFinish)($card->pivot->finish))
                ->sortByDesc('id')
                ->take(1)
                ->first();

            if ($last) {
                $totals['current_value'] += $count * $last->price;
            }

            $totals['total_cards'] += $count;
            $totals['acquired_value'] += $count * $card->pivot->price_when_added;
        });

        $gainLoss = $this->getGainLossValues->handle($totals['current_value'], $totals['acquired_value']);

        $totals['gain_loss']         = $gainLoss['gain_loss'];
        $totals['gain_loss_percent'] = $gainLoss['gain_loss_percent'];

        ray($totals);

        return $totals;
    }
}
