<?php

namespace App\Domain\Folders\Aggregate;

use App\Domain\Folders\Aggregate\Events\FolderCreated;
use App\Domain\Folders\Aggregate\Events\FolderDeleted;
use App\Domain\Folders\Aggregate\Events\FolderMoved;
use App\Domain\Folders\Aggregate\Events\FolderUpdated;
use Illuminate\Support\Facades\Auth;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class FolderAggregateRoot extends AggregateRoot
{
    public function createFolder(array $attributes) : self
    {
        $attributes['user_id']  = Auth::id();
        $attributes['uuid']     = $this->uuid();
        $this->recordThat(new FolderCreated($attributes));

        return $this;
    }

    public function deleteFolder() : self
    {
        $this->recordThat(new FolderDeleted());

        return $this;
    }

    public function moveFolder(string $uuid, string $destination, int $userId) : self
    {
        $this->recordThat(new FolderMoved($uuid, $destination, $userId));

        return $this;
    }

    public function updateFolder(array $attributes) : self
    {
        $attributes['user_id'] = Auth::id();
        $this->recordThat(new FolderUpdated($attributes));

        return $this;
    }
}
