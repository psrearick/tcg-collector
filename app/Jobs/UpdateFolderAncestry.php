<?php

namespace App\Jobs;

use App\Domain\Folders\Models\Folder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateFolderAncestry implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle() : void
    {
        Folder::withDepth()
            ->groupBy('id')
            ->having('depth', '=', 0)
            ->get()
            ->each(fn (Folder $folder) => $this->updateFolderDescendants($folder));
    }

    private function updateFolderDescendants(Folder $folder) : void
    {
        $folder->children->each(function ($child) {
            $this->updateFolderDescendants($child);
        });

        UpdateFolderTotals::dispatch($folder);
    }
}
