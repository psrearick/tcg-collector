<?php

namespace App\Domain\Prices\Models;

use App\Domain\Base\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Domain\Prices\Models\Price;

class PriceProvider extends Model
{
    protected $guarded = [];

    public function prices() : HasMany
    {
        return $this->hasMany(Price::class, 'provider_uuid', 'uuid');
    }
}