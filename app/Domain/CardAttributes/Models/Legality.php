<?php

namespace App\Domain\CardAttributes\Models;

use App\Domain\Base\Model;
use App\Domain\Cards\Models\Card;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * App\Domain\CardAttributes\Models\Legality
 *
 * @property int $id
 * @property string|null $format
 * @property string|null $status
 * @property int $card_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|Card[] $cards
 * @property-read int|null $cards_count
 * @method static \Illuminate\Database\Eloquent\Builder|Legality newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Legality newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Legality query()
 * @method static \Illuminate\Database\Eloquent\Builder|Legality whereCardId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Legality whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Legality whereFormat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Legality whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Legality whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Legality whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Legality extends Model
{
    use HasFactory;

    /**
     * get all cards assigned to this legality
     *
     * @return BelongsToMany
     */
    public function cards() : BelongsToMany
    {
        return $this->belongsToMany(Card::class);
    }
}
