<?php

namespace App\Jobs;

use App\Domain\Base\Collection;
use App\Domain\Collections\Models\CollectionGeneral;
use App\Domain\Folders\Models\Folder;
use App\Domain\Prices\Aggregate\Actions\GetCollectionTotals;
use App\Domain\Prices\Aggregate\Actions\GetFolderTotalsWithoutUpdate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateAncestry implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle() : void
    {
        $collections = CollectionGeneral::whereNull('deleted_at')->get();

        $collections->each(function ($collection) {
            ray($collection);
            $this->updateCollectionTotals($collection);
        });

        $this->updateFolderAncestry();
    }

    private function updateCollectionTotals(Collection $collection) : void
    {
        $collectionTotals         = (new GetCollectionTotals)($collection);
        $collection->summary()->updateOrCreate([
            'uuid'  => $collection->uuid,
            'type'  => 'collection',
        ], $collectionTotals);
    }

    private function updateFolderAncestry() : void
    {
        $rootFolders = Folder::withDepth()
            ->groupBy('id')
            ->having('depth', '=', 0)
            ->get();

        $rootFolders->each(fn ($folder) => $this->updateFolderDescendants($folder));
    }

    private function updateFolderDescendants(Folder $folder) : void
    {
        $folder->children->each(function ($child) {
            $this->updateFolderDescendants($child);
        });

        $this->updateFolderTotals($folder);
    }

    private function updateFolderTotals(Folder $folder) : void
    {
        $folderTotals         = (new GetFolderTotalsWithoutUpdate)($folder);

        $folder->summary()->updateOrCreate([
            'uuid'  => $folder->uuid,
            'type'  => 'folder',
        ], $folderTotals);
    }
}
