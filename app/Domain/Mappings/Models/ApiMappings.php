<?php

namespace App\Domain\Mappings\Models;

use App\Domain\Base\Model;
use App\Domain\Cards\Models\Card;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Domain\Mappings\Models\ApiMappings
 *
 * @property int $id
 * @property int $card_id
 * @property string $identifier
 * @property string $source
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Card|null $card
 * @method static \Illuminate\Database\Eloquent\Builder|ApiMappings newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ApiMappings newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ApiMappings query()
 * @method static \Illuminate\Database\Eloquent\Builder|ApiMappings whereCardId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApiMappings whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApiMappings whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApiMappings whereIdentifier($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApiMappings whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApiMappings whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ApiMappings extends Model
{
    public function card() : BelongsTo
    {
        return $this->belongsTo(Card::class);
    }
}
