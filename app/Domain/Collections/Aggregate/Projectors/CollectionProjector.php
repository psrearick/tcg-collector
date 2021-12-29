<?php

namespace App\Domain\Collections\Aggregate\Projectors;

use Spatie\EventSourcing\EventHandlers\Projectors\Projector;
use App\Domain\Collections\Aggregate\Events\CollectionCreated;
use App\Domain\Collections\Models\Collection;
use App\Domain\Collections\Aggregate\Events\CollectionDeleted;

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
}