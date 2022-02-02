<?php

namespace App\Domain\Groups\Presenters;

use App\App\Contracts\PresenterInterface;
use App\Domain\Collections\Aggregate\DataObjects\CollectionData;
use App\Domain\Collections\Models\Collection;
use App\Domain\Groups\Actions\GetGroupCollectionUuids;
use App\Domain\Prices\Aggregate\Actions\GetSummaryData;
use App\Domain\Users\DataObjects\UserData;
use App\Support\Collection as AppSupportCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection as SupportCollection;

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
                return $collection->user_id === $userId;
            });
        }
    }

    private function getCollections() : void
    {
        $uuids                = (new GetGroupCollectionUuids)();
        $this->collections    = Collection::with(['summary', 'user'])
            ->whereIn('uuid', $uuids)
            ->get();

        $this->transformCollections();
        $this->filterCollections();
        $this->sortCollections();
    }

    private function getUsers() : SupportCollection
    {
        $currentGroup   = auth()->user()->currentTeam;
        $collections    = $this->collections;

        return $currentGroup->allUsers()->map(function ($user) use ($collections) {
            $data               = new UserData($user->toArray());
            $userCollections    = $collections->filter(function ($collection) use ($user) {
                return $collection->user_id === $user->id;
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
            $collectionData['user']         = new UserData($collectionData['user']);
            $collectionData['summary_data'] = $summary;

            return new CollectionData($collectionData);
        });
    }
}
