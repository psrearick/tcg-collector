<?php

namespace App\Http\Controllers;

use App\Domain\Cards\DataObjects\CardData;
use App\Domain\Cards\DataObjects\CardSearchData;
use App\Domain\Cards\Models\Card;
use App\Domain\Collections\Aggregate\Actions\FormatCollectionCards;
use App\Domain\Collections\Aggregate\Actions\GetAllCollectionCards;
use App\Domain\Collections\Aggregate\Actions\SearchAllCollectionCards;
use App\Domain\Collections\Aggregate\Actions\SearchCollection;
use App\Domain\Collections\Aggregate\DataObjects\CollectionCardSearchData;
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
