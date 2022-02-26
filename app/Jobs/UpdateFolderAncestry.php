<?php

namespace App\Jobs;

use App\Domain\Folders\Models\Folder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;

class UpdateFolderAncestry implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private array $jobs = [];

    public function handle() : void
    {
        Folder::withDepth()
            ->groupBy('id')
            ->having('depth', '=', 0)
            ->get()
            ->each(fn (Folder $folder) => $this->updateFolderDescendants($folder));

        Bus::chain($this->jobs)->dispatch();
    }

    private function updateFolderDescendants(Folder $folder) : void
    {
        $folder->children->each(function ($child) {
            $this->updateFolderDescendants($child);
        });

        $this->jobs[] = new UpdateFolderTotals($folder);
    }
}
