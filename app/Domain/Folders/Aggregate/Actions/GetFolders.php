<?php

namespace App\Domain\Folders\Aggregate\Actions;

use App\Domain\Folders\Aggregate\DataObjects\FolderData;
use App\Domain\Folders\Models\Folder;

class GetFolders
{
    public function __invoke()
    {
        return Folder::all()->map(function ($folder) {
            return (new FolderData($folder->toArray()))->toArray();
        })->values()->toArray();
    }
}