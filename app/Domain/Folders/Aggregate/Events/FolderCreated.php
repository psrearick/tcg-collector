<?php

namespace App\Domain\Folders\Aggregate\Events;

use Illuminate\Support\Facades\Log;
use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class FolderCreated extends ShouldBeStored
{
    /** @var array */
    public $folderAttributes;

    public function __construct(array $folderAttributes)
    {
        $this->folderAttributes = $folderAttributes;
    }
}