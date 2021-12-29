<?php

namespace App\Domain\Folders\Aggregate\Projectors;

use App\Domain\Folders\Aggregate\Events\FolderCreated;
use App\Domain\Folders\Models\Folder;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;
use App\Domain\Folders\Aggregate\Events\FolderDeleted;
use Illuminate\Support\Facades\Log;

class FolderProjector extends Projector
{
    public function onFolderCreated(FolderCreated $event)
    {
        $attributes = $event->folderAttributes;
        $parent = $attributes['parent_uuid'];
        unset($attributes['parent_uuid']);

        $folder = Folder::create($attributes);

        if ($parent) {
            $folder->appendToNode(Folder::where('uuid', '=', $parent)->first());
        }
    }

    public function onFolderDeleted(FolderDeleted $event)
    {
        Folder::uuid($event->folderUuid)->delete();
    }
}