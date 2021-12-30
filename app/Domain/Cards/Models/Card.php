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
use App\Domain\Collections\Models\Collection as ModelsCollection;
use App\Domain\Mappings\Models\ApiMappings;
use App\Domain\Prices\Models\Price;
use App\Domain\Sets\Models\Set;
use App\Jobs\ImportCardImages;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use App\Domain\Cards\Actions\GetCardImage;

class Card extends Model
{
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
            ModelsCollection::class, 'card_collections', 'collection_uuid', 'card_uuid', 'uuid', 'uuid'
        );
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
