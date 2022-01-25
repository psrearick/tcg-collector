<?php

namespace App\Domain\Folders\Models;

use App\Domain\Base\Model;
use App\Domain\Collections\Models\Collection;
use App\Models\User;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class FolderRoot extends Model
{
    use HasFactory, SoftDeletes, HasUuid;

    const USERSCOPE = 'notShared';

    protected $guarded = [];

    public function collections() : HasMany
    {
        return $this->hasMany(Collection::class, 'folder_uuid', 'uuid');
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
