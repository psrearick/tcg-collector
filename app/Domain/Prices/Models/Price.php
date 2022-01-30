<?php

namespace App\Domain\Prices\Models;

use App\Domain\Base\Model;
use App\Domain\Cards\Models\Card;
use Database\Factories\PriceFactory;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Domain\Prices\Models\Price
 *
 * @property int $id
 * @property string $card_uuid
 * @property string $provider_uuid
 * @property int|null $price
 * @property string|null $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Card|null $card
 * @property-read \App\Domain\Prices\Models\PriceProvider|null $priceProvider
 * @method static \Database\Factories\PriceFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Price newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Price newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Price query()
 * @method static \Illuminate\Database\Eloquent\Builder|Price whereCardUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Price whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Price whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Price wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Price whereProviderUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Price whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Price whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
