<?php

namespace App\Domain\Collections\Models;

use App\Domain\Base\Model;
use App\Domain\Cards\Models\Card;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CollectionCardSummary extends Model
{
    protected $guarded = [];

    public function card() : BelongsTo
    {
        return $this->belongsTo(Card::class, 'card_uuid', 'uuid');
    }

    public function collection() : BelongsTo
    {
        return $this->belongsTo(Collection::class, 'collection_uuid', 'uuid');
    }
}
