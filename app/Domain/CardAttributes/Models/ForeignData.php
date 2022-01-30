<?php

namespace App\Domain\CardAttributes\Models;

use App\Domain\Base\Model;
use App\Domain\Cards\Models\Card;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Domain\CardAttributes\Models\ForeignData
 *
 * @property int $id
 * @property string|null $flavorText
 * @property string|null $language
 * @property int|null $multiverseid
 * @property string|null $name
 * @property string|null $text
 * @property string|null $type
 * @property int $card_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Card|null $cards
 * @method static \Illuminate\Database\Eloquent\Builder|ForeignData newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ForeignData newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ForeignData query()
 * @method static \Illuminate\Database\Eloquent\Builder|ForeignData whereCardId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ForeignData whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ForeignData whereFlavorText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ForeignData whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ForeignData whereLanguage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ForeignData whereMultiverseid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ForeignData whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ForeignData whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ForeignData whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ForeignData whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ForeignData extends Model
{
    use HasFactory;

    /**
     * get the card that owns this foreign data
     *
     * @return BelongsTo
     */
    public function cards() : BelongsTo
    {
        return $this->belongsTo(Card::class);
    }
}
