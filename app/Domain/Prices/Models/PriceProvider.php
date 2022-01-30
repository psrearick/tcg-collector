<?php

namespace App\Domain\Prices\Models;

use App\Domain\Base\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Domain\Prices\Models\PriceProvider
 *
 * @property int $id
 * @property string $uuid
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Domain\Prices\Models\Price[] $prices
 * @property-read int|null $prices_count
 * @method static \Illuminate\Database\Eloquent\Builder|PriceProvider newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PriceProvider newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PriceProvider query()
 * @method static \Illuminate\Database\Eloquent\Builder|PriceProvider whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PriceProvider whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PriceProvider whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PriceProvider whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PriceProvider whereUuid($value)
 * @mixin \Eloquent
 */
class PriceProvider extends Model
{
    protected $guarded = [];

    public function prices() : HasMany
    {
        return $this->hasMany(Price::class, 'provider_uuid', 'uuid');
    }
}
