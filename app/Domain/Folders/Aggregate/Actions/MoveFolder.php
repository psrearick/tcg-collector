<?php

namespace App\Domain\Folders\Aggregate\Actions;

use App\Domain\Folders\Aggregate\FolderAggregateRoot;

class MoveFolder
{
    public function __invoke(string $uuid, string $destination, int $userId) : string
    {
        FolderAggregateRoot::retrieve($uuid)
            ->moveFolder($uuid, $destination, $userId)
            ->persist();

        return $uuid;
    }
}
