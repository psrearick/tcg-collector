<?php

namespace App\Domain\Folders\Aggregate\Actions;

use App\Domain\Folders\Aggregate\DataObjects\FolderData;
use App\Domain\Folders\Models\Folder;

class GetFolder
{
    public function __invoke(string $uuid) : FolderData
    {
        $folderData = [];
        $folder     = Folder::uuid($uuid);
        if ($folder) {
            $folderData = $folder->toArray();
        }

        return new FolderData($folderData);
    }
}
