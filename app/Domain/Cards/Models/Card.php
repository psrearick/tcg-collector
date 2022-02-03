<?php

namespace App\Domain\Cards\Models;

use App\Domain\Base\Model;
use App\Domain\CardAttributes\Models\Color;
use App\Domain\CardAttributes\Models\Face;
use App\Domain\CardAttributes\Models\Finish;
use App\Domain\CardAttributes\Models\ForeignData;
use App\Domain\CardAttributes\Models\FrameEffect;
use App\Domain\CardAttributes\Models\Game;
use App\Domain\CardAttributes\Models\Keyword;
use App\Domain\CardAttributes\Models\LeadershipSkill;
use App\Domain\CardAttributes\Models\Legality;
use App\Domain\CardAttributes\Models\PromoType;
use App\Domain\CardAttributes\Models\RelatedObjects;
use App\Domain\CardAttributes\Models\Ruling;
use App\Domain\Cards\Actions\GetCardImage;
use App\Domain\Collections\Models\CardCollection;
use App\Domain\Collections\Models\Collection as ModelsCollection;
use App\Domain\Collections\Models\CollectionGeneral;
use App\Domain\Mappings\Models\ApiMappings;
use App\Domain\Prices\Models\Price;
use App\Domain\Sets\Models\Set;
use App\Jobs\ImportCardImages;
use App\Traits\HasUuid;
use Database\Factories\CardFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

/**
 * App\Domain\Cards\Models\Card
 *
 * @property int $id
 * @property string|null $uuid
 * @property string $name
 * @property string $name_normalized
 * @property string $cardId
 * @property string|null $arenaId
 * @property string|null $languageCode
 * @property int|null $mtgoId
 * @property int|null $mtgoFoilId
 * @property int|null $tcgplayerId
 * @property int|null $cardmarketId
 * @property string|null $oracleId
 * @property string|null $printsSearchUri
 * @property string|null $rulingsUri
 * @property string|null $scryfallUri
 * @property string|null $scryfallApiUri
 * @property string|null $artist
 * @property int|null $booster
 * @property string|null $borderColor
 * @property string|null $cardBackId
 * @property string|null $collectorNumber
 * @property int|null $hasContentWarning
 * @property int|null $isOnlineOnly
 * @property string|null $frameVersion
 * @property int|null $isFullArt
 * @property int|null $isHighresImage
 * @property string|null $illustrationId
 * @property string|null $imageStatus
 * @property string|null $printedName
 * @property string|null $printedText
 * @property string|null $printedTypeLine
 * @property int|null $isPromo
 * @property string|null $rarity
 * @property string|null $releaseDate
 * @property int|null $isReprint
 * @property string|null $scryfallSetId
 * @property string|null $scryfallSetUri
 * @property int $set_id
 * @property int|null $isStorySpotlight
 * @property int|null $isTextless
 * @property int|null $isVariation
 * @property string|null $variationOf
 * @property string|null $watermark
 * @property float|null $convertedManaCost
 * @property int|null $edhrecRank
 * @property int|null $hasFoil
 * @property int|null $hasNonFoil
 * @property string|null $layout
 * @property string|null $handModifier
 * @property string|null $lifeModifier
 * @property string|null $loyalty
 * @property string|null $manaCost
 * @property string|null $oracleText
 * @property int|null $isOversized
 * @property string|null $power
 * @property int|null $isReserved
 * @property string|null $toughness
 * @property string|null $typeLine
 * @property string|null $imagePath
 * @property string|null $imagePngUri
 * @property string|null $imageBorderCropUri
 * @property string|null $imageArtCropUri
 * @property string|null $imageLargeUri
 * @property string|null $imageNormalUri
 * @property string|null $imageSmallUri
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|ApiMappings[] $apiMappings
 * @property-read int|null $api_mappings_count
 * @property-read \Illuminate\Database\Eloquent\Collection|ModelsCollection[] $collections
 * @property-read int|null $collections_count
 * @property-read \Illuminate\Database\Eloquent\Collection|Color[] $colors
 * @property-read int|null $colors_count
 * @property-read \Illuminate\Database\Eloquent\Collection|Face[] $faces
 * @property-read int|null $faces_count
 * @property-read \Illuminate\Database\Eloquent\Collection|Finish[] $finishes
 * @property-read int|null $finishes_count
 * @property-read \Illuminate\Database\Eloquent\Collection|ForeignData[] $foreignData
 * @property-read int|null $foreign_data_count
 * @property-read \Illuminate\Database\Eloquent\Collection|FrameEffect[] $frameEffects
 * @property-read int|null $frame_effects_count
 * @property-read \Illuminate\Database\Eloquent\Collection|Game[] $games
 * @property-read int|null $games_count
 * @property-read string $image_url
 * @property-read \Illuminate\Database\Eloquent\Collection|Keyword[] $keywords
 * @property-read int|null $keywords_count
 * @property-read \Illuminate\Database\Eloquent\Collection|LeadershipSkill[] $leadershipSkills
 * @property-read int|null $leadership_skills_count
 * @property-read \Illuminate\Database\Eloquent\Collection|Legality[] $legalities
 * @property-read int|null $legalities_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Domain\Cards\Models\MultiverseId[] $multiverseIds
 * @property-read int|null $multiverse_ids_count
 * @property-read \Illuminate\Database\Eloquent\Collection|Price[] $prices
 * @property-read int|null $prices_count
 * @property-read \Illuminate\Database\Eloquent\Collection|PromoType[] $promoTypes
 * @property-read int|null $promo_types_count
 * @property-read \Illuminate\Database\Eloquent\Collection|RelatedObjects[] $relatedObjects
 * @property-read int|null $related_objects_count
 * @property-read \Illuminate\Database\Eloquent\Collection|Ruling[] $rulings
 * @property-read int|null $rulings_count
 * @property-read Set|null $set
 * @method static \Database\Factories\CardFactory factory(...$parameters)
 * @method static Builder|Card newModelQuery()
 * @method static Builder|Card newQuery()
 * @method static Builder|Card notOnlineOnly()
 * @method static Builder|Card query()
 * @method static Builder|Card whereArenaId($value)
 * @method static Builder|Card whereArtist($value)
 * @method static Builder|Card whereBooster($value)
 * @method static Builder|Card whereBorderColor($value)
 * @method static Builder|Card whereCardBackId($value)
 * @method static Builder|Card whereCardId($value)
 * @method static Builder|Card whereCardmarketId($value)
 * @method static Builder|Card whereCollectorNumber($value)
 * @method static Builder|Card whereConvertedManaCost($value)
 * @method static Builder|Card whereCreatedAt($value)
 * @method static Builder|Card whereEdhrecRank($value)
 * @method static Builder|Card whereFrameVersion($value)
 * @method static Builder|Card whereHandModifier($value)
 * @method static Builder|Card whereHasContentWarning($value)
 * @method static Builder|Card whereHasFoil($value)
 * @method static Builder|Card whereHasNonFoil($value)
 * @method static Builder|Card whereId($value)
 * @method static Builder|Card whereIllustrationId($value)
 * @method static Builder|Card whereImageArtCropUri($value)
 * @method static Builder|Card whereImageBorderCropUri($value)
 * @method static Builder|Card whereImageLargeUri($value)
 * @method static Builder|Card whereImageNormalUri($value)
 * @method static Builder|Card whereImagePath($value)
 * @method static Builder|Card whereImagePngUri($value)
 * @method static Builder|Card whereImageSmallUri($value)
 * @method static Builder|Card whereImageStatus($value)
 * @method static Builder|Card whereIsFullArt($value)
 * @method static Builder|Card whereIsHighresImage($value)
 * @method static Builder|Card whereIsOnlineOnly($value)
 * @method static Builder|Card whereIsOversized($value)
 * @method static Builder|Card whereIsPromo($value)
 * @method static Builder|Card whereIsReprint($value)
 * @method static Builder|Card whereIsReserved($value)
 * @method static Builder|Card whereIsStorySpotlight($value)
 * @method static Builder|Card whereIsTextless($value)
 * @method static Builder|Card whereIsVariation($value)
 * @method static Builder|Card whereLanguageCode($value)
 * @method static Builder|Card whereLayout($value)
 * @method static Builder|Card whereLifeModifier($value)
 * @method static Builder|Card whereLoyalty($value)
 * @method static Builder|Card whereManaCost($value)
 * @method static Builder|Card whereMtgoFoilId($value)
 * @method static Builder|Card whereMtgoId($value)
 * @method static Builder|Card whereName($value)
 * @method static Builder|Card whereNameNormalized($value)
 * @method static Builder|Card whereOracleId($value)
 * @method static Builder|Card whereOracleText($value)
 * @method static Builder|Card wherePower($value)
 * @method static Builder|Card wherePrintedName($value)
 * @method static Builder|Card wherePrintedText($value)
 * @method static Builder|Card wherePrintedTypeLine($value)
 * @method static Builder|Card wherePrintsSearchUri($value)
 * @method static Builder|Card whereRarity($value)
 * @method static Builder|Card whereReleaseDate($value)
 * @method static Builder|Card whereRulingsUri($value)
 * @method static Builder|Card whereScryfallApiUri($value)
 * @method static Builder|Card whereScryfallSetId($value)
 * @method static Builder|Card whereScryfallSetUri($value)
 * @method static Builder|Card whereScryfallUri($value)
 * @method static Builder|Card whereSetId($value)
 * @method static Builder|Card whereTcgplayerId($value)
 * @method static Builder|Card whereToughness($value)
 * @method static Builder|Card whereTypeLine($value)
 * @method static Builder|Card whereUpdatedAt($value)
 * @method static Builder|Card whereUuid($value)
 * @method static Builder|Card whereVariationOf($value)
 * @method static Builder|Card whereWatermark($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|CollectionGeneral[] $collectionsGeneral
 * @property-read int|null $collections_general_count
 */
class Card extends Model
{
    use HasFactory, HasUuid;

    protected $casts = [
        'number' => 'int',
    ];

    /**
     * @return HasMany
     */
    public function apiMappings() : HasMany
    {
        return $this->hasMany(ApiMappings::class);
    }

    public function collections() : BelongsToMany
    {
        return $this->belongsToMany(
            ModelsCollection::class, 'card_collections', 'card_uuid', 'collection_uuid', 'uuid', 'uuid'
        )
            ->using(CardCollection::class)
            ->withPivot(['price_when_added', 'description', 'condition', 'quantity', 'finish', 'date_added', 'created_at'])
            ->whereNull('card_collections.deleted_at')
            ->withTimestamps();
    }

    public function collectionsGeneral() : BelongsToMany
    {
        return $this->belongsToMany(CollectionGeneral::class, 'card_collections', 'card_uuid', 'collection_uuid', 'uuid', 'uuid')
            ->using(CardCollection::class)
            ->withPivot(['price_when_added', 'description', 'condition', 'quantity', 'finish', 'date_added', 'created_at'])
            ->whereNull('card_collections.deleted_at')
            ->withTimestamps();
    }

    /**
     * get all colors this card is assigned to
     *
     * @return BelongsToMany
     */
    public function colors() : BelongsToMany
    {
        return $this->belongsToMany(Color::class)->withPivot('type');
    }

    /**
     * @return HasMany
     */
    public function faces() : HasMany
    {
        return $this->hasMany(Face::class);
    }

    public function finishes() : BelongsToMany
    {
        return $this->belongsToMany(Finish::class);
    }

    /**
     * Get all foreign data for this card
     *
     * @return HasMany
     */
    public function foreignData() : HasMany
    {
        return $this->hasMany(ForeignData::class);
    }

    /**
     * Get all frame effects for this card
     *
     * @return BelongsToMany
     */
    public function frameEffects() : BelongsToMany
    {
        return $this->belongsToMany(FrameEffect::class, 'card_frame_effect');
    }

    /**
     * @return BelongsToMany
     */
    public function games() : BelongsToMany
    {
        return $this->belongsToMany(Game::class);
    }

    /**
     * @return string
     */
    public function getImageUrlAttribute() : string
    {
        if ($this->imagePath) {
            return Storage::url($this->imagePath);
        }
        $imageUrl = app(GetCardImage::class)->execute($this->cardId, 'image');
        ImportCardImages::dispatchAfterResponse($this->id, $this->imageNormalUri);

        return $imageUrl;
    }

    /**
     * get all keywords for this card
     *
     * @return BelongsToMany
     */
    public function keywords() : BelongsToMany
    {
        return $this->belongsToMany(Keyword::class);
    }

    /**
     * Get all leadership  skill for this cards
     * @return BelongsToMany
     */
    public function leadershipSkills() : BelongsToMany
    {
        return $this->belongsToMany(LeadershipSkill::class);
    }

    /**
     * Get all legalities for this card
     *
     * @return HasMany
     */
    public function legalities() : HasMany
    {
        return $this->hasMany(Legality::class);
    }

    /**
     * @return HasMany
     */
    public function multiverseIds() : HasMany
    {
        return $this->hasMany(MultiverseId::class);
    }

    /**
     * @return HasMany
     */
    public function prices() : HasMany
    {
        return $this->hasMany(Price::class, 'card_uuid', 'uuid');
    }

    /**
     * @return BelongsToMany
     */
    public function promoTypes() : BelongsToMany
    {
        return $this->belongsToMany(PromoType::class);
    }

    /**
     * Get all printings for this card
     *
     * @return Collection
     */
//    public function printings() : Collection
//    {
//        return Card::where('scryfallOracleId', $this->scryfallOracleId)->with(['set', 'prices', 'prices.priceProvider'])->get();
//    }

    /**
     * @return Collection
     */
//    public function printingSets() : Collection
//    {
//        return Printing::where('scryfallOracleId', '=', $this->scryfallOracleId)->with('set')->get();
//    }

    /**
     * @return BelongsToMany
     */
    public function relatedObjects() : BelongsToMany
    {
        return $this->belongsToMany(RelatedObjects::class);
    }

    /**
     * Get all rulings for this card
     *
     * @return HasMany
     */
    public function rulings() : HasMany
    {
        return $this->hasMany(Ruling::class);
    }

    /**
     * Scope a query to only cards that are not online only.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeNotOnlineOnly($query)
    {
        return $query->where('cards.isOnlineOnly', '=', false);
    }

    /**
     * get the set this card is assigned to
     *
     * @return BelongsTo
     */
    public function set() : BelongsTo
    {
        return $this->belongsTo(Set::class);
    }

    protected static function newFactory()
    {
        return CardFactory::new();
    }

    /*
     * Get all tokens associated with this card
     *
     * @return BelongsToMany
     */
//    public function tokens() : BelongsToMany
//    {
//        return $this->belongsToMany(Token::class);
//    }
}
