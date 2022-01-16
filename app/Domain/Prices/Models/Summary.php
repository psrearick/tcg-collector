<?php

namespace App\Domain\Prices\Models;

use App\Domain\Base\Model;
use App\Domain\Collections\Models\Collection;
use App\Domain\Folders\Models\Folder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Summary extends Model implements ShouldQueue
{
    protected $casts = [
        'current_value'     => 'integer',
        'acquired_value'    => 'integer',
        'gain_loss'         => 'integer',
        'total_cards'       => 'integer',
    ];

    protected $guarded = [];

    public function collections() : HasMany
    {
        return $this->hasMany(Collection::class, 'uuid', 'uuid');
    }

    public function folders() : HasMany
    {
        return $this->hasMany(Folder::class, 'uuid', 'uuid');
    }

    public function getGainLossPercentAttribute($value) : float
    {
        return round($value, 4);
    }
}
