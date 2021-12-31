<?php

namespace App\Domain\Prices\Models;

use App\Domain\Base\Model;
use App\Domain\Cards\Models\Card;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Price extends Model implements ShouldQueue
{
    protected $guarded = [];

    public function card() : BelongsTo
    {
        return $this->belongsTo(Card::class, 'card_uuid', 'uuid');
    }

    public function priceProvider() : BelongsTo
    {
        return $this->belongsTo(PriceProvider::class, 'provider_uuid', 'uuid');
    }
}
