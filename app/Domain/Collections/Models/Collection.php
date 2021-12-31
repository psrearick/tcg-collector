<?php

namespace App\Domain\Collections\Models;

use App\Domain\Base\Model;
use App\Domain\Cards\Models\Card;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Collection extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function cards() : BelongsToMany
    {
        return $this->belongsToMany(
            Card::class, 'card_collections', 'collection_uuid', 'card_uuid', 'uuid', 'uuid'
        )
        ->withPivot(['price_when_added', 'description', 'condition', 'quantity', 'date_added', 'created_at', 'finish'])
        ->whereNull('card_collections.deleted_at')
        ->withTimestamps();
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
