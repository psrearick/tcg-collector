<?php

namespace App\Domain\Folders\Aggregate\Projectors;

use App\Domain\Folders\Aggregate\Events\FolderCreated;
use App\Domain\Folders\Models\Folder;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;
use App\Domain\Folders\Aggregate\Events\FolderDeleted;
use App\Domain\Folders\Aggregate\Events\FolderUpdated;
use Illuminate\Support\Facades\Log;

class FolderProjector extends Projector
{
    public function onFolderCreated(FolderCreated $event)
    {
        $attributes = $event->folderAttributes;
        $parent = $attributes['parent_uuid'];

        $folder = Folder::create($attributes);

        if ($parent) {
            $folder->appendToNode(Folder::where('uuid', '=', $parent)->first())->save();
        }
    }

    public function onFolderDeleted(FolderDeleted $event)
    {
        Folder::uuid($event->uuid)->delete();
    }

    public function onFolderUpdate(FolderUpdated $event)
    {
        $attributes = $event->folderAttributes;
        $folder = Folder::uuid($attributes['uuid']);
        $parent = $attributes['parent_uuid'] ?? null;
        unset($attributes['parent_uuid']);
        $folder->update($attributes);

        if ($parent) {
            $folder->appendToNode(Folder::where('uuid', '=', $parent)->first());
        }
    }
}