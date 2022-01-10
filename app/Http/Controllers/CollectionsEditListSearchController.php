<?php

namespace App\Http\Controllers;

use App\Domain\Collections\Presenters\CollectionsPresenter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CollectionsEditListSearchController
{
    public function store(string $uuid, Request $request) : JsonResponse
    {
        $collections = (new CollectionsPresenter($request->all(), $uuid))->present();

        return response()->json(
        [
            'collection'    => $collections['collection'],
            'list'          => $collections['list'],
            'search'        => $collections['search'],
            'totals'        => $collections['summary'],
        ]);
    }
}
