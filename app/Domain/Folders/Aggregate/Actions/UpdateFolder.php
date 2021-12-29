<?php

namespace App\Domain\Folders\Aggregate\Actions;

use App\Domain\Folders\Aggregate\DataObjects\FolderData;
use App\Domain\Folders\Aggregate\FolderAggregateRoot;

class UpdateFolder
{
    public function __invoke(array $folder)
    {
        $data = (new FolderData($folder))->toArray();
        $uuid = $data['uuid'];
        FolderAggregateRoot::retrieve($uuid)
            ->updateFolder($data)
            ->persist();
        
        return $uuid;
    }
}