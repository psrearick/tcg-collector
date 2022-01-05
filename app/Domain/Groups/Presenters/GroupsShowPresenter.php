<?php

namespace App\Domain\Groups\Presenters;

use App\App\Contracts\PresenterInterface;
use App\Domain\Collections\Aggregate\DataObjects\CollectionData;
use App\Domain\Folders\Models\Folder;
use App\Domain\Prices\Aggregate\Actions\GetSummaryData;
use App\Domain\Prices\Aggregate\DataObjects\SummaryData;
use App\Domains\Users\DataObjects\UserData;
use App\Support\Collection;

class GroupsShowPresenter implements PresenterInterface
{
    private ?array $pagination;

    public function __construct(?array $pagination = [])
    {
        $this->pagination = $pagination;
    }

    public function present(): array
    {
        $currentGroup   = auth()->user()->currentTeam;
        $collections    = $currentGroup->collections;
        $folders        = $currentGroup->folders;
        
        $collectionsMerged = [];

        $collections->each(function ($collection) use (&$collectionsMerged) {
            $data = New CollectionData($collection->toArray());
            $data->user = New UserData($collection->user->toArray());
            $collectionsMerged[] = $data;
        });

        $folders->each(function ($folder) use (&$collectionsMerged) {
            $folder->collections->each(function ($collection) use (&$collectionsMerged) {
                $data = New CollectionData($collection->toArray());
                $data->user = New UserData($collection->user->toArray());
                $collectionsMerged[] = $data;
            });

            $folder->descendants->each(function ($descendant) use (&$collectionsMerged) {
                $descendant->collections->each(function ($collection) use (&$collectionsMerged) {
                    $data = New CollectionData($collection->toArray());
                    $data->user = New UserData($collection->user->toArray());
                    $collectionsMerged[] = $data;
                });
            });
        });

        $users = $currentGroup->allUsers()->map(function ($user) use ($collectionsMerged) {
            $data                   = new UserData($user->toArray());
            $data->collection_count = collect($collectionsMerged)->where('user_id', '=', $user->id)->count();
            $data->folder_count     = Folder::inCurrentGroup()->where('user_id', '=', $user->id)->count();

            return $data;
        });

        collect($collectionsMerged)->transform(function ($collection) {
            $summaryData = (new GetSummaryData)(collect([$collection]), null, false);
            $collection->summary_data = new SummaryData($summaryData);
        });

        $collectionPaginated = (new Collection($collectionsMerged));
        if ($this->pagination) {
            $page = $this->pagination;
            $collectionPaginated = $collectionPaginated->paginate($page['per_page'], $page['total'], $page['current_page'], $page['page_name']);
        } else {
            $collectionPaginated = $collectionPaginated->paginate(35);
        }

        return [
            'collections'   => $collectionPaginated,
            'users'         => $users,
        ];
    }
}
