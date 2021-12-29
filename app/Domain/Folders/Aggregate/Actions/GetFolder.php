<?php

use App\Domain\Folders\Aggregate\DataObjects\FolderData;
use App\Domain\Folders\Models\Folder;

class GetFolder
{
    public function __invoke(string $uuid)
    {
        $folder = Folder::where('uuid', '=', $uuid)->first();
        if (!$folder) {
            return;
        }
        
        return new FolderData($folder->toArray());
    }
}