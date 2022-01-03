<?php

namespace App\Http\Controllers;

use App\Domain\Cards\Actions\FormatCards;
use App\Domain\Cards\DataObjects\CardSearchData;
use App\Domain\Collections\Aggregate\Actions\SearchCollectionCards;
use App\Domain\Collections\Aggregate\DataObjects\CollectionCardSearchData;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CollectionsEditSearchController
{
    public function store(string $collection, Request $request, SearchCollectionCards $searchCollectionCards, FormatCards $formatCards) : JsonResponse
    {
        $searchData = new CollectionCardSearchData([
            'uuid'      => $collection,
            'search'    => new CardSearchData($request->all()),
        ]);

        $builder = $searchCollectionCards($searchData)->builder ?: [];

        if (!$builder) {
            return response()->json([]);
        }

        $cards = $formatCards($builder, $searchData);

        return response()->json($cards);
    }
}
