<?php

namespace App\Domain\Prices\Models;

use App\Domain\Base\Model;
use App\Domain\Collections\Models\Collection;
use App\Domain\Folders\Models\Folder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * App\Domain\Prices\Models\Summary
 *
 * @property int $id
 * @property string $uuid
 * @property string $type
 * @property int $total_cards
 * @property int|null $current_value
 * @property int|null $acquired_value
 * @property int|null $gain_loss
 * @property float $gain_loss_percent
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Collection|null $collection
 * @property-read Folder|null $folder
 * @method static \Illuminate\Database\Eloquent\Builder|Summary newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Summary newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Summary query()
 * @method static \Illuminate\Database\Eloquent\Builder|Summary whereAcquiredValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Summary whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Summary whereCurrentValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Summary whereGainLoss($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Summary whereGainLossPercent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Summary whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Summary whereTotalCards($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Summary whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Summary whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Summary whereUuid($value)
 * @mixin \Eloquent
 */
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
