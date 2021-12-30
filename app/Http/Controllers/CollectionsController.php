<?php

namespace App\Http\Controllers;

use App\Domain\Collections\Aggregate\Actions\CreateCollection;
use App\Domain\Collections\Aggregate\Actions\UpdateCollection;
use App\Domain\Folders\Aggregate\Queries\FolderChildren;
use App\Http\Controllers\Controller;
use GetCollection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CollectionsController extends Controller
{
    public function create(Request $request) : Response
    {
        return Inertia::render('Collections/Create', [
            'folder' => $request->get('folder') ?? null,
        ]);
    }

    public function destroy()
    {
    }

    public function edit(string $uuid, GetCollection $getCollection) : Response
    {
        $collection = $getCollection($uuid);

        return Inertia::render('Collections/Edit', ['collection' => $collection]);
    }

    public function index() : Response
    {
        $folderChildren = new FolderChildren('', auth()->id());

        return Inertia::render('Collections/Index', [
            'collections'   => $folderChildren->collections(),
            'folders'       => $folderChildren->folders(),
        ]);
    }

    public function show(string $uuid, GetCollection $getCollection) : Response
    {
        $collection = $getCollection($uuid);

        return Inertia::render('Collections/Show', ['collection' => $collection]);
    }

    public function store(Request $request, CreateCollection $createCollection) : RedirectResponse
    {
        $uuid = $createCollection($request->all());

        return redirect()->route('collections.show', $uuid);
    }

    public function update(Request $request, UpdateCollection $updateCollection) : RedirectResponse
    {
        $updateCollection($request->all());

        return back();
    }
}
