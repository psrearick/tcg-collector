<?php

namespace App\Domain\CardAttributes\Models;

use App\Domain\Base\Model;
use App\Domain\Cards\Models\Card;
use App\Domain\Cards\Models\Token;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * App\Domain\CardAttributes\Models\Keyword
 *
 * @property int $id
 * @property string $name
 * @property string|null $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|Card[] $cards
 * @property-read int|null $cards_count
 * @property-read \Illuminate\Database\Eloquent\Collection|Token[] $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword query()
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Keyword extends Model
{
    use HasFactory;

    /**
     * Get all cards with this keyword
     *
     * @return MorphToMany
     */
    public function cards() : MorphToMany
    {
        return $this->morphedByMany(Card::class, 'keywordable');
    }

    /**
     * get all tokens assigned to this keyword
     *
     * @return MorphToMany
     */
    public function tokens() : MorphToMany
    {
        return $this->morphedByMany(Token::class, 'keywordable');
    }
}
