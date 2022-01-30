<?php

namespace App\Domain\CardAttributes\Models;

use App\Domain\Base\Model;
use App\Domain\Cards\Models\Card;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * App\Domain\CardAttributes\Models\RelatedObjects
 *
 * @property int $id
 * @property string $object_id
 * @property string $component
 * @property string $name
 * @property string $type
 * @property string $uri
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|Card[] $cards
 * @property-read int|null $cards_count
 * @method static \Illuminate\Database\Eloquent\Builder|RelatedObjects newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RelatedObjects newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RelatedObjects query()
 * @method static \Illuminate\Database\Eloquent\Builder|RelatedObjects whereComponent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RelatedObjects whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RelatedObjects whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RelatedObjects whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RelatedObjects whereObjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RelatedObjects whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RelatedObjects whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RelatedObjects whereUri($value)
 * @mixin \Eloquent
 */
class RelatedObjects extends Model
{
    use HasFactory;

    /**
     * @return BelongsToMany
     */
    public function cards() : BelongsToMany
    {
        return $this->belongsToMany(Card::class);
    }
}
