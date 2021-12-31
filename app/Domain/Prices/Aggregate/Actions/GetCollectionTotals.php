<?php

namespace App\Domain\Prices\Aggregate\Actions;

use App\Domain\Collections\Models\Collection;

class GetCollectionTotals
{
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
                ->where('type', '=', $this->matchFinish($card->pivot->finish))
                ->sortByDesc('created_at')
                ->take(1)
                ->first();

            if ($last) {
                $totals['current_value'] += $count * $last->price;
            }

            $totals['total_cards'] += $count;
            $totals['acquired_value'] += $count * $card->pivot->price_when_added;
        });

        $gainLoss        = $totals['current_value'] - $totals['acquired_value'];
        $gainLossPercent = $gainLoss == 0 ? 0 : 1;
        $gainLossPercent = $totals['acquired_value'] != 0 ? $gainLoss / $totals['acquired_value'] : $gainLossPercent;

        $totals['gain_loss']         = $gainLoss;
        $totals['gain_loss_percent'] = $gainLossPercent;

        return $totals;
    }

    protected function matchFinish(string $finish) : string
    {
        return match ($finish) {
            'nonfoil'   => 'usd',
            'foil'      => 'usd_foil',
            'etched'    => 'usd_etched',
            default     => '',
        };
    }
}
