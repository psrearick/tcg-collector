<?php

namespace App\Domain\Folders\Aggregate\Projectors;

use App\Domain\Collections\Aggregate\Events\CollectionCreated;
use App\Domain\Collections\Aggregate\Events\CollectionMoved;
use App\Domain\Collections\Models\Collection;
use App\Domain\Folders\Aggregate\Events\FolderCreated;
use App\Domain\Folders\Aggregate\Events\FolderMoved;
use App\Domain\Folders\Models\AllowedDestination;
use App\Domain\Folders\Models\Folder;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class AllowedDestinationProjector extends Projector
{
    public function onCollectionCreated(CollectionCreated $event)
    {
        $attributes   = $event->collectionAttributes;
        $uuid         = $attributes['uuid'];
        $parent       = $attributes['folder_uuid'];
        $userId       = $attributes['user_id'];
        $destinations = Folder::where('user_id', '=', $userId);
        if ($parent) {
            $destinations->where('uuid', '!=', $parent);
        }
        $destinations->pluck('uuid')->each(function ($destination) use ($uuid) {
            AllowedDestination::firstOrCreate([
                'type'        => 'collection',
                'uuid'        => $uuid,
                'destination' => $destination,
            ]);
        });
    }

    public function onCollectionMoved(CollectionMoved $event)
    {
        $parent       = $event->destination;
        $uuid         = $event->uuid;
        $user_id      = $event->user_id;
        $destinations = Folder::where('user_id', '=', $user_id);
        if ($parent) {
            $destinations->where('uuid', '!=', $parent);
        }
        $allowedDestination = $destinations->pluck('uuid');
        $allowedDestination->each(function ($destination) use ($uuid) {
            AllowedDestination::firstOrCreate([
                'type'        => 'collection',
                'uuid'        => $uuid,
                'destination' => $destination,
            ]);
        });

        AllowedDestination::where('uuid', '=', $uuid)
            ->whereNotIn('destination', $allowedDestination->toArray())
            ->delete();
    }

    public function onFolderCreated(FolderCreated $event)
    {
        $attributes     = $event->folderAttributes;
        $uuid           = $attributes['uuid'];
        $userId         = $attributes['user_id'];

        Collection::where('user_id', '=', $userId)->where('folder_uuid', '!=', $uuid)->get()
            ->each(function ($collection) use ($uuid) {
                AllowedDestination::firstOrCreate([
                    'type'          => 'collection',
                    'uuid'          => $collection->uuid,
                    'destination'   => $uuid,
                ]);
            });

        $parent         = $attributes['parent_uuid'] ?? null;
        $destinations   = Folder::where('user_id', '=', $userId)
            ->where('uuid', '!=', $uuid)->get();

        $destinations->each(function ($destination) use ($uuid, $parent) {
            if ($this->validDestinationForFolder($uuid, $destination->uuid, $parent)) {
                AllowedDestination::firstOrCreate([
                    'type'        => 'folder',
                    'uuid'        => $uuid,
                    'destination' => $destination->uuid,
                ]);
            }

            if ($this->validDestinationForFolder($destination->uuid, $uuid, $parent)) {
                AllowedDestination::firstOrCreate([
                    'type'        => 'folder',
                    'uuid'        => $destination->uuid,
                    'destination' => $uuid,
                ]);
            }
        });
    }

    public function onFolderMoved(FolderMoved $event)
    {
        $parent     = $event->destination;
        $uuid       = $event->uuid;
        $user_id    = $event->user_id;

        $destinations   = Folder::where('user_id', '=', $user_id)
            ->where('uuid', '!=', $uuid)->get();

        $destinations->each(function ($destination) use ($uuid, $parent) {
            if ($this->validDestinationForFolder($uuid, $destination->uuid, $parent)) {
                AllowedDestination::firstOrCreate([
                    'type'        => 'folder',
                    'uuid'        => $uuid,
                    'destination' => $destination->uuid,
                ]);
            }

            if ($this->validDestinationForFolder($destination->uuid, $uuid, $parent)) {
                AllowedDestination::firstOrCreate([
                    'type'        => 'folder',
                    'uuid'        => $destination->uuid,
                    'destination' => $uuid,
                ]);
            }

            if (!$this->validDestinationForFolder($uuid, $destination->uuid, $parent)) {
                AllowedDestination::where('type', '=', 'folder')
                    ->where('uuid', '=', $uuid)
                    ->where('destination', '=', $destination->uuid)
                    ->delete();
            }

            if (!$this->validDestinationForFolder($destination->uuid, $uuid, $parent)) {
                AllowedDestination::where('type', '=', 'folder')
                    ->where('uuid', '=', $destination->uuid)
                    ->where('destination', '=', $uuid)
                    ->delete();
            }
        });
    }

    protected function validDestinationForFolder(string $uuid, string $destination, ?string $parent) : bool
    {
        $current     = Folder::uuid($uuid);
        $descendants = [];
        if ($current) {
            $descendants    = optional($current->descendants)->pluck('uuid')->toArray();
            $parent         = $current->parent_uuid ?? $parent;
        }

        if ($descendants) {
            if (in_array($destination, $descendants)) {
                return false;
            }
        }

        if ($destination == $uuid) {
            return false;
        }

        if ($destination == $parent) {
            return false;
        }

        return true;
    }
}
