<?php

namespace App\Domain\CardAttributes\Models;

use App\Domain\Base\Model;
use App\Domain\Cards\Models\Card;
use App\Domain\Cards\Models\Token;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * App\Domain\CardAttributes\Models\Type
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|Card[] $cards
 * @property-read int|null $cards_count
 * @property-read \Illuminate\Database\Eloquent\Collection|Token[] $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder|Type newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Type newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Type query()
 * @mixin \Eloquent
 */
class Type extends Model
{
    use HasFactory;

    /**
     * Get all cards assigned to this type
     *
     * @return MorphToMany
     */
    public function cards() : MorphToMany
    {
        return $this->morphedByMany(Card::class, 'typeable');
    }

    /**
     * Get all tokens assigned to this type
     *
     * @return MorphToMany
     */
    public function tokens() : MorphToMany
    {
        return $this->morphedByMany(Token::class, 'typeable');
    }
}
