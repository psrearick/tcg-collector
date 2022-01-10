<?php

namespace App\Domain\Prices\Aggregate\Actions;

use App\Domain\Folders\Models\Folder;

class UpdateFolderAncestryTotals
{
    public function __invoke(Folder $folder) : void
    {
        $this->updateFolderTotals($folder);
        $folder->ancestors->each(function ($ancestor) {
            $this->updateFolderTotals($ancestor);
        });
    }

    private function updateFolderTotals(Folder $folder) : void
    {
        $folderTotals         = (new GetFolderTotals)($folder, true);
        $folderTotals['type'] = 'folder';
        $folder->summary()->updateOrCreate([
            'uuid' => $folder->uuid,
        ], $folderTotals);
    }
}
