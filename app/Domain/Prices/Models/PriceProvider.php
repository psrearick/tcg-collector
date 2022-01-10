<?php

namespace App\Domain\Prices\Models;

use App\Domain\Base\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PriceProvider extends Model
{
    protected $guarded = [];

    public function prices() : HasMany
    {
        return $this->hasMany(Price::class, 'provider_uuid', 'uuid');
    }
}
