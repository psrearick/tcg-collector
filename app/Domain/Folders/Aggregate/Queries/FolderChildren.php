<?php

namespace App\Domain\Folders\Aggregate\Queries;

use App\Domain\Collections\Aggregate\Events\CollectionCreated;
use App\Domain\Folders\Aggregate\Events\FolderCreated;
use App\Domain\Folders\Aggregate\Events\FolderUpdated;
use App\Domain\Folders\Models\Folder;
use GetCollection;
use GetFolder;
use Spatie\EventSourcing\EventHandlers\Projectors\EventQuery;
use Spatie\EventSourcing\StoredEvents\Models\EloquentStoredEvent;

class FolderChildren extends EventQuery
{
    private array $folders = [];
    private array $collections = [];

    public function __construct(private ?string $folder)
    {
        EloquentStoredEvent::query()
            ->where('event_class', '=', FolderCreated::class)
            ->orWhere('event_class', '=', FolderUpdated::class)
            ->orWhere('event_class', '=', CollectionCreated::class)
            ->cursor()
            ->each(fn (EloquentStoredEvent $event) => $this->apply($event->toStoredEvent()));
    }

    public function folders() : array
    {
        $folders = collect(array_keys($this->folders))->map(function ($uuid) {
            return (new GetFolder())($uuid);
        })->toArray();
        return $folders;
    }

    public function collections() : array
    {
        return array_map(function ($uuid) {
            return (new GetCollection())($uuid);
        }, array_keys($this->collections));
    }

    protected function applyCollectionCreated(CollectionCreated $collectionCreated) : void
    {
        $attributes = $collectionCreated->collectionAttributes;
        $parent = $attributes['folder_uuid'] ?? null;
        $collectionUuid = $attributes['uuid'];

        if ($parent == $this->folder) {
            $this->collections[$collectionUuid] = $attributes;
        }
    }

    protected function applyFolderCreated(FolderCreated $folderCreated) : void
    {
        $attributes = $folderCreated->folderAttributes;
        $parent = $attributes['parent_uuid'] ?? null;
        $folderUuid = $attributes['uuid'];

        if ($parent == $this->folder) {
            $this->folders[$folderUuid] = $attributes;
        }
    }

    protected function applyFolderUpdated(FolderUpdated $folderUpdated) : void
    {
        $attributes = $folderUpdated->folderAttributes;
        $parent = $attributes['parent_uuid'] ?? null;
        $folderUuid = $attributes['uuid'];

        if ($parent == $this->folder) {
            $this->folders[$folderUuid] = $attributes;
        } else {
            if (array_key_exists($folderUuid, $this->folders)) {
                unset($this->folders[$folderUuid]);
            }
        }
    }
}