<?php

namespace App\Http\Controllers;

use App\Domain\Collections\Aggregate\Actions\CreateCollection;
use App\Domain\Collections\Aggregate\Actions\UpdateCollection;
use App\Domain\Collections\Presenters\CollectionsPresenter;
use App\Domain\Folders\Aggregate\Queries\FolderChildren;
use App\Domain\Prices\Aggregate\Actions\GetSummaryData;
use App\Http\Controllers\Controller;
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

    public function edit(string $uuid, Request $request) : Response
    {
        $collections = (new CollectionsPresenter($request->all(), $uuid))->present();

        return Inertia::render('Collections/Edit',
        [
            'collection'    => $collections['collection'],
            'list'          => $collections['list'],
            'search'        => $collections['search'],
        ]);
    }

    public function index() : Response
    {
        $folderChildren = new FolderChildren('', auth()->id());
        $collections    = $folderChildren->collections();
        $folders        = $folderChildren->folders();
        $summary        = (new GetSummaryData)($collections, $folders);

        return Inertia::render('Collections/Index', [
            'collections'   => $collections,
            'folders'       => $folders,
            'totals'        => $summary,
        ]);
    }

    public function show(
        string $uuid,
        Request $request,
        GetSummaryData $getSummaryData,
    ) : Response {
        $collections     = (new CollectionsPresenter($request->all(), $uuid))->present();
        $summary         = $getSummaryData([$collections['collection']->toArray()]);

        return Inertia::render('Collections/Show', [
            'totals'        => $summary,
            'collection'    => $collections['collection'],
            'list'          => $collections['list'],
            'search'        => $collections['search'],
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
