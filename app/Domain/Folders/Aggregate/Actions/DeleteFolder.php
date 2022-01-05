<?php

namespace App\Domain\Folders\Aggregate\Actions;

use App\Domain\Folders\Aggregate\FolderAggregateRoot;

class DeleteFolder
{
    public function __invoke(string $uuid) : string
    {
        FolderAggregateRoot::retrieve($uuid)
            ->deleteFolder()
            ->persist();

        return $uuid;
    }
}
