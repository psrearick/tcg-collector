<?php

namespace App\Http\Controllers;

use App\Domain\Groups\Presenters\GroupUserPresenter;
use Inertia\Inertia;
use Inertia\Response;

class GroupUsersController
{
    public function show(int $user) : Response
    {
        $groupUser = (new GroupUserPresenter($user, null))->present();

        return Inertia::render('Groups/User', $groupUser);
    }
}
