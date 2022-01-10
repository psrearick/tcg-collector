<?php

namespace App\App\Scopes;

use App\Domain\Collections\Models\Collection;
use App\Domain\Folders\Models\Folder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class UserScopeNotShared implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param Builder $builder
     * @param Model $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        $inGroup = request()->get('inGroup');

        if (!$inGroup) {
            return $builder->where('collections.user_id', Auth::id());
        }

        $folders = [];
        Folder::inCurrentGroup()->get()->each(function ($folder) use (&$folders) {
            $folders = array_merge($folders, Folder::descendantsAndSelf($folder)->pluck('uuid')->all());
        });

        $collections = Collection::inCurrentGroup()->get()->pluck('uuid')->toArray();

        return $builder->whereIn('uuid', $collections)->orWhereIn('folder_uuid', $folders);
    }
}
