<?php

namespace App\Models;

use App\Domain\Base\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\CardSearchDataObject
 *
 * @property int $id
 * @property string $card_uuid
 * @property string $card_name
 * @property string $card_name_normalized
 * @property string|null $set_id
 * @property string|null $set_name
 * @property string|null $set_code
 * @property string|null $features
 * @property string|null $prices
 * @property string|null $collector_number
 * @property mixed|null $finishes
 * @property string|null $image
 * @property string|null $set_image
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\CardSearchDataObjectFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|CardSearchDataObject newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CardSearchDataObject newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CardSearchDataObject query()
 * @method static \Illuminate\Database\Eloquent\Builder|CardSearchDataObject whereCardName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CardSearchDataObject whereCardNameNormalized($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CardSearchDataObject whereCardUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CardSearchDataObject whereCollectorNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CardSearchDataObject whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CardSearchDataObject whereFeatures($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CardSearchDataObject whereFinishes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CardSearchDataObject whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CardSearchDataObject whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CardSearchDataObject wherePrices($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CardSearchDataObject whereSetCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CardSearchDataObject whereSetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CardSearchDataObject whereSetImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CardSearchDataObject whereSetName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CardSearchDataObject whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CardSearchDataObject extends Model
{
    use HasFactory;
}
