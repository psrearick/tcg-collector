<?php

namespace App\Domain\Collections\Aggregate\Projectors;

use App\Domain\Collections\Aggregate\Events\CollectionCardUpdated;
use App\Domain\Collections\Aggregate\Events\CollectionCreated;
use App\Domain\Collections\Aggregate\Events\CollectionDeleted;
use App\Domain\Collections\Aggregate\Events\CollectionMoved;
use App\Domain\Collections\Aggregate\Events\CollectionUpdated;
use App\Domain\Collections\Models\Collection;
use Carbon\Carbon;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class CollectionProjector extends Projector
{
    public function onCollectionCardUpdated(CollectionCardUpdated $event) : void
    {
        $attributes = $event->collectionCardAttributes;
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

    public function onCollectionCreated(CollectionCreated $event) : void
    {
        Collection::create($event->collectionAttributes);
    }

    public function onCollectionDeleted(CollectionDeleted $event) : void
    {
        Collection::uuid($event->collectionUuid)->delete();
    }

    public function onCollectionMoved(CollectionMoved $event) : void
    {
        Collection::uuid($event->uuid)->update([
            'folder_uuid' => $event->destination,
        ]);
    }

    public function onCollectionUpdated(CollectionUpdated $event) : void
    {
        $attributes = $event->collectionAttributes;
        Collection::uuid($attributes['uuid'])->update($attributes);
    }
}
