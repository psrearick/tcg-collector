<?php

namespace App\Http\Controllers;

use App\Domain\Cards\DataObjects\CardSearchData;
use App\Domain\Collections\Aggregate\Actions\SearchCollectionCards;
use App\Domain\Collections\Aggregate\DataObjects\CollectionCardSearchData;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Domain\Cards\Actions\FormatCards;

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

        $formatCards    = new FormatCards;
        $cards          = $formatCards($builder);

        if ($searchData->search->paginator) {
            $page = $searchData->search->paginator;
            return response()->json($cards->paginate(
                $page['per_page'],
                $page['total'],
                $page['current_page']
            ));
        }
        return response()->json($cards->paginate(35));
    }
}
