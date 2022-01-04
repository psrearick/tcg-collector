<?php

namespace App\Domain\Collections\Aggregate\Projectors;

use App\Domain\Collections\Aggregate\Events\CollectionCardUpdated;
use App\Domain\Collections\Aggregate\Events\CollectionCreated;
use App\Domain\Collections\Aggregate\Events\CollectionDeleted;
use App\Domain\Collections\Aggregate\Events\CollectionMoved;
use App\Domain\Collections\Aggregate\Events\CollectionUpdated;
use App\Domain\Collections\Models\Collection;
use App\Domain\Collections\Models\CollectionCardSummary;
use App\Domain\Folders\Models\Folder;
use App\Domain\Prices\Aggregate\Actions\GetFolderTotals;
use App\Domain\Prices\Aggregate\Actions\UpdateFolderAncestryTotals;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class CollectionProjector extends Projector
{
    public function onCollectionCardUpdated(CollectionCardUpdated $event) : void
    {
        $attributes = $event->collectionCardAttributes;
        $this->updateCollectionCard($attributes);
        $this->updateCollectionCardSummary($attributes);
        Cache::restoreLock('saving-collection-card', $attributes['lock'])->release();
    }

    public function onCollectionCreated(CollectionCreated $event) : void
    {
        Collection::create($event->collectionAttributes);
    }

    public function onCollectionDeleted(CollectionDeleted $event) : void
    {
        $collection = Collection::uuid($event->aggregateRootUuid());
        $folderUuid = $collection->folder_uuid;
        $collection->delete();

        if ($folderUuid) {
            $folder = Folder::uuid($folderUuid);
            if ($folder) {
                (new UpdateFolderAncestryTotals)($folder);
            }
        }
    }

    public function onCollectionMoved(CollectionMoved $event) : void
    {
        $collection         = Collection::uuid($event->uuid);
        $destinationUuid    = $event->destination;
        $originalParentUuid = $collection->folder_uuid;

        $collection->update([
            'folder_uuid' => $destinationUuid,
        ]);

        if ($destinationUuid) {
            (new UpdateFolderAncestryTotals)(Folder::uuid($destinationUuid));
        }

        if ($originalParentUuid) {
            (new UpdateFolderAncestryTotals)(Folder::uuid($originalParentUuid));
        }
    }

    public function onCollectionUpdated(CollectionUpdated $event) : void
    {
        $attributes = $event->collectionAttributes;
        Collection::uuid($attributes['uuid'])->update($attributes);
    }

    private function updateCollectionCard(array $attributes) : void
    {
        Collection::uuid($attributes['uuid'])->cards()
            ->attach($attributes['updated']['id'], [
                'collection_uuid'  => $attributes['uuid'],
                'card_uuid'        => $attributes['updated']['uuid'],
                'price_when_added' => $attributes['updated']['acquired_price'],
                'quantity'         => $attributes['quantity_diff'],
                'finish'           => $attributes['updated']['finish'],
                'date_added'       => Carbon::now(),
            ]);
    }

    private function updateCollectionCardSummary(array $attributes) : void
    {
        $cardUuid   = $attributes['updated']['uuid'];
        $finish     = $attributes['updated']['finish'];
        $change     = $attributes['quantity_diff'];
        $price      = $attributes['updated']['acquired_price'];

        $existingCard = CollectionCardSummary::where('collection_uuid', '=', $attributes['uuid'])
            ->where('card_uuid', '=', $cardUuid)
            ->where('finish', '=', $finish)
            ->first();

        if (!$existingCard) {
            CollectionCardSummary::create([
                'collection_uuid'       => $attributes['uuid'],
                'card_uuid'             => $cardUuid,
                'price_when_added'      => $price,
                'price_when_updated'    => $price,
                'current_price'         => $price,
                'quantity'              => $change,
                'finish'                => $finish,
                'date_added'            => Carbon::now(),
            ]);

            return;
        }

        $existingCard->update([
            'price_when_updated'    => $price,
            'current_price'         => $price,
            'quantity'              => $existingCard->quantity + $change,
        ]);
    }
}
