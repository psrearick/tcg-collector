<?php

namespace App\Domain\CardAttributes\Models;

use App\Domain\Base\Model;
use App\Domain\Sets\Models\Set;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Domain\CardAttributes\Models\Printing
 *
 * @property-read Set|null $Set
 * @method static \Illuminate\Database\Eloquent\Builder|Printing newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Printing newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Printing query()
 * @mixin \Eloquent
 */
class Printing extends Model
{
    use HasFactory;

    /**
     * @return BelongsTo
     */
    public function Set() : BelongsTo
    {
        return $this->belongsTo(Set::class);
    }
}
