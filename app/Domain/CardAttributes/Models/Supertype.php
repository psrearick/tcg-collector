<?php

namespace App\Domain\CardAttributes\Models;

use App\Domain\Base\Model;
use App\Domain\Cards\Models\Card;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * App\Domain\CardAttributes\Models\Supertype
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|Card[] $cards
 * @property-read int|null $cards_count
 * @method static \Illuminate\Database\Eloquent\Builder|Supertype newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Supertype newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Supertype query()
 * @mixin \Eloquent
 */
class Supertype extends Model
{
    use HasFactory;

    /**
     * get all cards assigned to this supertype
     *
     * @return MorphToMany
     */
    public function cards() : MorphToMany
    {
        return $this->morphedByMany(Card::class, 'supertypeable');
    }
}
