<?php

namespace App\Domain\Collections\Aggregate\Actions;

use Brick\Math\BigInteger;
use Brick\Math\RoundingMode;
use Brick\Money\Money;
use Illuminate\Support\Collection;

class CalculateSummary
{
    public function __invoke(Collection $collectionItems) : array
    {
        $totals = [
            'total_cards'       => 0,
            'current_value'     => Money::ofMinor(0, 'USD'),
            'acquired_value'    => Money::ofMinor(0, 'USD'),
        ];

        $collectionItems->each(function ($item) use (&$totals) {
            $item = is_array($item) ? $item : $item->toArray();
            $totals['total_cards'] += $item['quantity'];
            $quantity = BigInteger::of($item['quantity']);
            $totals['current_value'] = $totals['current_value']
                ->plus(Money::ofMinor($item['price'], 'USD')->multipliedBy($quantity));
            $totals['acquired_value'] = $totals['acquired_value']
                ->plus(Money::ofMinor($item['acquired_price'], 'USD')->multipliedBy($quantity));
        });

        $gainLoss           = $totals['current_value']->minus($totals['acquired_value'])->getAmount();
        $acquiredValue      = $totals['acquired_value']->getAmount();
        $gainLossPercent    = $gainLoss->isEqualTo(0) ? 0 : 1;

        if (!$acquiredValue->isEqualTo(0)) {
            $gainLossPercent = $gainLoss
                ->dividedBy($acquiredValue, 4, RoundingMode::UP)->toFloat();
        }

        $totals['acquired_value']        = $totals['acquired_value']->formatTo('en_US');
        $totals['current_value']         = $totals['current_value']->formatTo('en_US');
        $totals['gain_loss']             = Money::of($gainLoss->toFloat(), 'USD')->formatTo('en_US');
        $totals['gain_loss_percent']     = $gainLossPercent;

        return $totals;
    }
}
