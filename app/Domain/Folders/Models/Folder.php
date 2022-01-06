<?php

namespace App\Domain\Folders\Models;

use App\App\Scopes\UserScope;
use App\App\Scopes\UserScopeNotShared;
use App\Domain\Folders\Models\FolderRoot;
use App\Domain\Prices\Models\Summary;
use App\Models\Team;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Kalnoy\Nestedset\NodeTrait;

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

    public function groupDescendants()
    {
        return $this->descendants()->inCurrentGroup();
    }

    public function groups() : BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'folder_teams', 'folder_uuid', 'team_id', 'uuid', 'id');
    }

    public function scopeInCurrentGroup($query) : Builder
    {
        return $query
            ->withoutGlobalScopes([UserScope::class, UserScopeNotShared::class])
            ->join('folder_teams', 'folders.uuid', '=', 'folder_teams.folder_uuid')
            ->where('folder_teams.team_id', '=', auth()->user()->currentTeam->id);
    }

    public function summary() : BelongsTo
    {
        return $this->belongsTo(Summary::class, 'uuid', 'uuid');
    }
}
