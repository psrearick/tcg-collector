<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class GroupsController
{
    public function show() : Response
    {
        $collections = auth()->user()->currentTeam->collections;
        return Inertia::render('Groups/Show', [
            'collections' => $collections,
        ]);
    }
}