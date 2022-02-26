<?php

namespace App\Domain\Prices\Aggregate\Actions\Summaries;

use App\Actions\GetGainLossValues;
use App\Domain\Folders\Models\Folder;

class CalculateFolderTotals
{
    private GetCollectionSummaries $collectionSummaries;

    private GetGainLossValues $getGainLossValues;

    public function __construct(GetGainLossValues $getGainLossValues, GetCollectionSummaries $collectionSummaries)
    {
        $this->getGainLossValues   = $getGainLossValues;
        $this->collectionSummaries = $collectionSummaries;
    }

    public function execute(Folder $folder) : array
    {
        $collectionsTotal = collect($this->collectionSummaries->execute($folder))
            ->reduce(static function (array $carry, array $collection) {
                $carry['total_cards'] += $collection['total_cards'];
                $carry['current_value'] += $collection['current_value'];
                $carry['acquired_value'] += $collection['acquired_value'];

                return $carry;
            }, $this->base());

        $totals = $folder->children->reduce(function (array $carry, Folder $descendant) {
            $descendantTotals = $descendant->summary ?: $this->base();
            $carry['total_cards'] += $descendantTotals['total_cards'];
            $carry['current_value'] += $descendantTotals['current_value'];
            $carry['acquired_value'] += $descendantTotals['acquired_value'];

            return $carry;
        }, $collectionsTotal);

        $gainLoss = $this->getGainLossValues->execute($totals['current_value'], $totals['acquired_value']);

        $totals['gain_loss']         = $gainLoss['gain_loss'];
        $totals['gain_loss_percent'] = $gainLoss['gain_loss_percent'];

        return $totals;
    }

    private function base() : array
    {
        return [
            'total_cards'       => 0,
            'current_value'     => 0,
            'acquired_value'    => 0,
            'gain_loss'         => 0,
            'gain_loss_percent' => 0,
        ];
    }
}
