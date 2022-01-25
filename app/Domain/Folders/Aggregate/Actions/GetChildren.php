<?php

namespace App\Domain\Folders\Aggregate\Actions;

use App\Domain\Collections\Aggregate\DataObjects\CollectionData;
use App\Domain\Collections\Models\Collection;
use App\Domain\Folders\Aggregate\DataObjects\FolderData;
use App\Domain\Folders\Models\Folder;
use App\Domain\Prices\Aggregate\Actions\GetSummaryData;
use Illuminate\Support\Collection as SupportCollection;

class GetChildren
{
    protected int $user_id;

    protected ?string $uuid;

    public function __invoke(?string $uuid = null, ?int $user_id = null)
    {
        $this->uuid    = $uuid ?: '';
        $this->user_id = $user_id ?: auth()->id();

        return [
            'folders'     => $this->getFolders(),
            'collections' => $this->getCollections(),
        ];
    }

    protected function formatAllowed(SupportCollection $destinations, bool $isRoot) : array
    {
        $allowed = $destinations->whereNull('deleted_at')
            ->filter(function ($destination) {
                return !!$destination->destinationFolder;
            })
            ->map(function ($destination) {
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

    protected function getCollections() : SupportCollection
    {
        $uuid        = $this->uuid;
        $collections = Collection::with(
                'summary',
                'allowedDestinations',
                'allowedDestinations.folder',
                'allowedDestinations.folder.ancestors',
                'allowedDestinations.destinationFolder',
                'groups'
            )
            ->whereNull('deleted_at')
            ->where('user_id', '=', $this->user_id);

        if ($uuid) {
            $collections = $collections->where('folder_uuid', '=', $uuid);
        } else {
            $collections = $collections->where(function ($query) {
                $query->where('folder_uuid', '=', '')
                    ->orWhereNull('folder_uuid');
            });
        }

        return $collections->get()->map(function ($collection) {
            $collectionSummary = (new GetSummaryData)(collect([$collection]));
            $collectionData             = (new CollectionData($collection->toArray()));
            $collectionData->allowed  = $this
                ->formatAllowed($collection->allowedDestinations, !$collection->folder_uuid);
            $collectionData->groups   = $collection->groups->pluck('id')->values()->toArray();
            $collectionData->summary_data = $collectionSummary;

            return $collectionData->toArray();
        })->sortBy('name')->values();
    }

    protected function getFolders() : SupportCollection
    {
        $uuid    = $this->uuid;
        $folders = Folder::with('summary',
                'allowedDestinations',
                'allowedDestinations.folder',
                'allowedDestinations.folder.ancestors',
                'allowedDestinations.destinationFolder',
                'groups'
            )
            ->where('user_id', '=', $this->user_id);

        if (!$uuid) {
            $folders = $folders->where(function ($query) use ($uuid) {
                $query->where('parent_uuid', '=', $uuid)
                        ->orWhereNull('parent_uuid');
            });
        } else {
            $folders = $folders->where('parent_uuid', '=', $uuid);
        }

        return $folders->get()->map(function ($folder) {
            $folderSummary              = (new GetSummaryData)(null, collect([$folder]));
            $folderData                 = (new FolderData($folder->toArray()));
            $folderData->allowed        = $this
                ->formatAllowed($folder->allowedDestinations, $folder->isRoot());
            $folderData->groups         = $folder->groups->pluck('id')->values()->toArray();
            $folderData->summary_data   = $folderSummary;

            return $folderData->toArray();
        })->sortBy('name')->values();
    }
}
