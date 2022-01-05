<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Domains\Users\DataObjects\UserData;
use Inertia\Inertia;
use Inertia\Response;
use App\Domain\Collections\Models\Collection;
use App\Domain\Folders\Models\Folder;

class GroupUsersController
{
    public function show(User $user) : Response
    {
        $data                   = new UserData($user->toArray());
        $data->collection_count = Collection::inCurrentGroup()->where('user_id', '=', $user->id)->count();
        $data->folder_count     = Folder::inCurrentGroup()->where('user_id', '=', $user->id)->count();
        return Inertia::render('Groups/User', [
            'groupUser'         => $user,
        ]);
    }
}