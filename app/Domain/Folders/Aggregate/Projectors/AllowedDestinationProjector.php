<?php

namespace App\Domain\Folders\Aggregate\Projectors;

use App\Domain\Collections\Aggregate\Events\CollectionCreated;
use App\Domain\Collections\Aggregate\Events\CollectionMoved;
use App\Domain\Folders\Aggregate\Events\FolderCreated;
use App\Domain\Folders\Aggregate\Events\FolderMoved;
use App\Domain\Folders\Aggregate\Services\AllowedDestinationService;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class AllowedDestinationProjector extends Projector
{
    private AllowedDestinationService $service;

    public function __construct()
    {
        $this->service = new AllowedDestinationService;
    }

    public function onCollectionCreated(CollectionCreated $event) : void
    {
        $attributes = $event->collectionAttributes;
        $uuid       = $attributes['uuid'];
        $folder     = $attributes['folder_uuid'];
        $userId     = $attributes['user_id'];

        $this->service->setAllowedCollectionDestinations($uuid, $folder, $userId);
    }

    public function onCollectionMoved(CollectionMoved $event)
    {
        $uuid     = $event->uuid;
        $folder   = $event->destination;
        $userId   = $event->user_id;

        $this->service->setAllowedCollectionDestinations($uuid, $folder, $userId);
    }

    public function onFolderCreated(FolderCreated $event)
    {
        $attributes     = $event->folderAttributes;
        $uuid           = $attributes['uuid'];
        $userId         = $attributes['user_id'];
        $parent         = $attributes['parent_uuid'] ?? '';

        $this->service->setAllowedFolderForCollections($uuid, $userId);
        $this->service->setAllowedFolderForFolders($uuid, $parent, $userId);
        $this->service->setAllowedFoldersForFolder($uuid, $parent, $userId);
    }

    public function onFolderMoved(FolderMoved $event)
    {
        $parent = $event->destination;
        $uuid   = $event->uuid;
        $userId = $event->user_id;

        $this->service->setAllowedFoldersForFolder($uuid, $parent, $userId);
        $this->service->setAllowedFolderForFolders($uuid, $parent, $userId);
    }
}
