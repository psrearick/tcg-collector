<?php

namespace App\Domain\Groups\Presenters;

use App\App\Contracts\PresenterInterface;
use App\Domain\Collections\Aggregate\DataObjects\CollectionData;
use App\Domain\Collections\Models\Collection;
use App\Domain\Folders\Models\Folder;
use App\Domain\Prices\Aggregate\Actions\GetSummaryData;
use App\Domain\Users\DataObjects\UserData;
use App\Support\Collection as AppSupportCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\DB;

class GroupsShowPresenter implements PresenterInterface
{
    private ?SupportCollection $collections;

    private ?array $pagination;

    private ?int $userId;

    public function __construct(?array $pagination = [], ?int $userId = null)
    {
        $this->pagination = $pagination;
        $this->userId     = $userId;
    }

    public function present() : array
    {
        $this->getCollections();

        return [
            'collections'   => $this->paginateCollection(),
            'users'         => $this->getUsers(),
            'userId'        => $this->userId,
        ];
    }

    private function filterCollections() : void
    {
        if ($userId = $this->userId) {
            $this->collections = $this->collections->filter(function ($collection) use ($userId) {
                return $collection->user_id == $userId;
            });
        }
    }

    private function getCollections() : void
    {
        $uuids                = $this->getGroupCollectionUuids();
        $this->collections    = Collection::whereIn('uuid', $uuids)
            ->with(['summary', 'user'])->get();

        $this->transformCollections();
        $this->filterCollections();
        $this->sortCollections();
    }

    private function getGroupCollectionUuids() : array
    {
        $folders = [];
        Folder::get()->each(function ($folder) use (&$folders) {
            $folders = array_merge($folders, Folder::descendantsAndSelf($folder)->pluck('uuid')->all());
        });

        $collections = DB::table('collections')
            ->whereIn('folder_uuid', $folders)
            ->whereNull('deleted_at')
            ->pluck('uuid')
            ->toArray();

        $groupCollections = Collection::get()->pluck('uuid')->toArray();

        return array_unique(array_merge($collections, $groupCollections), SORT_REGULAR);
    }

    private function getUsers() : SupportCollection
    {
        $currentGroup   = auth()->user()->currentTeam;
        $collections    = $this->collections;

        return $currentGroup->allUsers()->map(function ($user) use ($collections) {
            $data               = new UserData($user->toArray());
            $userCollections    = $collections->filter(function ($collection) use ($user) {
                return $collection->user_id == $user->id;
            })->transform(function ($collection) {
                $collection->summary = $collection->summary_data;

                return $collection;
            });

            $data->collection_count = $userCollections->count();
            $data->summary_data     = (new GetSummaryData)($userCollections, null, true);

            foreach ($data->summary_data as $key => $datum) {
                $data->$key = $datum;
            }

            return $data;
        });
    }

    private function paginateCollection() : LengthAwarePaginator
    {
        $collectionPaginated = (new AppSupportCollection($this->collections));
        if ($this->pagination) {
            $page                = $this->pagination;
            $collectionPaginated = $collectionPaginated->paginate($page['per_page'], $page['total'], $page['current_page']);
        } else {
            $collectionPaginated = $collectionPaginated->paginate(25);
        }

        return $collectionPaginated;
    }

    private function sortCollections() : void
    {
        $this->collections = $this->collections->sortBy('name');
    }

    private function transformCollections() : void
    {
        $this->collections->transform(function ($collection) {
            $summary        = (new GetSummaryData)(collect([$collection]));
            $collectionData = $collection->toArray();
            $userData       = new UserData($collectionData['user']);

            $collectionData['user']         = $userData;
            $collectionData['summary_data'] = $summary;

            return new CollectionData($collectionData);
        });
    }
}
