<?php

namespace App\Http\Controllers;

use App\Domain\Cards\DataObjects\CardSearchData;
use App\Domain\Collections\Aggregate\Actions\SearchCollectionCards;
use App\Domain\Collections\Aggregate\DataObjects\CollectionCardSearchData;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CollectionsEditSearchController
{
    public function store(string $collection, Request $request, SearchCollectionCards $searchCollectionCards) : JsonResponse
    {
        $searchData = new CollectionCardSearchData([
            'uuid'      => $collection,
            'search'    => new CardSearchData($request->all()),
        ]);

        $results = $searchCollectionCards($searchData);

        return response()->json([]);
    }
}
