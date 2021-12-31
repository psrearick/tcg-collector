<?php

namespace App\Domain\Prices\Aggregate\Projectors;

use App\Domain\Collections\Aggregate\Events\CollectionCardUpdated;
use App\Domain\Collections\Models\Collection;
use App\Domain\Folders\Models\Folder;
use App\Domain\Prices\Aggregate\Actions\GetCollectionTotals;
use App\Domain\Prices\Aggregate\Actions\GetFolderTotals;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class SummaryProjector extends Projector
{
    public function onCollectionCardUpdated(CollectionCardUpdated $collectionCardUpdated)
    {
        $attributes         = $collectionCardUpdated->collectionCardAttributes;
        $collection         = Collection::uuid($attributes['uuid']);

        $collectionTotals = optional($collection->summary)->toArray();
        if (!$collectionTotals) {
            $getCollectionTotals = new GetCollectionTotals;
            $collectionTotals    = $getCollectionTotals($collection);
        }

        $quantity   = $attributes['quantity_diff'];
        $price      = $attributes['updated']['price'];
        $change     = $quantity * $price;

        $totalCards     = $collectionTotals['total_cards'] + $quantity;
        $currentValue   = $collectionTotals['current_value'] + $change;
        $acquiredValue  = $collectionTotals['acquired_value'] + $change;

        $gainLoss        = $currentValue - $acquiredValue;
        $gainLossPercent = $gainLoss == 0 ? 0 : 1;
        $gainLossPercent = $acquiredValue != 0 ? $gainLoss / $acquiredValue : $gainLossPercent;

        $parentUuid = $collection->folder_uuid;
        if ($parentUuid) {
            $parent = Folder::uuid($parentUuid);

            $ancestors = $parent->ancestors;
            $ancestors->each(function ($ancestor) use ($quantity, $change) {
                $this->updateFolderWithCollectionCard($ancestor, $quantity, $change);
            });

            $this->updateFolderWithCollectionCard($parent, $quantity, $change);
        }

        $collection->summary()->updateOrCreate([
            'uuid'              => $collection->uuid,
        ], [
            'type'              => 'collection',
            'total_cards'       => $totalCards,
            'current_value'     => $currentValue,
            'acquired_value'    => $acquiredValue,
            'gain_loss'         => $gainLoss,
            'gain_loss_percent' => $gainLossPercent,
        ]);
    }

    protected function updateFolderWithCollectionCard(Folder $folder, int $quantity, float $change)
    {
        $folderTotals = optional($folder->summary)->toArray();
        if (!$folderTotals) {
            $getFolderTotals = new GetFolderTotals;
            $folderTotals    = $getFolderTotals($folder);
        }

        $folderTotalCards     = $folderTotals['total_cards'] + $quantity;
        $folderCurrentValue   = $folderTotals['current_value'] + $change;
        $folderAcquiredValue  = $folderTotals['acquired_value'] + $change;

        $folderGainLoss        = $folderCurrentValue - $folderAcquiredValue;
        $folderGainLossPercent = $folderGainLoss == 0 ? 0 : 1;
        $folderGainLossPercent = $folderAcquiredValue != 0 ? $folderGainLoss / $folderAcquiredValue : $folderGainLossPercent;

        $f = $folder->summary()->updateOrCreate([
            'uuid'              => $folder->uuid,
        ], [
            'type'              => 'folder',
            'total_cards'       => $folderTotalCards,
            'current_value'     => $folderCurrentValue,
            'acquired_value'    => $folderAcquiredValue,
            'gain_loss'         => $folderGainLoss,
            'gain_loss_percent' => $folderGainLossPercent,
        ]);
    }
}
