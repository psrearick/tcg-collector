<?php

namespace App\Domain\Folders\Models;

use App\Domain\Base\Model;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Domain\Collections\Models\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kalnoy\Nestedset\NodeTrait;

class Folder extends Model
{
    use HasFactory, SoftDeletes, NodeTrait;

    protected $guarded = [];

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function collections() : HasMany
    {
        return $this->hasMany(Collection::class);
    }

    public static function uuid(string $uuid) : self
    {
        return self::where('uuid', '=', $uuid)->first();
    }
}