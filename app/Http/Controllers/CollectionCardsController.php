<?php

namespace App\Http\Controllers;

use App\Domain\Cards\Models\Card;
use App\Domain\Collections\Aggregate\Actions\UpdateCollectionCard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CollectionCardsController extends Controller
{
    public function store(string $collection, Request $request, UpdateCollectionCard $updateCollectionCard) : JsonResponse
    {
        $newCard = $updateCollectionCard(['uuid' => $collection, 'change' => $request->all()]);

        return response()->json($newCard);
    }
}
