<?php

namespace App\Domain\Folders\Models;

use App\Domain\Base\Model;
use App\Domain\Collections\Models\Collection;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class FolderRoot extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function collections() : HasMany
    {
        return $this->hasMany(Collection::class);
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function uuid(string $uuid) : self
    {
        return self::where('uuid', '=', $uuid)->first();
    }
}
