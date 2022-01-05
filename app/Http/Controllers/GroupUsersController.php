<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Domains\Users\DataObjects\UserData;
use Inertia\Inertia;
use Inertia\Response;
use App\Domain\Collections\Models\Collection;
use App\Domain\Folders\Models\Folder;
use App\Domain\Groups\Presenters\GroupUserPresenter;

class GroupUsersController
{
    public function show(int $user) : Response
    {
        $groupUser = (new GroupUserPresenter($user, null))->present();

        return Inertia::render('Groups/User', $groupUser);
    }
}
