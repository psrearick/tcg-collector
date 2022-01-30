<?php

namespace App\Domain\Collections\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * App\Domain\Collections\Models\CardCollection
 *
 * @method static \Illuminate\Database\Eloquent\Builder|CardCollection newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CardCollection newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CardCollection query()
 * @mixin \Eloquent
 */
class CardCollection extends Pivot
{
    protected $casts = [
        'price_when_added'  => 'integer',
        'quantity'          => 'integer',
    ];
}
