<?php

namespace App\Domain\Folders\Models;

use App\Domain\Collections\Models\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AllowedDestination extends FolderRoot
{
    public function collection() : BelongsTo
    {
        return $this->belongsTo(Collection::class, 'uuid', 'uuid');
    }

    public function destinationFolder() : BelongsTo
    {
        return $this->belongsTo(Folder::class, 'destination', 'uuid');
    }

    public function folder() : BelongsTo
    {
        return $this->belongsTo(Folder::class, 'uuid', 'uuid');
    }
}
