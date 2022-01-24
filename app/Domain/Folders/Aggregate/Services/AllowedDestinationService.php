<?php

namespace App\Domain\Folders\Aggregate\Services;

use App\Domain\Collections\Models\Collection;
use App\Domain\Folders\Models\AllowedDestination;
use App\Domain\Folders\Models\Folder;

class AllowedDestinationService
{
    public function allowCollectionDestinations(string $uuid, array $destinations) : void
    {
        collect($destinations)->each(function ($destination) use ($uuid) {
            AllowedDestination::firstOrCreate([
                'type'        => 'collection',
                'uuid'        => $uuid,
                'destination' => $destination,
            ]);
        });
    }

    public function allowFolderForCollections(string $uuid, array $collections) : void
    {
        collect($collections)->each(function ($collection) use ($uuid) {
            AllowedDestination::firstOrCreate([
                'type'          => 'collection',
                'uuid'          => $collection,
                'destination'   => $uuid,
            ]);
        });
    }

    /**
     * @param string $uuid The uuid of the subject folder
     * @param array $folders the folders that can move into this subject
     * @param string $parent The uuid of the target parent folder / destination
     */
    public function allowFolderForFolders(string $uuid, array $folders, string $parent = '') : void
    {
        collect($folders)->each(function ($folder) use ($uuid, $parent) {
            if (!$this->isValidDestinationForFolder($folder, $uuid, null)) {
                return;
            }

            AllowedDestination::firstOrCreate([
                'type'          => 'folder',
                'uuid'          => $folder,
                'destination'   => $uuid,
            ]);
        });
    }

    /**
     * @param string $uuid The uuid of the subject folder
     * @param array $folders the options for the subject to move into
     * @param string $parent The uuid of the target parent folder / destination
     */
    public function allowFoldersForFolder(string $uuid, array $folders, string $parent = '') : void
    {
        collect($folders)->each(function ($folder) use ($uuid, $parent) {
            if (!$this->isValidDestinationForFolder($uuid, $folder, $parent)) {
                return;
            }

            AllowedDestination::firstOrCreate([
                'type'          => 'folder',
                'uuid'          => $uuid,
                'destination'   => $folder,
            ]);
        });
    }

    public function disallowCollectionDestinations(string $uuid, array $destinations) : void
    {
        AllowedDestination::where('uuid', '=', $uuid)
            ->whereIn('destination', $destinations)
            ->delete();
    }

    public function disallowFolderForFolders(string $uuid, array $folders, string $parent = '') : void
    {
        collect($folders)->each(function ($folder) use ($uuid, $parent) {
            if ($this->isValidDestinationForFolder($folder, $uuid, null)) {
                return;
            }

            AllowedDestination::where('type', '=', 'folder')
                ->where('uuid', $folder)
                ->where('destination', '=', $uuid)
                ->delete();
        });
    }

    public function disallowFoldersForFolder(string $uuid, array $folders, string $parent = '') : void
    {
        collect($folders)->each(function ($folder) use ($uuid, $parent) {
            if ($this->isValidDestinationForFolder($uuid, $folder, $parent)) {
                return;
            }

            AllowedDestination::where('type', '=', 'folder')
                ->where('uuid', $uuid)
                ->where('destination', '=', $folder)
                ->delete();
        });
    }

    public function setAllowedCollectionDestinations(string $uuid, ?string $folder = '', ?int $userId = null) : void
    {
        $allowed = $this->getCollectionAllowed($uuid, $folder, $userId);
        $this->allowCollectionDestinations($uuid, $allowed, $userId);

        $disallow = $this->getCollectionDisallowed($allowed);
        $this->disallowCollectionDestinations($uuid, $disallow);
    }

    public function setAllowedFolderForCollections(string $uuid, ?int $userId = null) : void
    {
        $collections = $this->getCollectionsAllowedForFolder($uuid, $userId);
        $this->allowFolderForCollections($uuid, $collections);
    }

    /**
     * @param string $uuid The uuid of the subject folder
     * @param string $parentUuid The uuid of the target parent folder
     */
    public function setAllowedFolderForFolders(string $uuid, ?string $parentUuid = '', ?int $userId = null) : void
    {
        $folders = $this->getFoldersAllowedForFolder($parentUuid, $userId);
        $this->allowFolderForFolders($uuid, $folders, $parentUuid);
        $this->disallowFolderForFolders($uuid, $folders, $parentUuid);
    }

    public function setAllowedFoldersForFolder(string $uuid, string $parentUuid, ?int $userId = null) : void
    {
        $folders = $this->getFoldersAllowedForFolder($parentUuid, $userId);
        $this->allowFoldersForFolder($uuid, $folders, $parentUuid);
        $this->disallowFoldersForFolder($uuid, $folders, $parentUuid);
    }

    protected function getCollectionAllowed(string $uuid, ?string $folder = '', ?int $userId = null) : array
    {
        if (!$userId) {
            $userId = optional(Collection::uuid($uuid))->user_id;
        }

        $destinations = Folder::where('user_id', '=', $userId);

        if ($folder) {
            $destinations->where('uuid', '!=', $folder);
        }

        return $destinations->pluck('uuid')->all();
    }

    protected function getCollectionDisallowed(array $allowed) : array
    {
        return Folder::whereNotIn('uuid', $allowed)->pluck('uuid')->all();
    }

    protected function getCollectionsAllowedForFolder(string $uuid, ?int $userId = null) : array
    {
        if (!$userId) {
            $userId = optional(Folder::uuid($uuid))->user_id;
        }

        return Collection::where('user_id', '=', $userId)->pluck('uuid')->all();
    }

    /**
     * @param string $uuid The uuid of the subject folder
     * @param string $parentUuid The uuid of the target parent folder
     */
    protected function getFoldersAllowedForFolder(string $parentUuid, ?int $userId = null) : array
    {
        if (!$userId) {
            $userId = optional(Folder::uuid($parentUuid))->user_id;
        }

        $parent = $parentUuid ?? null;

        return Folder::where('user_id', '=', $userId)
            ->pluck('uuid')
            ->all();
    }

    protected function isValidDestinationForFolder(string $uuid, string $destination, ?string $parent) : bool
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
