<?php

namespace App\Domain\Collections\Models;

use App\Domain\Base\Model;
use App\Domain\Cards\Models\Card;
use App\Models\CardSearchDataObject;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CollectionCardSummary extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    public function card() : BelongsTo
    {
        return $this->belongsTo(Card::class, 'card_uuid', 'uuid');
    }

    public function cardSearchDataObject() : BelongsTo
    {
        return $this->belongsTo(CardSearchDataObject::class, 'card_uuid', 'card_uuid');
    }

    public function collection() : BelongsTo
    {
        return $this->belongsTo(Collection::class, 'collection_uuid', 'uuid');
    }
}
