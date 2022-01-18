<?php

namespace App\Domain\Prices\Models;

use App\Domain\Base\Model;
use App\Domain\Collections\Models\Collection;
use App\Domain\Folders\Models\Folder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Summary extends Model implements ShouldQueue
{
    protected $casts = [
        'current_value'     => 'integer',
        'acquired_value'    => 'integer',
        'gain_loss'         => 'integer',
        'total_cards'       => 'integer',
    ];

    protected $guarded = [];

    public function collection() : HasOne
    {
        return $this->hasOne(Collection::class, 'uuid', 'uuid');
    }

    public function folder() : HasOne
    {
        return $this->hasOne(Folder::class, 'uuid', 'uuid');
    }

    public function getGainLossPercentAttribute($value) : float
    {
        return round($value, 4);
    }
}
