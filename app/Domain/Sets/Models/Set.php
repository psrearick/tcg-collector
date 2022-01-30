<?php

namespace App\Domain\Sets\Models;

use App\App\Scopes\NotOnlineOnlySetScope;
use App\Domain\Base\Model;
use App\Domain\CardAttributes\Models\Printing;
use App\Domain\Cards\Models\Card;
use App\Domain\Cards\Models\Token;
use Database\Factories\SetFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Domain\Sets\Models\Set
 *
 * @property int $id
 * @property string $setId
 * @property string $name
 * @property string $code
 * @property string|null $mtgoCode
 * @property int|null $tcgPlayerGroupId
 * @property string|null $type
 * @property string|null $releaseDate
 * @property string|null $block
 * @property string|null $blockCode
 * @property string|null $parentCode
 * @property int|null $setSize
 * @property int|null $printedSetSize
 * @property int|null $isOnlineOnly
 * @property int|null $isFoilOnly
 * @property int|null $isNonFoilOnly
 * @property string|null $scryfallUri
 * @property string|null $scryfallApiUri
 * @property string|null $scryfallSvgUri
 * @property string|null $scryfallApiSearch
 * @property string|null $svgPath
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|Card[] $cards
 * @property-read int|null $cards_count
 * @property-read \Illuminate\Database\Eloquent\Collection|Printing[] $printings
 * @property-read int|null $printings_count
 * @property-read \Illuminate\Database\Eloquent\Collection|Token[] $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\SetFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Set newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Set newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Set query()
 * @method static \Illuminate\Database\Eloquent\Builder|Set whereBlock($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Set whereBlockCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Set whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Set whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Set whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Set whereIsFoilOnly($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Set whereIsNonFoilOnly($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Set whereIsOnlineOnly($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Set whereMtgoCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Set whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Set whereParentCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Set wherePrintedSetSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Set whereReleaseDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Set whereScryfallApiSearch($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Set whereScryfallApiUri($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Set whereScryfallSvgUri($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Set whereScryfallUri($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Set whereSetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Set whereSetSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Set whereSvgPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Set whereTcgPlayerGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Set whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Set whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Set extends Model
{
    use HasFactory;

    public static function booted() : void
    {
        static::addGlobalScope(new NotOnlineOnlySetScope);
    }

    /**
     * get all cards in this set
     *
     * @return HasMany
     */
    public function cards() : HasMany
    {
        return $this->hasMany(Card::class);
    }

    /**
     * return this cards printing record
     *
     * @return HasMany
     */
    public function printings() : HasMany
    {
        return $this->hasMany(Printing::class);
    }

    /**
     * get all tokens in this set
     *
     * @return HasMany
     */
    public function tokens() : HasMany
    {
        return $this->hasMany(Token::class);
    }

    protected static function newFactory()
    {
        return SetFactory::new();
    }
}
