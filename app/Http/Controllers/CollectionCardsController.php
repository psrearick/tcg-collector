<?php

namespace App\Http\Controllers;

use App\Domain\Collections\Aggregate\Actions\UpdateCollectionCard;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CollectionCardsController extends Controller
{
    public function store(string $collection, Request $request, UpdateCollectionCard $updateCollectionCard) : Response
    {
        try {
            $newCard = $updateCollectionCard(['uuid' => $collection, 'change' => $request->all()]);
        } catch (Exception $e) {
            return $request->wantsJson()
                ? response()->json(['message' => 'failed to save card'], 500)
                : response('failed to save card', 500);
        }

        return $request->wantsJson()
                    ? new JsonResponse($newCard, 200)
                    : back()->with('status', $newCard);
    }
}
