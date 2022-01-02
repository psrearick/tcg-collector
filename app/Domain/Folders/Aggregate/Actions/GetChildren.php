<?php

namespace App\Domain\Folders\Aggregate\Actions;

use App\Domain\Collections\Models\Collection;
use App\Domain\Collections\Aggregate\DataObjects\CollectionData;
use App\Domain\Prices\Aggregate\Actions\GetSummaryData;
use App\Domain\Folders\Models\Folder;
use App\Domain\Folders\Aggregate\DataObjects\FolderData;
use Illuminate\Support\Collection as SupportCollection;

class GetChildren
{
    protected ?string $uuid;

    protected int $user_id;

    public function __invoke(?string $uuid = null, ?int $user_id = null)
    {
        $this->uuid = $uuid ?: "";
        $this->user_id = $user_id ?: auth()->id();

        return [
            'folders' => $this->getFolders(),
            'collections' => $this->getCollections(),
        ];
    }

    protected function getFolders() : SupportCollection
    {
        $folders = Folder
            ::whereNull('deleted_at')
            ->where('parent_uuid', '=', $this->uuid)
            ->where('user_id', '=', $this->user_id)
            ->with('summary', 'allowedDestinations', 'allowedDestinations.folder', 'allowedDestinations.folder.ancestors', 'allowedDestinations.destinationFolder')
            ->get();

        return $folders->map(function ($folder) {
            $folderData             = (new FolderData($folder->toArray()))->toArray();
            $folderData['allowed']  = $this->formatAllowed($folder->allowedDestinations, 'folder', $folder->isRoot());
            $folderSummary = (new GetSummaryData)(null, collect([$folder]));
            $folderData['count']    = $folderSummary['total_cards'];
            $folderData['value']    = $folderSummary['current_value'];

            return $folderData;
        })->sortBy('name')->values();
    }

    protected function getCollections() : SupportCollection
    {
        $collections = Collection
            ::whereNull('deleted_at')
            ->where('folder_uuid', '=', $this->uuid)
            ->where('user_id', '=', $this->user_id)
            ->with('summary','allowedDestinations', 'allowedDestinations.folder', 'allowedDestinations.folder.ancestors', 'allowedDestinations.destinationFolder')
            ->get();

        return $collections->map(function ($collection) {
            $collectionData             = (new CollectionData($collection->toArray()))->toArray();
            $collectionData['allowed']  = $this->formatAllowed($collection->allowedDestinations, 'collection', empty($collection->folder_uuid));
            $collectionSummary = (new GetSummaryData)(collect([$collection]));
            $collectionData['count']    = $collectionSummary['total_cards'];
            $collectionData['value']    = $collectionSummary['current_value'];

            return $collectionData;
        })->sortBy('name')->values();
    }

    protected function formatAllowed(SupportCollection $destinations, bool $isRoot) : array
    {
        $allowed = $destinations->map(function ($destination) {
                $folder = $destination->destinationFolder;
                $ancestry   = $folder->ancestors->count()
                    ? implode(' > ', $folder->ancestors->pluck('name')->toArray())
                    : '';
                $path     = ($ancestry ? $ancestry . ' > ' : '') . $folder->name;
                $folderData = $folder->toArray();
                $folderData['ancestry'] = $ancestry;
                $folderData['path'] = $path;
                return (new FolderData($folderData))->toArray();
        })->sortBy('path')->values()->toArray();

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