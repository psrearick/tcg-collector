<?php

namespace App\Domain\Collections\Models;

use App\Domain\Base\Model;
use App\Domain\Cards\Models\Card;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Collection extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function cards() : BelongsToMany
    {
        return $this->belongsToMany(
            Card::class, 'card_collections', 'card_uuid', 'collection_uuid', 'uuid', 'uuid'
        );
    }

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
