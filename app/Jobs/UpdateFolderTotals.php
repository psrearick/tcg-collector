<?php

namespace App\Jobs;

use App\Domain\Folders\Models\Folder;
use App\Domain\Prices\Aggregate\Actions\GetFolderTotalsWithoutUpdate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateFolderTotals implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private Folder $folder;

    public function __construct(Folder $folder)
    {
        $this->folder = $folder;
    }

    public function handle() : void
    {
        $folderTotals = (new GetFolderTotalsWithoutUpdate)($this->folder);

        $this->folder->summary()->updateOrCreate([
            'uuid'  => $this->folder->uuid,
            'type'  => 'folder',
        ], $folderTotals);
    }
}
