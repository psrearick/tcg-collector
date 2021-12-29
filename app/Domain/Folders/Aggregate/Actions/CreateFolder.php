<?php

namespace App\Domain\Folders\Aggregate\Actions;

use App\Domain\Folders\Aggregate\DataObjects\FolderData;
use Illuminate\Support\Str;
use App\Domain\Folders\Aggregate\FolderAggregateRoot;

class CreateFolder
{
    public function __invoke(array $folder) : string
    {
        $newUuid = Str::uuid();
        $data = (new FolderData($folder))->toArray();
        FolderAggregateRoot::retrieve($newUuid)
            ->createFolder($data)
            ->persist();
        
        return $newUuid;
    }
}
