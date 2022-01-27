<?php

namespace App\Domain\Prices\Aggregate\Actions;

use App\App\Scopes\UserScope;
use App\App\Scopes\UserScopeNotShared;
use App\Domain\Folders\Models\Folder;

class GetFolderTotalsWithoutUpdate
{
    public function __invoke(Folder $folder) : array
    {
        $totals = [
            'total_cards'       => 0,
            'current_value'     => 0,
            'acquired_value'    => 0,
        ];

        $folder->collections()
        ->withoutGlobalScopes([UserScopeNotShared::class, UserScope::class])
        ->get()
        ->each(function ($collection) use (&$totals) {
            $collectionTotals = optional($collection->summary)->toArray();
            $totals['total_cards'] += $collectionTotals['total_cards'];
            $totals['current_value'] += $collectionTotals['current_value'];
            $totals['acquired_value'] += $collectionTotals['acquired_value'];
        });

        $folder->children->each(function ($descenant) use (&$totals) {
            $descentantTotals = optional($descenant->summary)->toArray();

            if (!$descentantTotals) {
                return;
            }

            $totals['total_cards'] += $descentantTotals['total_cards'];
            $totals['current_value'] += $descentantTotals['current_value'];
            $totals['acquired_value'] += $descentantTotals['acquired_value'];
        });

        $gainLoss        = $totals['current_value'] - $totals['acquired_value'];
        $gainLossPercent = $gainLoss == 0 ? 0 : 1;
        $gainLossPercent = $totals['acquired_value'] != 0 ? $gainLoss / $totals['acquired_value'] : $gainLossPercent;

        $totals['gain_loss']         = $gainLoss;
        $totals['gain_loss_percent'] = $gainLossPercent;

        return $totals;
    }
}
