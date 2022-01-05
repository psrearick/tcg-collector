<?php

namespace App\Http\Controllers;

use App\Domain\Groups\Presenters\GroupsShowPresenter;
use Inertia\Inertia;
use Inertia\Response;

class GroupsController
{
    public function show() : Response
    {
        $presenter = (new GroupsShowPresenter())->present();
        return Inertia::render('Groups/Show', [
            'collections'   => $presenter['collections'],
            'users'         => $presenter['users'],
        ]);
    }
}