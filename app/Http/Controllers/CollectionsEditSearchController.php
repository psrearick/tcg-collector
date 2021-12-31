<?php

namespace App\Http\Controllers;

use App\Domain\Cards\Actions\FormatCollectionCards;
use App\Domain\Cards\DataObjects\CardSearchData;
use App\Domain\Collections\Aggregate\Actions\SearchCollectionCards;
use App\Domain\Collections\Aggregate\DataObjects\CollectionCardSearchData;
use App\Support\Collection;
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

        $builder = $searchCollectionCards($searchData)->builder ?: [];

        if (!$builder) {
            return response()->json([]);
        }

        $formatCards    = new FormatCollectionCards;
        $cards          = $formatCards($builder, 25, $searchData);

        return response()->json($cards);
        // return response()->json(['cards' => $cards]);
    }
}
