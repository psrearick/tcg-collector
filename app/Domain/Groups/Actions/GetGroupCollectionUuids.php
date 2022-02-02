<?php

namespace App\Domain\Groups\Actions;

use App\Domain\Collections\Models\Collection;
use App\Domain\Folders\Models\Folder;
use Illuminate\Support\Facades\DB;

class GetGroupCollectionUuids
{
    public function __invoke() : array
    {
        $folders = Folder::get()->reduce(function ($carry, $folder) {
            return array_merge($carry, Folder::descendantsAndSelf($folder)->pluck('uuid')->all());
        }, []);

        $collections = DB::table('collections')
            ->whereIn('folder_uuid', $folders)
            ->whereNull('deleted_at')
            ->pluck('uuid')
            ->toArray();

        $groupCollections = Collection::get()->pluck('uuid')->toArray();

        return array_unique(array_merge($collections, $groupCollections), SORT_REGULAR);
    }
}
