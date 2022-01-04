<?php

namespace App\Http\Controllers;

use App\Domain\Collections\Aggregate\Actions\MoveCollectionCards;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CollectionCardsMoveController
{
    public function update(string $collection, Request $request, MoveCollectionCards $moveCollectionCards) : JsonResponse
    {
        $moveCollectionCards($collection, $request->get('collection'), $request->get('items'));

        return response()->json(['success' => true]);
    }
}
