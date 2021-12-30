<?php

namespace App\Domain\Collections\Aggregate\Projectors;

use App\Domain\Collections\Aggregate\Events\CollectionCreated;
use App\Domain\Collections\Aggregate\Events\CollectionDeleted;
use App\Domain\Collections\Aggregate\Events\CollectionMoved;
use App\Domain\Collections\Models\Collection;
use Illuminate\Support\Facades\Log;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class CollectionProjector extends Projector
{
    public function onCollectionCreated(CollectionCreated $event)
    {
        Collection::create($event->collectionAttributes);
    }

    public function onCollectionDeleted(CollectionDeleted $event)
    {
        Collection::uuid($event->collectionUuid)->delete();
    }

    public function onCollectionMoved(CollectionMoved $event)
    {
        Collection::uuid($event->uuid)->update([
            'folder_uuid' => $event->destination,
        ]);
    }
}
