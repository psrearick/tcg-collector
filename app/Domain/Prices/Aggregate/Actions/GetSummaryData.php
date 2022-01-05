<?php

namespace App\Domain\Prices\Aggregate\Actions;

use App\Domain\Collections\Models\Collection as DomainCollection;
use App\Domain\Folders\Models\Folder;
use Illuminate\Support\Collection;

class GetSummaryData
{
    public function __invoke(?Collection $collections = null, ?Collection $folders = null, bool $hasSummary = true)
    {
        $totals = [
            'total_cards'       => 0,
            'current_value'     => 0,
            'acquired_value'    => 0,
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
                    $totals['current_value'] += $summary->current_value;
                    $totals['acquired_value'] += $summary->acquired_value;
                }
                // }
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
                    $totals['current_value'] += $summary->current_value;
                    $totals['acquired_value'] += $summary->acquired_value;
                }
            });
        }

        $gainLoss        = $totals['current_value'] - $totals['acquired_value'];
        $gainLossPercent = $gainLoss == 0 ? 0 : 1;
        $gainLossPercent = $totals['acquired_value'] != 0 ? $gainLoss / $totals['acquired_value'] : $gainLossPercent;

        $totals['gain_loss']         = $gainLoss;
        $totals['gain_loss_percent'] = $gainLossPercent;

        return $totals;
    }
}
