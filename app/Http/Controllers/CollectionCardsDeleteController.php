<?php

namespace App\Http\Controllers;

use App\Domain\Collections\Aggregate\Actions\DeleteCollectionCards;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CollectionCardsDeleteController
{
    public function update(string $collection, Request $request, DeleteCollectionCards $deleteCollectionCards) : JsonResponse
    {
        $deleteCollectionCards($collection, $request->get('items'));

        return response()->json(['success' => true]);
    }
}
