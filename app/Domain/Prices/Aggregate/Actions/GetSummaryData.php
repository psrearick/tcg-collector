<?php

namespace App\Domain\Prices\Aggregate\Actions;

use App\Domain\Collections\Models\Collection as DomainCollection;
use App\Domain\Folders\Models\Folder;
use App\Domain\Prices\Aggregate\DataObjects\SummaryData;
use Brick\Math\RoundingMode;
use Brick\Money\Money;
use Illuminate\Support\Collection;

class GetSummaryData
{
    public function __invoke(?Collection $collections = null, ?Collection $folders = null, bool $hasSummary = true) : SummaryData
    {
        $totals = [
            'total_cards'       => 0,
            'current_value'     => Money::ofMinor(0, 'USD'),
            'acquired_value'    => Money::ofMinor(0, 'USD'),
        ];

        if ($collections) {
            if (!$hasSummary) {
                $collections = DomainCollection::whereIn('uuid', $collections->pluck('uuid'))->with('summary');

                $collections = $collections->get();
            }

            $collections->each(function ($collection) use (&$totals) {
                $summary = $collection->summary;
                if ($summary) {
                    $totals['total_cards'] += $summary->total_cards;
                    $totals['current_value'] = $totals['current_value']->plus(Money::ofMinor($summary->current_value, 'USD'));
                    $totals['acquired_value'] = $totals['acquired_value']->plus(Money::ofMinor($summary->acquired_value, 'USD'));
                }
            });
        }

        if ($folders) {
            if (!$hasSummary) {
                $folders = Folder::whereIn('uuid', $folders->pluck('uuid'))->with('summary')->get();
            }

            $folders->each(function ($folder) use (&$totals) {
                $summary = $folder->summary;
                if ($summary) {
                    $totals['total_cards'] += $summary->total_cards;
                    $totals['current_value'] = $totals['current_value']->plus(Money::ofMinor($summary->current_value, 'USD'));
                    $totals['acquired_value'] = $totals['acquired_value']->plus(Money::ofMinor($summary->acquired_value, 'USD'));
                }
            });
        }

        $gainLoss           = $totals['current_value']->minus($totals['acquired_value'])->getAmount();
        $acquiredValue      = $totals['acquired_value']->getAmount();
        $gainLossPercent    = $gainLoss->isEqualTo(0) ? 0 : 1;

        if (!$acquiredValue->isEqualTo(0)) {
            $gainLossPercent = $gainLoss
                ->dividedBy($acquiredValue, 4, RoundingMode::UP)->toFloat();
        }

        $gainLoss               = Money::of($gainLoss->toFloat(), 'USD');
        $acquiredValue          = $totals['acquired_value']->getMinorAmount()->toInt();
        $acquiredValueFormatted = $totals['acquired_value']->formatTo('en_US');
        $currentValue           = $totals['current_value']->getMinorAmount()->toInt();
        $currentValueFormatted  = $totals['current_value']->formatTo('en_US');

        $totals['acquired_value']           = $acquiredValue;
        $totals['display_acquired_value']   = $acquiredValueFormatted;
        $totals['current_value']            = $currentValue;
        $totals['display_current_value']    = $currentValueFormatted;
        $totals['gain_loss']                = $gainLoss->getMinorAmount()->toInt();
        $totals['display_gain_loss']        = $gainLoss->formatTo('en_US');
        $totals['gain_loss_percent']        = $gainLossPercent;

        return new SummaryData($totals);
    }
}
