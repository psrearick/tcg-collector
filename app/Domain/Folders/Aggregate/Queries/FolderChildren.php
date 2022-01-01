<?php

namespace App\Domain\Folders\Aggregate\Queries;

use App\Domain\Collections\Aggregate\DataObjects\CollectionData;
use App\Domain\Collections\Aggregate\Events\CollectionCreated;
use App\Domain\Collections\Aggregate\Events\CollectionMoved;
use App\Domain\Collections\Models\Collection;
use App\Domain\Folders\Aggregate\DataObjects\FolderData;
use App\Domain\Folders\Aggregate\Events\FolderCreated;
use App\Domain\Folders\Aggregate\Events\FolderMoved;
use App\Domain\Folders\Models\AllowedDestination;
use App\Domain\Folders\Models\Folder;
use App\Domain\Prices\Aggregate\Actions\GetSummaryData;
use Spatie\EventSourcing\EventHandlers\Projectors\EventQuery;
use Spatie\EventSourcing\StoredEvents\Models\EloquentStoredEvent;

class FolderChildren extends EventQuery
{
    private array $collections = [];

    private array $folders = [];

    public function __construct(private ?string $folder, private ?int $user_id)
    {
        EloquentStoredEvent::query()
            ->whereIn('event_class', [
                FolderCreated::class,
                FolderMoved::class,
                CollectionCreated::class,
                CollectionMoved::class,
            ])
            ->cursor()
            ->each(fn (EloquentStoredEvent $event) => $this->apply($event->toStoredEvent()));
    }

    public function collections() : array
    {
        return array_map(function ($uuid) {
            $collection                 = Collection::uuid($uuid);
            $collectionData             = (new CollectionData($collection->toArray()))->toArray();
            $collectionData['allowed']  = $this->formatAllowed($uuid, 'collection', empty($collection->folder_uuid));
            $collectionSummary = (new GetSummaryData)([$collectionData]);
            $collectionData['count']    = $collectionSummary['total_cards'];
            $collectionData['value']    = $collectionSummary['current_value'];

            return $collectionData;
        }, array_keys($this->collections));
    }

    public function folders() : array
    {
        $folders = collect(array_keys($this->folders))->map(function ($uuid) {
            $folder                 = Folder::uuid($uuid);
            $folderData             = (new FolderData($folder->toArray()))->toArray();

            $folderData['allowed']  = $this->formatAllowed($uuid, 'folder', $folder->isRoot());
            $folderSummary = (new GetSummaryData)(null, [$folderData]);
            $folderData['count']    = $folderSummary['total_cards'];
            $folderData['value']    = $folderSummary['current_value'];

            return $folderData;
        })->toArray();

        return $folders;
    }

    protected function applyCollectionCreated(CollectionCreated $collectionCreated) : void
    {
        $attributes     = $collectionCreated->collectionAttributes;
        $parent         = $attributes['folder_uuid'] ?? null;
        $collectionUuid = $attributes['uuid'];
        $userId         = $attributes['user_id'];

        if ($parent == $this->folder && $userId == $this->user_id) {
            $this->collections[$collectionUuid] = $attributes;
        }
    }

    protected function applyCollectionMoved(CollectionMoved $collectionMoved) : void
    {
        $attributes = [
            'folder_uuid'   => $collectionMoved->destination,
            'uuid'          => $collectionMoved->uuid,
            'user_id'       => $collectionMoved->user_id,
        ];

        if ($attributes['folder_uuid'] == $this->folder && $attributes['user_id'] == $this->user_id) {
            $this->collections[$attributes['uuid']] = $attributes;
        } else {
            if (array_key_exists($attributes['uuid'], $this->collections)) {
                unset($this->collections[$attributes['uuid']]);
            }
        }
    }

    protected function applyFolderCreated(FolderCreated $folderCreated) : void
    {
        $attributes = $folderCreated->folderAttributes;
        $parent     = $attributes['parent_uuid'] ?? null;
        $folderUuid = $attributes['uuid'];
        $userId     = $attributes['user_id'];

        if ($parent == $this->folder && $userId == $this->user_id) {
            $this->folders[$folderUuid] = $attributes;
        }
    }

    protected function applyFolderMoved(FolderMoved $folderMoved) : void
    {
        $attributes = [
            'parent_uuid'   => $folderMoved->destination,
            'uuid'          => $folderMoved->uuid,
            'user_id'       => $folderMoved->user_id,
        ];

        if ($attributes['parent_uuid'] == $this->folder && $attributes['user_id'] == $this->user_id) {
            $this->folders[$attributes['uuid']] = $attributes;
        } else {
            if (array_key_exists($attributes['uuid'], $this->folders)) {
                unset($this->folders[$attributes['uuid']]);
            }
        }
    }

    protected function formatAllowed(string $uuid, string $type, bool $isRoot) : array
    {
        $allowed = AllowedDestination::where('type', '=', $type)->where('uuid', '=', $uuid)->whereNotNull('destination')->pluck('destination')->map(
            function ($destination) {
                $folder             = Folder::uuid($destination);
                $data               = (new FolderData($folder->toArray()))->toArray();
                $data['ancestry']   = $folder->ancestors->count()
                ? implode(' > ', $folder->ancestors->pluck('name')->toArray())
                : '';
                $data['path']     = ($data['ancestry'] ? $data['ancestry'] . ' > ' : '') . $data['name'];

                return $data;
            }
        )->sortBy('path')->values()->toArray();
        if (!$isRoot) {
            $root = (new FolderData([
                'uuid'      => '',
                'name'      => 'Root',
            ]))->toArray();
            $root['path'] = 'Root';
            array_unshift($allowed, $root);
        }

        return $allowed;
    }
}
