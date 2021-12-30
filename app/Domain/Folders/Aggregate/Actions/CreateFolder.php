<?php

namespace App\Domain\Folders\Aggregate\Actions;

use App\Domain\Folders\Aggregate\DataObjects\FolderData;
use App\Domain\Folders\Aggregate\FolderAggregateRoot;
use Illuminate\Support\Str;

class CreateFolder
{
    public function __invoke(array $folder) : string
    {
        $newUuid = Str::uuid();
        $data    = (new FolderData($folder))->toArray();
        FolderAggregateRoot::retrieve($newUuid)
            ->createFolder($data)
            ->persist();

        return $newUuid;
    }
}
