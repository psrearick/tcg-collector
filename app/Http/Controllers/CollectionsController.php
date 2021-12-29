<?php

namespace App\Http\Controllers;

use App\Domain\Collections\Models\Collection;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Response;
use Inertia\Inertia;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use App\Domain\Collections\Aggregate\CollectionAggregateRoot;
use Illuminate\Support\Str;

class CollectionsController extends Controller
{
    public function create(Request $request) : Response
    {
        return Inertia::render('Collections/Create', [
            'folder' => $request->get('folder') ? (int) $request->get('folder') : null,
        ]);
    }

    public function store(Request $request) : RedirectResponse
    {
        $newUuid = Str::uuid();

        CollectionAggregateRoot::retrieve($newUuid)
            ->createCollection($request->all())
            ->persist();

        return back();
    }
}