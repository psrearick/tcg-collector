<?php

namespace App\Domain\Groups\Presenters;

use App\App\Contracts\PresenterInterface;
use App\App\Scopes\UserScope;
use App\App\Scopes\UserScopeNotShared;
use App\Domain\Collections\Aggregate\DataObjects\CollectionData;
use App\Domain\Collections\Models\Collection;
use App\Domain\Folders\Models\Folder;
use App\Domain\Prices\Aggregate\Actions\GetSummaryData;
use App\Domain\Prices\Aggregate\DataObjects\SummaryData;
use App\Domains\Users\DataObjects\UserData;
use App\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\DB;

class GroupsShowPresenter implements PresenterInterface
{
    private ?array $pagination;

    private ?int $userId;

    public function __construct(?array $pagination = [], ?int $userId = null)
    {
        $this->pagination = $pagination;
        $this->userId     = $userId;
    }

    public function present() : array
    {
        $currentGroup   = auth()->user()->currentTeam;
        $uuids          = $this->getGroupCollectionUuids();
        // dd($uuids);
        $collections    = Collection::whereIn('uuid', $uuids)->with(['summary', 'user'])->get();
        $summaryData    = (new GetSummaryData)($collections, null, true);

        $collections->transform(function ($collection) {
            $collectionData = $collection->toArray();
            $summaryData   = new SummaryData((new GetSummaryData)(collect([$collection]), null, true));
            $userData       = new UserData($collectionData['user']);

            $collectionData['user']         = $userData;
            $collectionData['summary_data'] = $summaryData;

            return new CollectionData($collectionData);
        });

        $users = $currentGroup->allUsers()->map(function ($user) use ($collections) {
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

        if ($this->userId) {
            $userId      = $this->userId;
            $collections = $collections->filter(function ($collection) use ($userId) {
                return $collection->user_id == $userId;
            });
        }

        $collections = $collections->sortBy('name');

        $collectionPaginated = (new SupportCollection($collections));
        if ($this->pagination) {
            $page                = $this->pagination;
            $collectionPaginated = $collectionPaginated->paginate($page['per_page'], $page['total'], $page['current_page']);
        } else {
            $collectionPaginated = $collectionPaginated->paginate(25);
        }

        return [
            'collections'   => $collectionPaginated,
            'users'         => $users,
            'userId'        => $this->userId,
        ];
    }

    private function getGroupCollectionUuids()
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
}
