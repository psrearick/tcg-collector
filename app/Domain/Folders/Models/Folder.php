<?php

namespace App\Domain\Folders\Models;

use App\Domain\Folders\Models\FolderRoot;
use App\Domain\Prices\Models\Summary;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Kalnoy\Nestedset\NodeTrait;

class Folder extends FolderRoot
{
    use NodeTrait;

    public function summary() : BelongsTo
    {
        return $this->belongsTo(Summary::class, 'uuid', 'uuid');
    }
}
