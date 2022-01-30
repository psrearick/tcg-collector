<?php

namespace App\Domain\Folders\Models;

use App\Domain\Base\Model;
use App\Domain\Collections\Models\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Domain\Folders\Models\AllowedDestination
 *
 * @property int $id
 * @property string $uuid
 * @property string $destination
 * @property string $type
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Collection|null $collection
 * @property-read \App\Domain\Folders\Models\Folder|null $destinationFolder
 * @property-read \App\Domain\Folders\Models\Folder|null $folder
 * @method static \Illuminate\Database\Eloquent\Builder|AllowedDestination newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AllowedDestination newQuery()
 * @method static \Illuminate\Database\Query\Builder|AllowedDestination onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AllowedDestination query()
 * @method static \Illuminate\Database\Eloquent\Builder|AllowedDestination whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AllowedDestination whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AllowedDestination whereDestination($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AllowedDestination whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AllowedDestination whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AllowedDestination whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AllowedDestination whereUuid($value)
 * @method static \Illuminate\Database\Query\Builder|AllowedDestination withTrashed()
 * @method static \Illuminate\Database\Query\Builder|AllowedDestination withoutTrashed()
 * @mixin \Eloquent
 */
class AllowedDestination extends Model
{
    use HasFactory, SoftDeletes;

    public function collection() : BelongsTo
    {
        return $this->belongsTo(Collection::class, 'uuid', 'uuid');
    }

    public function destinationFolder() : BelongsTo
    {
        return $this->belongsTo(Folder::class, 'destination', 'uuid');
    }

    public function folder() : BelongsTo
    {
        return $this->belongsTo(Folder::class, 'uuid', 'uuid');
    }
}
