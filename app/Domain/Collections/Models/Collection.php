<?php

namespace App\Domain\Collections\Models;

use App\Domain\Base\Model;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Collection extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function folder() : BelongsTo
    {
        return $this->belongsTo(Folder::class);
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
