<?php

namespace App\Domain\Collections\Models;

use App\Domain\Base\Model;
use App\Domain\Cards\Models\Card;
use App\Models\CardSearchDataObject;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Domain\Collections\Models\CollectionCardSummary
 *
 * @property int $id
 * @property string $card_uuid
 * @property string $collection_uuid
 * @property int|null $price_when_added
 * @property int|null $price_when_updated
 * @property int|null $current_price
 * @property string|null $description
 * @property string|null $condition
 * @property int $quantity
 * @property string|null $finish
 * @property string|null $import_uuid
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string|null $date_added
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Card|null $card
 * @property-read CardSearchDataObject|null $cardSearchDataObject
 * @property-read \App\Domain\Collections\Models\Collection|null $collection
 * @method static \Illuminate\Database\Eloquent\Builder|CollectionCardSummary newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CollectionCardSummary newQuery()
 * @method static \Illuminate\Database\Query\Builder|CollectionCardSummary onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|CollectionCardSummary query()
 * @method static \Illuminate\Database\Eloquent\Builder|CollectionCardSummary whereCardUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CollectionCardSummary whereCollectionUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CollectionCardSummary whereCondition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CollectionCardSummary whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CollectionCardSummary whereCurrentPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CollectionCardSummary whereDateAdded($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CollectionCardSummary whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CollectionCardSummary whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CollectionCardSummary whereFinish($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CollectionCardSummary whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CollectionCardSummary whereImportUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CollectionCardSummary wherePriceWhenAdded($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CollectionCardSummary wherePriceWhenUpdated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CollectionCardSummary whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CollectionCardSummary whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|CollectionCardSummary withTrashed()
 * @method static \Illuminate\Database\Query\Builder|CollectionCardSummary withoutTrashed()
 * @mixin \Eloquent
 */
class CollectionCardSummary extends Model
{
    use SoftDeletes;

    protected $casts = [
        'price_when_added'      => 'integer',
        'price_when_updated'    => 'integer',
        'current_price'         => 'integer',
        'quantity'              => 'integer',
    ];

    protected $guarded = [];

    public function card() : BelongsTo
    {
        return $this->belongsTo(Card::class, 'card_uuid', 'uuid');
    }

    public function cardSearchDataObject() : BelongsTo
    {
        return $this->belongsTo(CardSearchDataObject::class, 'card_uuid', 'card_uuid');
    }

    public function collection() : BelongsTo
    {
        return $this->belongsTo(Collection::class, 'collection_uuid', 'uuid');
    }
}
