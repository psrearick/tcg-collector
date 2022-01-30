<?php

namespace App\Domain\CardAttributes\Models;

use App\Domain\Base\Model;
use App\Domain\Cards\Models\Card;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Domain\CardAttributes\Models\Face
 *
 * @property int $id
 * @property int $card_id
 * @property string $name
 * @property string|null $artist
 * @property string|null $flavorText
 * @property string|null $illustrationId
 * @property string|null $loyalty
 * @property string|null $manaCost
 * @property string|null $oracleText
 * @property string|null $power
 * @property string|null $printedName
 * @property string|null $printedText
 * @property string|null $printedTypeLine
 * @property string|null $toughness
 * @property string|null $typeLine
 * @property string|null $watermark
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Card|null $card
 * @method static \Illuminate\Database\Eloquent\Builder|Face newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Face newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Face query()
 * @method static \Illuminate\Database\Eloquent\Builder|Face whereArtist($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Face whereCardId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Face whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Face whereFlavorText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Face whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Face whereIllustrationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Face whereLoyalty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Face whereManaCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Face whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Face whereOracleText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Face wherePower($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Face wherePrintedName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Face wherePrintedText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Face wherePrintedTypeLine($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Face whereToughness($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Face whereTypeLine($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Face whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Face whereWatermark($value)
 * @mixin \Eloquent
 */
class Face extends Model
{
    use HasFactory;

    /**
     * @return BelongsTo
     */
    public function card() : BelongsTo
    {
        return $this->belongsTo(Card::class);
    }
}
