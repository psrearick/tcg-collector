<?php

namespace App\Domain\Prices\Aggregate\Actions;

use App\Actions\GetGainLossValues;
use App\Domain\Folders\Models\Folder;

class GetFolderTotals
{
    private GetGainLossValues $getGainLossValues;

    public function __construct()
    {
        $this->getGainLossValues = new GetGainLossValues();
    }

    public function __invoke(Folder $folder, bool $forceUpdate = false) : array
    {
        $totals = [
            'total_cards'       => 0,
            'current_value'     => 0,
            'acquired_value'    => 0,
        ];

        $folder->baseCollections()->get()->each(function ($collection) use (&$totals, $forceUpdate) {
            $collectionTotals = optional($collection->summary)->toArray();
            if ($forceUpdate || !$collectionTotals) {
                $getCollectionTotals = new GetCollectionTotals;
                $collectionTotals = $getCollectionTotals($collection);
            }

            $totals['total_cards'] += $collectionTotals['total_cards'];
            $totals['current_value'] += $collectionTotals['current_value'];
            $totals['acquired_value'] += $collectionTotals['acquired_value'];
        });

        $folder->children->each(function ($descendant) use (&$totals, $forceUpdate) {
            $descendantTotals = optional($descendant->summary)->toArray();
            if ($forceUpdate || !$descendantTotals) {
                $getFolderTotals = new GetFolderTotals;
                $descendantTotals = $getFolderTotals($descendant);
            }

            $totals['total_cards'] += $descendantTotals['total_cards'];
            $totals['current_value'] += $descendantTotals['current_value'];
            $totals['acquired_value'] += $descendantTotals['acquired_value'];
        });

        $gainLoss = $this->getGainLossValues->handle($totals['current_value'], $totals['acquired_value']);

        $totals['gain_loss']         = $gainLoss['gain_loss'];
        $totals['gain_loss_percent'] = $gainLoss['gain_loss_percent'];

        return $totals;
    }
}
