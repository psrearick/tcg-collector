<?php

namespace App\Http\Controllers;

use App\Domain\Collections\Aggregate\Actions\SearchAllCollectionCards;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class GroupsSearchController
{
    public function show()
    {
        return Inertia::render('Groups/Search');
    }

    public function store(Request $request, SearchAllCollectionCards $searchAllCollectionCards) : JsonResponse
    {
        $request->merge(['inGroup' => true]);
        $cards = $searchAllCollectionCards($request->all());

        return response()->json($cards);
    }
}
