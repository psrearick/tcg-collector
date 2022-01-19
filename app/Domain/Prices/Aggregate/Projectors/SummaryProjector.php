<?php

namespace App\Domain\Prices\Aggregate\Projectors;

use App\Domain\Collections\Aggregate\Events\CollectionCardUpdated;
use App\Domain\Collections\Models\Collection;
use App\Domain\Folders\Models\Folder;
use App\Domain\Prices\Aggregate\Actions\GetCollectionTotals;
use App\Domain\Prices\Aggregate\Actions\GetFolderTotals;
use App\Domain\Prices\Models\Summary;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class SummaryProjector extends Projector
{
    public function onCollectionCardUpdated(CollectionCardUpdated $collectionCardUpdated) : void
    {
        $attributes = $collectionCardUpdated->collectionCardAttributes;

        $this->updateFolderSummaries($attributes);
        $this->updateCollectionSummary($attributes);
    }

    private function calculateTotals(array $totals, array $attributes) : array
    {
        // get attribute values
        $price          = $attributes['updated']['price'];
        $acquired       = $attributes['updated']['acquired_price'];
        // the change in quantity
        $quantity       = $attributes['quantity_diff'];
        // the result quantity
        $endQuantity    = $attributes['updated']['quantity'];
        // the quantity before the change
        $startQuantity  = $endQuantity - $quantity;
        // the price of the change
        $valueChange    = $quantity * $price;

        // if a from price is set, use that is the previous price
        $previousPrice = $price;
        if (isset($attributes['change']['from'])) {
            $previousPrice = $attributes['change']['from']['price'] ?? $price;
        }

        // calculate to value change using acquired value
        $previousValue = $startQuantity * $previousPrice;
        $updatedValue  = $endQuantity * ($acquired ?: $price);
        $valueDiff     = $updatedValue - $previousValue;
        $priceChange   = $price - $previousPrice;

        // get totals
        $totalCards      = $totals['total_cards'] + $quantity;
        $currentValue    = $priceChange <> 0 ? $price : ($totals['current_value'] + $valueChange);
        $acquiredValue   = $totals['acquired_value'] + $valueDiff;

        // calculate gain/loss
        $gainLoss        = $currentValue - $acquiredValue;
        $gainLossPercent = $gainLoss == 0 ? 0 : 1;
        $gainLossPercent = $acquiredValue != 0 ? $gainLoss / $acquiredValue : $gainLossPercent;

        return [
            'total_cards'       => $totalCards,
            'current_value'     => $currentValue,
            'acquired_value'    => $acquiredValue,
            'gain_loss'         => $gainLoss,
            'gain_loss_percent' => $gainLossPercent,
        ];
    }

    private function updateCollectionSummary(array $attributes) : void
    {
        $collection = Collection::uuid($attributes['uuid']);

        // get summary or data for a new one
        $totals = optional($collection->summary)->toArray();
        if (!$totals) {
            $getCollectionTotals = new GetCollectionTotals;
            $totals              = $getCollectionTotals($collection);
        }

        $summary    = Summary::updateOrCreate([
            'uuid'              => $collection->uuid,
            'type'              => 'collection',
        ], $this->calculateTotals($totals, $attributes));
        $collection->summary()->associate($summary);
        $collection->save();
    }

    private function updateFolderSummaries(array $attributes) : void
    {
        $collection = Collection::uuid($attributes['uuid']);

        if (!$folder = $collection->folder) {
            return;
        }

        $this->updateFolderSummary($folder, $attributes);
    }

    private function updateFolderSummary(Folder $folder, array $attributes) : void
    {
        if ($parent = $folder->parent) {
            // set parent values before this folder
            $this->updateFolderSummary($parent, $attributes);
        }

        // get summary or data for a new one
        $totals = optional($folder->summary)->toArray();
        if (!$totals) {
            $getFolderTotals = new GetFolderTotals;
            $totals          = $getFolderTotals($folder);
        }

        $folder->summary()->updateOrCreate([
            'uuid'          => $folder->uuid,
            'type'          => 'folder',
        ], $this->calculateTotals($totals, $attributes));
    }
}
