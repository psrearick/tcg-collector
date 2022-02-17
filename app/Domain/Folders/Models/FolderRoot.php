<?php

namespace App\Domain\Folders\Models;

use App\Domain\Base\Collection as BaseCollection;
use App\Domain\Base\Model;
use App\Domain\Collections\Models\Collection;
use App\Models\User;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Domain\Folders\Models\FolderRoot
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|Collection[] $collections
 * @property-read int|null $collections_count
 * @property-read User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|FolderRoot newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FolderRoot newQuery()
 * @method static \Illuminate\Database\Query\Builder|FolderRoot onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|FolderRoot query()
 * @method static \Illuminate\Database\Query\Builder|FolderRoot withTrashed()
 * @method static \Illuminate\Database\Query\Builder|FolderRoot withoutTrashed()
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|BaseCollection[] $baseCollections
 * @property-read int|null $base_collections_count
 */
class FolderRoot extends Model
{
    use HasFactory, SoftDeletes, HasUuid;

    public const SCOPE = 'notShared';

    protected $guarded = [];

    public function baseCollections() : HasMany
    {
        return $this->hasMany(BaseCollection::class, 'folder_uuid', 'uuid');
    }

    public function collections() : HasMany
    {
        return $this->hasMany(Collection::class, 'folder_uuid', 'uuid');
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
