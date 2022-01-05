<?php

namespace App\Http\Controllers;

use App\Domain\Groups\Presenters\GroupsShowPresenter;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class GroupsController
{
    public function show(Request $request) : Response
    {
        $presenter = (new GroupsShowPresenter($request->paginate))->present();
        return Inertia::render('Groups/Show', [
            'collections'   => $presenter['collections'],
            'users'         => $presenter['users'],
        ]);
    }
}