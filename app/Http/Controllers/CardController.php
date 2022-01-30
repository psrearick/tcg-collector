<?php

namespace App\Http\Controllers;

use App\Domain\Cards\Presenters\CardShowPresenter;
use Inertia\Inertia;
use Inertia\Response;

class CardController extends Controller
{
    public function show(string $uuid) : Response
    {
        return Inertia::render('Cards/Show', [
            'card' => (new CardShowPresenter($uuid))->present(),
        ]);
    }
}
