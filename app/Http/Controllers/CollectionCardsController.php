<?php

namespace App\Http\Controllers;

use App\Domain\Collections\Aggregate\Actions\UpdateCollectionCard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CollectionCardsController extends Controller
{
    public function store(string $collection, Request $request, UpdateCollectionCard $updateCollectionCard) : Response
    {
        $newCard = $updateCollectionCard(['uuid' => $collection, 'change' => $request->all()]);

        return $request->wantsJson()
                    ? new JsonResponse($newCard, 200)
                    : back()->with('status', $newCard);
    }
}
