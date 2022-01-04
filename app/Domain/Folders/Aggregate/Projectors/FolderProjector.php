<?php

namespace App\Domain\Folders\Aggregate\Projectors;

use App\Domain\Collections\Aggregate\Actions\DeleteCollection;
use App\Domain\Folders\Aggregate\Actions\DeleteFolder;
use App\Domain\Folders\Aggregate\Events\FolderCreated;
use App\Domain\Folders\Aggregate\Events\FolderDeleted;
use App\Domain\Folders\Aggregate\Events\FolderMoved;
use App\Domain\Folders\Aggregate\Events\FolderUpdated;
use App\Domain\Folders\Models\Folder;
use App\Domain\Prices\Aggregate\Actions\UpdateFolderAncestryTotals;
use Illuminate\Support\Facades\Log;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class FolderProjector extends Projector
{
    public function onFolderCreated(FolderCreated $event) : void
    {
        $attributes = $event->folderAttributes;
        $parent     = $attributes['parent_uuid'];

        $folder = Folder::create([
            'uuid'          => $attributes['uuid'],
            'name'          => $attributes['name'],
            'description'   => $attributes['description'],
            'user_id'       => $attributes['user_id'],
            'is_public'     => $attributes['is_public'],
            'parent_uuid'   => $attributes['parent_uuid'],
        ]);

        if ($parent) {
            $folder->appendToNode(Folder::uuid($parent))->save();
        }
    }

    public function onFolderDeleted(FolderDeleted $event) : void
    {
        $folder       = Folder::uuid($event->aggregateRootUuid());
        $descendants  = $folder->descendants;
        $collections  = $folder->collections;
        $folderParent = $folder->parent;

        foreach ($descendants as $descendant) {
            (new DeleteFolder)($descendant->uuid);
        }

        foreach ($collections as $collection) {
            (new DeleteCollection)($collection->uuid);
        }

        $folder->delete();

        if ($folderParent) {
            (new UpdateFolderAncestryTotals)($folderParent);
        }
    }

    public function onFolderMoved(FolderMoved $folderMoved) : void
    {
        $folder         = Folder::uuid($folderMoved->uuid);
        $originalParent = $folder->parent;
        $newParentUuid  = $folderMoved->destination;

        if ($newParentUuid == '') {
            $folder->parent_uuid = '';
            $folder->makeRoot()->save();
        }

        if ($newParentUuid) {
            $newParent           = Folder::uuid($newParentUuid);
            $folder->parent_uuid = $newParentUuid;
            $folder->appendToNode($newParent)->save();
            (new UpdateFolderAncestryTotals)($newParent);
        }

        if ($originalParent) {
            (new UpdateFolderAncestryTotals)($originalParent);
        }
    }

    public function onFolderUpdate(FolderUpdated $event) : void
    {
        $attributes = $event->folderAttributes;
        Folder::uuid($attributes['uuid'])->update($attributes);
    }
}
