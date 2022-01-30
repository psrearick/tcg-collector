<?php

namespace App\Domain\CardAttributes\Models;

use App\Domain\Base\Model;
use App\Domain\Cards\Models\Card;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Domain\CardAttributes\Models\Ruling
 *
 * @property int $id
 * @property string|null $date
 * @property string|null $text
 * @property int $card_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Card|null $cards
 * @method static \Illuminate\Database\Eloquent\Builder|Ruling newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Ruling newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Ruling query()
 * @method static \Illuminate\Database\Eloquent\Builder|Ruling whereCardId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ruling whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ruling whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ruling whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ruling whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ruling whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Ruling extends Model
{
    use HasFactory;

    /**
     * get the card that owns this ruling
     *
     * @return BelongsTo
     */
    public function cards() : BelongsTo
    {
        return $this->belongsTo(Card::class);
    }
}
