<?php

namespace App\Domain\Collections\Models;

use App\App\Scopes\UserScope;
use App\App\Scopes\UserScopeNotShared;
use App\Domain\Base\Model;
use App\Domain\Cards\Models\Card;
use App\Domain\Folders\Models\AllowedDestination;
use App\Domain\Folders\Models\Folder;
use App\Domain\Prices\Models\Summary;
use App\Models\Team;
use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Collection extends Model
{
    use HasFactory, SoftDeletes, BelongsToUser;

    const USERSCOPE = 'notShared';

    protected $guarded = [];

    public function allowedDestinations() : HasMany
    {
        return $this->hasMany(AllowedDestination::class, 'uuid', 'uuid');
    }

    public function cards() : BelongsToMany
    {
        return $this->belongsToMany(
            Card::class, 'card_collections', 'collection_uuid', 'card_uuid', 'uuid', 'uuid'
        )
        ->withPivot(['price_when_added', 'description', 'condition', 'quantity', 'date_added', 'created_at', 'finish'])
        ->whereNull('card_collections.deleted_at')
        ->withTimestamps();
    }

    public function cardSummaries() : HasMany
    {
        return $this->hasMany(CollectionCardSummary::class, 'collection_uuid', 'uuid');
    }

    public function folder() : BelongsTo
    {
        return $this->belongsTo(Folder::class, 'folder_uuid', 'uuid');
    }

    public function groups() : BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'collection_teams', 'collection_uuid', 'team_id', 'uuid', 'id');
    }

    public function scopeInCurrentGroup($query) : Builder
    {
        return $query
            ->withoutGlobalScopes([UserScope::class, UserScopeNotShared::class])
            ->leftJoin('collection_teams', 'collections.uuid', '=', 'collection_teams.collection_uuid')
            ->where('collection_teams.team_id', '=', auth()->user()->currentTeam->id);
    }

    public function summary() : BelongsTo
    {
        return $this->belongsTo(Summary::class, 'uuid', 'uuid');
    }

    public static function uuid(string $uuid) : ?self
    {
        return self::where('uuid', '=', $uuid)->first();
    }
}
