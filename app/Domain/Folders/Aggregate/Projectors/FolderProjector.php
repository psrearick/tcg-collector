<?php

namespace App\Domain\Folders\Aggregate\Projectors;

use App\Domain\Folders\Aggregate\Events\FolderCreated;
use App\Domain\Folders\Aggregate\Events\FolderDeleted;
use App\Domain\Folders\Aggregate\Events\FolderMoved;
use App\Domain\Folders\Aggregate\Events\FolderUpdated;
use App\Domain\Folders\Models\Folder;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class FolderProjector extends Projector
{
    public function onFolderCreated(FolderCreated $event) : void
    {
        $attributes = $event->folderAttributes;
        $parent     = $attributes['parent_uuid'];

        $folder = Folder::create($attributes);

        if ($parent) {
            $folder->appendToNode(Folder::uuid($parent))->save();
        }
    }

    public function onFolderDeleted(FolderDeleted $event) : void
    {
        Folder::uuid($event->uuid)->delete();
    }

    public function onFolderMoved(FolderMoved $folderMoved) : void
    {
        $folder = Folder::uuid($folderMoved->uuid);
        if ($folderMoved->destination == '') {
            $folder->parent_uuid = '';
            $folder->makeRoot()->save();

            return;
        }
        $folder->parent_uuid = $folderMoved->destination;
        $folder->appendToNode(Folder::uuid($folderMoved->destination))->save();
    }

    public function onFolderUpdate(FolderUpdated $event) : void
    {
        $attributes = $event->folderAttributes;
        $folder     = Folder::uuid($attributes['uuid']);
        $parent     = $attributes['parent_uuid'] ?? null;
        unset($attributes['parent_uuid']);
        $folder->update($attributes);

        if ($parent) {
            $folder->appendToNode(Folder::uuid($parent));
        }
    }
}
