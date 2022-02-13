<?php

namespace App\Http\Controllers;

use App\Domain\Cards\Actions\FormatCardsWithPagination;
use App\Domain\Cards\DataObjects\CardSearchData;
use App\Domain\Collections\Aggregate\Actions\SearchCollectionCards;
use App\Domain\Collections\Aggregate\DataObjects\CollectionCardSearchParameterData;
use App\Domain\Collections\Presenters\SetCollectionsPresenter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SetCollectionsController extends Controller
{
    public function edit(string $collection, Request $request, SearchCollectionCards $searchCollectionCards, FormatCardsWithPagination $formatCards) : JsonResponse
    {
        $search  = [
            'set_id'    => $request->get('set'),
            'card'      => $request->get('cardSearch'),
            'sort'      => [
                'collectorNumber' => 'asc',
            ],
        ];

        $searchData = new CollectionCardSearchParameterData([
            'uuid'      => $collection,
            'search'    => new CardSearchData($search),
        ]);

        $builder = $searchCollectionCards($searchData)->builder ?: [];

        if (!$builder) {
            return response()->json([]);
        }

        $cards = $formatCards($builder, $searchData);

        return response()->json($cards);
    }

    public function show(string $collection) : Response
    {
        return Inertia::render('Collections/AddFromSet', (new SetCollectionsPresenter($collection))->present());
    }
}
