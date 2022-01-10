<?php

namespace App\Http\Controllers;

use App\Domain\Collections\Aggregate\Actions\SearchAllCollectionCards;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CollectionsSearchController
{
    public function show()
    {
        return Inertia::render('Search/Show');
    }

    public function store(Request $request, SearchAllCollectionCards $searchAllCollectionCards) : JsonResponse
    {
        $cards = $searchAllCollectionCards($request->all());

        return response()->json($cards);
    }
}
