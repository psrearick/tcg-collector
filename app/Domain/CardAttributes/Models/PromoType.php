<?php

namespace App\Domain\CardAttributes\Models;

use App\Domain\Base\Model;
use App\Domain\Cards\Models\Card;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * App\Domain\CardAttributes\Models\PromoType
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|Card[] $Cards
 * @property-read int|null $cards_count
 * @method static \Illuminate\Database\Eloquent\Builder|PromoType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PromoType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PromoType query()
 * @method static \Illuminate\Database\Eloquent\Builder|PromoType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PromoType extends Model
{
    use HasFactory;

    /**
     * @return BelongsToMany
     */
    public function Cards() : BelongsToMany
    {
        return $this->belongsToMany(Card::class);
    }
}
