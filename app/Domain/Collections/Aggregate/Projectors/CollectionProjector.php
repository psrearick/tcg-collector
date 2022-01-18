<?php

namespace App\Domain\Collections\Aggregate\Projectors;

use App\Domain\Collections\Aggregate\Events\CollectionCreated;
use App\Domain\Collections\Aggregate\Events\CollectionDeleted;
use App\Domain\Collections\Aggregate\Events\CollectionMoved;
use App\Domain\Collections\Aggregate\Events\CollectionUpdated;
use App\Domain\Collections\Models\Collection;
use App\Domain\Folders\Models\Folder;
use App\Domain\Prices\Aggregate\Actions\UpdateFolderAncestryTotals;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class CollectionProjector extends Projector
{
    public function onCollectionCreated(CollectionCreated $event) : void
    {
        $attributes = $event->collectionAttributes;
        $collection = Collection::create([
            'uuid'          => $attributes['uuid'],
            'name'          => $attributes['name'],
            'description'   => $attributes['description'],
            'is_public'     => $attributes['is_public'],
            'user_id'       => $attributes['user_id'],
            'folder_uuid'   => $attributes['folder_uuid'],
        ]);

        if ($attributes['groups']) {
            $collection->groups()->sync($attributes['groups']);
        }
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
        $collection = Collection::uuid($attributes['uuid']);
        $collection->update([
            'name'          => $attributes['name'],
            'description'   => $attributes['description'],
            'is_public'     => $attributes['is_public'],
        ]);

        if ($attributes['groups']) {
            $collection->groups()->sync($attributes['groups']);
        }
    }
}
