<?php

namespace App\Http\Controllers;

use App\Domain\Stores\Actions\CreateStore;
use App\Domain\Stores\Presenters\StoreShowPresenter;
use App\Http\Requests\StoreStoreRequest;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;

class StoresController extends Controller
{
    public function create() : Response
    {
        return Inertia::render('Stores/Create');
    }

    public function index(Request $request) : Response
    {
        return Inertia::render('Stores/Index',
            (new StoreShowPresenter($request->paginate))->present(),
        );
    }

    public function store(StoreStoreRequest $request, CreateStore $createStore) : RedirectResponse
    {
        $createStore($request->toArray());
        return redirect()->route('stores.index');
    }
}