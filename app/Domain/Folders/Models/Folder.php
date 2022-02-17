<?php

namespace App\Domain\Folders\Models;

use App\App\Scopes\UserScope;
use App\App\Scopes\UserScopeNotShared;
use App\Domain\Prices\Models\Summary;
use App\Models\Team;
use App\Models\User;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Kalnoy\Nestedset\Collection;
use Kalnoy\Nestedset\DescendantsRelation;
use Kalnoy\Nestedset\NodeTrait;
use Kalnoy\Nestedset\QueryBuilder;

/**
 * App\Domain\Folders\Models\Folder
 *
 * @property int $id
 * @property string $uuid
 * @property string $name
 * @property string|null $description
 * @property int $user_id
 * @property int $is_public
 * @property string|null $parent_uuid
 * @property int $_lft
 * @property int $_rgt
 * @property int|null $parent_id
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|AllowedDestination[] $allowedDestinationChildren
 * @property-read int|null $allowed_destination_children_count
 * @property-read \Illuminate\Database\Eloquent\Collection|AllowedDestination[] $allowedDestinations
 * @property-read int|null $allowed_destinations_count
 * @property-read Collection|Folder[] $children
 * @property-read int|null $children_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Domain\Collections\Models\Collection[] $collections
 * @property-read int|null $collections_count
 * @property-read \Illuminate\Database\Eloquent\Collection|Team[] $groups
 * @property-read int|null $groups_count
 * @property-read Folder|null $parent
 * @property-read Summary|null $summary
 * @property-read User|null $user
 * @method static Collection|static[] all($columns = ['*'])
 * @method static QueryBuilder|Folder ancestorsAndSelf($id, array $columns = [])
 * @method static QueryBuilder|Folder ancestorsOf($id, array $columns = [])
 * @method static QueryBuilder|Folder applyNestedSetScope(?string $table = null)
 * @method static QueryBuilder|Folder countErrors()
 * @method static QueryBuilder|Folder d()
 * @method static QueryBuilder|Folder defaultOrder(string $dir = 'asc')
 * @method static QueryBuilder|Folder descendantsAndSelf($id, array $columns = [])
 * @method static QueryBuilder|Folder descendantsOf($id, array $columns = [], $andSelf = false)
 * @method static QueryBuilder|Folder fixSubtree($root)
 * @method static QueryBuilder|Folder fixTree($root = null)
 * @method static Collection|static[] get($columns = ['*'])
 * @method static QueryBuilder|Folder getNodeData($id, $required = false)
 * @method static QueryBuilder|Folder getPlainNodeData($id, $required = false)
 * @method static QueryBuilder|Folder getTotalErrors()
 * @method static QueryBuilder|Folder hasChildren()
 * @method static QueryBuilder|Folder hasParent()
 * @method static QueryBuilder|Folder inCurrentGroup()
 * @method static QueryBuilder|Folder isBroken()
 * @method static QueryBuilder|Folder leaves(array $columns = [])
 * @method static QueryBuilder|Folder makeGap(int $cut, int $height)
 * @method static QueryBuilder|Folder moveNode($key, $position)
 * @method static QueryBuilder|Folder newModelQuery()
 * @method static QueryBuilder|Folder newQuery()
 * @method static \Illuminate\Database\Query\Builder|Folder onlyTrashed()
 * @method static QueryBuilder|Folder orWhereAncestorOf(bool $id, bool $andSelf = false)
 * @method static QueryBuilder|Folder orWhereDescendantOf($id)
 * @method static QueryBuilder|Folder orWhereNodeBetween($values)
 * @method static QueryBuilder|Folder orWhereNotDescendantOf($id)
 * @method static QueryBuilder|Folder query()
 * @method static QueryBuilder|Folder rebuildSubtree($root, array $data, $delete = false)
 * @method static QueryBuilder|Folder rebuildTree(array $data, $delete = false, $root = null)
 * @method static QueryBuilder|Folder reversed()
 * @method static QueryBuilder|Folder root(array $columns = [])
 * @method static QueryBuilder|Folder whereAncestorOf($id, $andSelf = false, $boolean = 'and')
 * @method static QueryBuilder|Folder whereAncestorOrSelf($id)
 * @method static QueryBuilder|Folder whereCreatedAt($value)
 * @method static QueryBuilder|Folder whereDeletedAt($value)
 * @method static QueryBuilder|Folder whereDescendantOf($id, $boolean = 'and', $not = false, $andSelf = false)
 * @method static QueryBuilder|Folder whereDescendantOrSelf(string $id, string $boolean = 'and', string $not = false)
 * @method static QueryBuilder|Folder whereDescription($value)
 * @method static QueryBuilder|Folder whereId($value)
 * @method static QueryBuilder|Folder whereIsAfter($id, $boolean = 'and')
 * @method static QueryBuilder|Folder whereIsBefore($id, $boolean = 'and')
 * @method static QueryBuilder|Folder whereIsLeaf()
 * @method static QueryBuilder|Folder whereIsPublic($value)
 * @method static QueryBuilder|Folder whereIsRoot()
 * @method static QueryBuilder|Folder whereLft($value)
 * @method static QueryBuilder|Folder whereName($value)
 * @method static QueryBuilder|Folder whereNodeBetween($values, $boolean = 'and', $not = false)
 * @method static QueryBuilder|Folder whereNotDescendantOf($id)
 * @method static QueryBuilder|Folder whereParentId($value)
 * @method static QueryBuilder|Folder whereParentUuid($value)
 * @method static QueryBuilder|Folder whereRgt($value)
 * @method static QueryBuilder|Folder whereUpdatedAt($value)
 * @method static QueryBuilder|Folder whereUserId($value)
 * @method static QueryBuilder|Folder whereUuid($value)
 * @method static QueryBuilder|Folder withDepth(string $as = 'depth')
 * @method static \Illuminate\Database\Query\Builder|Folder withTrashed()
 * @method static QueryBuilder|Folder withoutRoot()
 * @method static \Illuminate\Database\Query\Builder|Folder withoutTrashed()
 * @mixin Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Domain\Collections\Models\Collection[] $groupCollections
 * @property-read int|null $group_collections_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Domain\Base\Collection[] $baseCollections
 * @property-read int|null $base_collections_count
 */
class Folder extends FolderRoot
{
    use NodeTrait;

    public function allowedDestinationChildren() : HasMany
    {
        return $this->hasMany(AllowedDestination::class, 'destination', 'uuid');
    }

    public function allowedDestinations() : HasMany
    {
        return $this->hasMany(AllowedDestination::class, 'uuid', 'uuid');
    }

    public function groupCollections() : HasMany
    {
        return $this->collections()->inCurrentGroup();
    }

    public function groupDescendants() : DescendantsRelation
    {
        return $this->descendants()->inCurrentGroup();
    }

    public function groups() : BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'folder_teams', 'folder_uuid', 'team_id', 'uuid', 'id');
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
            ->join('folder_teams', 'folders.uuid', '=', 'folder_teams.folder_uuid')
            ->where('folder_teams.team_id', '=', optional($team)->id);
    }

    public function summary() : BelongsTo
    {
        return $this->belongsTo(Summary::class, 'uuid', 'uuid');
    }
}
