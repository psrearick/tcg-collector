<?php

namespace App\Domain\Folders\Models;

use App\Domain\Folders\Models\FolderRoot;
use App\Domain\Prices\Models\Summary;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Kalnoy\Nestedset\NodeTrait;

class Folder extends FolderRoot
{
    use NodeTrait;

    public function allowedDestinationChildren() : HasMany
    {
        return $this->hasMany(AllowedDestination::class, 'destination', 'uuid');
    }

    public function allowedDestinations() : HasMany
    {
        return $this->hasMany(AllowedDestination::class, 'uuid', 'uuid');
    }

    public function summary() : BelongsTo
    {
        return $this->belongsTo(Summary::class, 'uuid', 'uuid');
    }
}
