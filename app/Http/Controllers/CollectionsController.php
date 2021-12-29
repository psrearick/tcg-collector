<?php

namespace App\Http\Controllers;

use App\Domain\Collections\Aggregate\Actions\CreateCollection;
use App\Http\Controllers\Controller;
use GetCollection;
use GetCollections;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CollectionsController extends Controller
{
    public function create(Request $request) : Response
    {
        return Inertia::render('Collections/Create', [
            'folder' => $request->get('folder') ? (int) $request->get('folder') : null,
        ]);
    }

    public function destroy()
    {}

    public function edit(string $uuid, GetCollection $getCollection) : Response
    {
        $collection = $getCollection($uuid);

        return Inertia::render('Collections/Edit', ['collection' => $collection]);
    }

    public function index(GetCollections $getCollections) : Response
    {
        return Inertia::render('Collections/Index', ['collections' => $getCollections()]);
    }

    public function store(Request $request, CreateCollection $createCollection) : RedirectResponse
    {
        $uuid = $createCollection($request->all());

        return redirect()->route('collections.edit', $uuid);
    }

    public function update()
    {}
}
