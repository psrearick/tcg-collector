<?php

namespace App\Http\Controllers;

use App\Domain\Cards\DataObjects\CardSearchData;
use App\Domain\Collections\Aggregate\Actions\CreateCollection;
use App\Domain\Collections\Aggregate\Actions\SearchCollection;
use App\Domain\Collections\Aggregate\Actions\UpdateCollection;
use App\Domain\Collections\Aggregate\DataObjects\CollectionCardSearchData;
use App\Domain\Collections\Aggregate\Queries\CollectionCardsSummary;
use App\Domain\Folders\Aggregate\Queries\FolderChildren;
use App\Domain\Prices\Aggregate\Actions\GetSummaryData;
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
        GetCollection $getCollection,
        GetSummaryData $getSummaryData,
        SearchCollection $searchCollection
    ) : Response {
        $collection      = $getCollection($uuid);
        $summary         = $getSummaryData([$collection->toArray()]);
        $collectionCards = (new CollectionCardsSummary($uuid))->list();

        $searchData = new CollectionCardSearchData([
            'data'      => $collectionCards,
            'uuid'      => $uuid,
            'search'    => new CardSearchData($request->all()),
        ]);

        $searchCollection = $searchCollection($searchData)->collection ?: null;

        if ($searchCollection && $searchData->search->paginator) {
            $page = $searchData->search->paginator;
            $list = $searchCollection->paginate(
                $page['per_page'],
                $page['total'],
                $page['current_page'],
            );
        } elseif ($searchCollection) {
            $list = $searchCollection->paginate(25);
        } else {
            $list = [];
        }

        return Inertia::render('Collections/Show', [
            'collection'    => $collection,
            'totals'        => $summary,
            'list'          => $list,
            'search'        => $searchData->search,
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
