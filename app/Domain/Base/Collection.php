<?php

namespace App\Domain\Base;

use App\App\Scopes\UserScope;
use App\App\Scopes\UserScopeNotShared;
use App\Domain\Cards\Models\Card;
use App\Domain\Collections\Models\CardCollection;
use App\Domain\Collections\Models\CollectionCardSummary;
use App\Domain\Folders\Models\AllowedDestination;
use App\Domain\Folders\Models\Folder;
use App\Domain\Prices\Models\Summary;
use App\Models\Team;
use App\Traits\BelongsToUser;
use App\Traits\HasUuid;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Domain\Base\Collection
 *
 * @property int $id
 * @property string $uuid
 * @property string $name
 * @property string|null $description
 * @property int|null $is_public
 * @property string|null $folder_uuid
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|AllowedDestination[] $allowedDestinations
 * @property-read int|null $allowed_destinations_count
 * @property-read \Illuminate\Database\Eloquent\Collection|CollectionCardSummary[] $cardSummaries
 * @property-read int|null $card_summaries_count
 * @property-read \Illuminate\Database\Eloquent\Collection|Card[] $cards
 * @property-read int|null $cards_count
 * @property-read Folder|null $folder
 * @property-read \Illuminate\Database\Eloquent\Collection|Team[] $groups
 * @property-read int|null $groups_count
 * @property-read Summary|null $summary
 * @property-read \App\Models\User|null $user
 * @method static Builder|Collection inCurrentGroup()
 * @method static Builder|Collection newModelQuery()
 * @method static Builder|Collection newQuery()
 * @method static \Illuminate\Database\Query\Builder|Collection onlyTrashed()
 * @method static Builder|Collection query()
 * @method static Builder|Collection whereCreatedAt($value)
 * @method static Builder|Collection whereDeletedAt($value)
 * @method static Builder|Collection whereDescription($value)
 * @method static Builder|Collection whereFolderUuid($value)
 * @method static Builder|Collection whereId($value)
 * @method static Builder|Collection whereIsPublic($value)
 * @method static Builder|Collection whereName($value)
 * @method static Builder|Collection whereUpdatedAt($value)
 * @method static Builder|Collection whereUserId($value)
 * @method static Builder|Collection whereUuid($value)
 * @method static \Illuminate\Database\Query\Builder|Collection withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Collection withoutTrashed()
 * @mixin Eloquent
 */
class Collection extends Model
{
    use HasFactory, SoftDeletes, BelongsToUser, HasUuid;

    protected $guarded = [];

    protected $table = 'collections';

    public function allowedDestinations() : HasMany
    {
        return $this->hasMany(AllowedDestination::class, 'uuid', 'uuid');
    }

    public function cards() : BelongsToMany
    {
        return $this->belongsToMany(
            Card::class, 'card_collections', 'collection_uuid', 'card_uuid', 'uuid', 'uuid'
        )
        ->using(CardCollection::class)
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

    public function scopeInCurrentGroup(Builder $query) : Builder
    {
        $user = auth()->user();
        if ($user === null) {
            return $query;
        }

        $team = $user->currentTeam;

        return $query
            ->withoutGlobalScopes([UserScope::class, UserScopeNotShared::class])
            ->leftJoin('collection_teams', 'collections.uuid', '=', 'collection_teams.collection_uuid')
            ->where('collection_teams.team_id', '=', optional($team)->id);
    }

    public function summary() : BelongsTo
    {
        return $this->belongsTo(Summary::class, 'uuid', 'uuid');
    }
}
