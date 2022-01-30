<?php

namespace App\Domain\Cards\Models;

use App\Domain\Base\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Domain\Cards\Models\MultiverseId
 *
 * @property int $id
 * @property int $multiverse_id
 * @property int $card_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Domain\Cards\Models\Card|null $Card
 * @method static \Illuminate\Database\Eloquent\Builder|MultiverseId newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MultiverseId newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MultiverseId query()
 * @method static \Illuminate\Database\Eloquent\Builder|MultiverseId whereCardId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MultiverseId whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MultiverseId whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MultiverseId whereMultiverseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MultiverseId whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class MultiverseId extends Model
{
    public function Card() : BelongsTo
    {
        return $this->belongsTo(Card::class);
    }
}
