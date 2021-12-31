<?php

namespace App\Domain\Prices\Aggregate\Actions;

use App\Domain\Prices\Models\Summary;

class GetSummaryData
{
    public function __invoke(?array $collections = null, ?array $folders = null)
    {
        $totals = [
            'total_cards'       => 0,
            'current_value'     => 0,
            'acquired_value'    => 0,
        ];

        if ($collections) {
            foreach ($collections as $collection) {
                $collectionSummary = Summary::where('uuid', '=', $collection['uuid'])
                    ->where('type', '=', 'collection')
                    ->first();
                if ($collectionSummary) {
                    $totals['total_cards'] += $collectionSummary->total_cards;
                    $totals['current_value'] += $collectionSummary->current_value;
                    $totals['acquired_value'] += $collectionSummary->acquired_value;
                }
            }
        }

        if ($folders) {
            foreach ($folders as $folder) {
                $folderSummary = Summary::where('uuid', '=', $folder['uuid'])
                    ->where('type', '=', 'folder')
                    ->first();
                if ($folderSummary) {
                    $totals['total_cards'] += $folderSummary->total_cards;
                    $totals['current_value'] += $folderSummary->current_value;
                    $totals['acquired_value'] += $folderSummary->acquired_value;
                }
            }
        }

        $gainLoss        = $totals['current_value'] - $totals['acquired_value'];
        $gainLossPercent = $gainLoss == 0 ? 0 : 1;
        $gainLossPercent = $totals['acquired_value'] != 0 ? $gainLoss / $totals['acquired_value'] : $gainLossPercent;

        $totals['gain_loss']         = $gainLoss;
        $totals['gain_loss_percent'] = $gainLossPercent;

        return $totals;
    }
}
