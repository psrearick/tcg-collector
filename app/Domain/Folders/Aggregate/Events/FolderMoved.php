<?php

namespace App\Domain\Folders\Aggregate\Events;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class FolderMoved extends ShouldBeStored
{
    /** @var string */
    public $destination;

    /** @var int */
    public $user_id;

    /** @var string */
    public $uuid;

    public function __construct(string $uuid, string $destination, int $user_id)
    {
        $this->uuid        = $uuid;
        $this->destination = $destination;
        $this->user_id     = $user_id;
    }
}
