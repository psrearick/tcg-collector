<?php

namespace App\Domain\CardAttributes\Models;

use App\Domain\Base\Model;
use App\Domain\Cards\Models\Card;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * App\Domain\CardAttributes\Models\Finish
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|Card[] $cards
 * @property-read int|null $cards_count
 * @method static \Illuminate\Database\Eloquent\Builder|Finish newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Finish newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Finish query()
 * @method static \Illuminate\Database\Eloquent\Builder|Finish whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Finish whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Finish whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Finish whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Finish extends Model
{
    use HasFactory;

    public function cards() : BelongsToMany
    {
        return $this->belongsToMany(Card::class);
    }
}
