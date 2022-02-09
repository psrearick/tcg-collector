<?php

namespace App\Http\Controllers;

use App\Domain\Cards\DataObjects\CardSearchData;
use App\Domain\Cards\Presenters\CardSearchPresenter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CardsSearchController extends Controller
{
    public function store(Request $request) : JsonResponse
    {
        $searchData = new CardSearchData($request->all());

        if (count($searchData->sort) === 0) {
            $searchData->sort = [
                'name'          => 'asc',
                'releaseDate'   => 'desc',
            ];
        }

        $cards = (new CardSearchPresenter($searchData))->present();

        return response()->json($cards);
    }
}
