<?php

namespace App\Domain\Cards\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * App\Domain\Cards\Models\Token
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $artist
 * @property string|null $asciiName
 * @property string|null $borderColor
 * @property int|null $edhrecRank
 * @property string|null $faceName
 * @property string|null $flavorText
 * @property string|null $frameVersion
 * @property int|null $hasFoil
 * @property int|null $hasNonFoil
 * @property string|null $image_path
 * @property int|null $isFullArt
 * @property int|null $isPromo
 * @property int|null $isReprint
 * @property string|null $layout
 * @property string|null $mcmId
 * @property string|null $mtgArenaId
 * @property string|null $mtgjsonV4Id
 * @property string|null $multiverseId
 * @property string $name
 * @property string|null $number
 * @property string|null $originalText
 * @property string|null $originalType
 * @property string|null $power
 * @property int|null $price_foil
 * @property int|null $price_normal
 * @property string|null $promoTypes
 * @property string|null $scryfallId
 * @property string|null $scryfallIllustrationId
 * @property string|null $scryfallOracleId
 * @property int|null $set_id
 * @property string|null $side
 * @property string|null $tcgplayerProductId
 * @property string|null $text
 * @property string|null $toughness
 * @property string|null $type
 * @property string $uuid
 * @property string|null $watermark
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Domain\Cards\Models\Card[] $cards
 * @property-read int|null $cards_count
 * @method static \Illuminate\Database\Eloquent\Builder|Token newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Token newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Token query()
 * @method static \Illuminate\Database\Eloquent\Builder|Token whereArtist($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Token whereAsciiName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Token whereBorderColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Token whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Token whereEdhrecRank($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Token whereFaceName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Token whereFlavorText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Token whereFrameVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Token whereHasFoil($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Token whereHasNonFoil($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Token whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Token whereImagePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Token whereIsFullArt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Token whereIsPromo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Token whereIsReprint($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Token whereLayout($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Token whereMcmId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Token whereMtgArenaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Token whereMtgjsonV4Id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Token whereMultiverseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Token whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Token whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Token whereOriginalText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Token whereOriginalType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Token wherePower($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Token wherePriceFoil($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Token wherePriceNormal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Token wherePromoTypes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Token whereScryfallId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Token whereScryfallIllustrationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Token whereScryfallOracleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Token whereSetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Token whereSide($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Token whereTcgplayerProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Token whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Token whereToughness($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Token whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Token whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Token whereUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Token whereWatermark($value)
 * @mixin \Eloquent
 */
class Token extends CardGeneric
{
    /**
     * get all cards that use this token
     *
     * @return BelongsToMany
     */
    public function cards() : BelongsToMany
    {
        return $this->belongsToMany(Card::class);
    }
}
