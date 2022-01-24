<?php

namespace App\Domain\Folders\Models;

use App\Domain\Base\Model;
use App\Domain\Collections\Models\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AllowedDestination extends Model
{
    use HasFactory, SoftDeletes;

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
