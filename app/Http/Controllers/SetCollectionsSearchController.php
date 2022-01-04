<?php

namespace App\Http\Controllers;

use App\Domain\Sets\Presenters\SetPresenter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SetCollectionsSearchController
{
    public function index(Request $request, SetPresenter $setPresenter) : JsonResponse
    {
        return response()->json($setPresenter->present($request->set));
    }
}
