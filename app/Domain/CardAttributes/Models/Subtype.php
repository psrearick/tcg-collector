<?php

namespace App\Domain\CardAttributes\Models;

use App\Domain\Base\Model;
use App\Domain\Cards\Models\Card;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * App\Domain\CardAttributes\Models\Subtype
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|Card[] $cards
 * @property-read int|null $cards_count
 * @method static \Illuminate\Database\Eloquent\Builder|Subtype newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Subtype newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Subtype query()
 * @mixin \Eloquent
 */
class Subtype extends Model
{
    use HasFactory;

    /**
     * get all cards assigned to this subtype
     *
     * @return MorphToMany
     */
    public function cards() : MorphToMany
    {
        return $this->morphedByMany(Card::class, 'subtypeable');
    }
}
