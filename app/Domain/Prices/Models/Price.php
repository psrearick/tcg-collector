<?php

namespace App\Domain\Prices\Models;

use App\Domain\Base\Model;
use App\Domain\Cards\Models\Card;
use Database\Factories\PriceFactory;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Price extends Model implements ShouldQueue
{
    use HasFactory;

    protected $guarded = [];

    public function card() : BelongsTo
    {
        return $this->belongsTo(Card::class, 'card_uuid', 'uuid');
    }

    public function priceProvider() : BelongsTo
    {
        return $this->belongsTo(PriceProvider::class, 'provider_uuid', 'uuid');
    }

    protected static function newFactory()
    {
        return PriceFactory::new();
    }
}
