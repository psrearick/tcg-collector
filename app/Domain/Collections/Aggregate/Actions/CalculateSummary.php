<?php

namespace App\Domain\Collections\Aggregate\Actions;

use Illuminate\Support\Collection;

class CalculateSummary
{
    public function __invoke(Collection $collectionItems) : array
    {
        $totals = [
            'total_cards'       => 0,
            'current_value'     => 0,
            'acquired_value'    => 0,
        ];

        $collectionItems->each(function ($item) use (&$totals) {
            $item = is_array($item) ? $item : $item->toArray();
            $totals['total_cards'] += $item['quantity'];
            $totals['current_value'] += $item['price'] * $item['quantity'];
            $totals['acquired_value'] += $item['acquired_price'] * $item['quantity'];
        });

        $gainLoss        = $totals['current_value'] - $totals['acquired_value'];
        $gainLossPercent = $gainLoss == 0 ? 0 : 1;
        $gainLossPercent = $totals['acquired_value'] != 0 ? $gainLoss / $totals['acquired_value'] : $gainLossPercent;

        $totals['gain_loss']         = $gainLoss;
        $totals['gain_loss_percent'] = $gainLossPercent;

        return $totals;
    }
}
