<?php

namespace App\Http\Controllers;

use App\Domain\Collections\Aggregate\Actions\CreateCollection;
use App\Domain\Collections\Aggregate\Actions\DeleteCollection;
use App\Domain\Collections\Aggregate\Actions\GetCollection;
use App\Domain\Collections\Aggregate\Actions\UpdateCollection;
use App\Domain\Collections\Aggregate\DataObjects\CollectionData;
use App\Domain\Folders\Aggregate\Actions\GetChildren;
use App\Domain\Prices\Aggregate\Actions\GetSummaryData;
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

    public function destroy(string $uuid, DeleteCollection $deleteCollection) : RedirectResponse
    {
        $deleteCollection($uuid);

        return redirect()->back();
    }

    public function edit(string $uuid, GetCollection $getCollection) : Response
    {
        return Inertia::render('Collections/Edit',
        [
            'collection' => $getCollection($uuid),
        ]);
    }

    public function index(GetSummaryData $getSummaryData, GetChildren $getChildren) : Response
    {
        $folderChildren = $getChildren(null, auth()->id());
        $collections    = $folderChildren['collections'];
        $folders        = $folderChildren['folders'];
        $summary        = $getSummaryData($collections, $folders, false);

        return Inertia::render('Collections/Index', [
            'collections'   => $collections,
            'folders'       => $folders,
            'totals'        => $summary,
        ]);
    }

    public function show(string $uuid) : Response
    {
        return Inertia::render('Collections/Show', [
            'collection' => new CollectionData(((new GetCollection)($uuid))->toArray()),
        ]);
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
