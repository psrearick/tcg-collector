<?php

namespace App\Http\Controllers;

use App\Actions\UpdateSettings;
use App\Domain\Collections\Aggregate\Actions\RecalculateCollectionCardSummaries;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class SettingsController
{
    public function show()
    {
        return Inertia::render('Settings/Show');
    }

    public function update(Request $request) : Response
    {
        (new RecalculateCollectionCardSummaries)($request->all());
        (new UpdateSettings)($request->all());

        return $request->wantsJson()
                    ? new JsonResponse('', 200)
                    : back()->with('status', 'settings-updated');
    }
}
