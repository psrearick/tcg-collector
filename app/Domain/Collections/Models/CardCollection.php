<?php

namespace App\Domain\Collections\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class CardCollection extends Pivot
{
    protected $casts = [
        'price_when_added'  => 'integer',
        'quantity'          => 'integer',
    ];
}
