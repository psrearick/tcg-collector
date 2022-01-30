<?php

namespace App\Domain\CardAttributes\Models;

use App\Domain\Base\Model;
use App\Domain\Cards\Models\Card;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * App\Domain\CardAttributes\Models\FrameEffect
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|Card[] $cards
 * @property-read int|null $cards_count
 * @method static \Illuminate\Database\Eloquent\Builder|FrameEffect newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FrameEffect newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FrameEffect query()
 * @method static \Illuminate\Database\Eloquent\Builder|FrameEffect whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FrameEffect whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FrameEffect whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FrameEffect whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FrameEffect whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class FrameEffect extends Model
{
    use HasFactory;

    /**
     * Get all cards assigned to this frame effect
     *
     * @return MorphToMany
     */
    public function cards() : BelongsToMany
    {
        return $this->belongsToMany(Card::class, 'card_frame_effect');
    }
}
